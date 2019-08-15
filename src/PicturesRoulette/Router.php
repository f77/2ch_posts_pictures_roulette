<?php

namespace PicturesRoulette;

use PicturesRoulette\ImageboardLoaders\ImageboardLoaderFactory;
use PicturesRoulette\Template\Template;
use PicturesRoulette\Utils\FileUtils;
use PicturesRoulette\Utils\ImageUtils;
use PicturesRoulette\Utils\LoaderUtils;
use PicturesRoulette\Utils\StringUtils;
use PicturesRoulette\Utils\Timer;

/**
 * Простой класс роутинга и вызова действий с обработкой ошибок.
 */
class Router
{
    use StringUtils;
    use FileUtils;
    use LoaderUtils;
    use ImageUtils;

    public const ACTION_PARAM_NAME = 'action';
    public const TIMER_KEY_ALL_TIME = 'all_time';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Timer
     */
    protected $timer;

    /**
     * @var Template
     */
    protected $template;

    /**
     * @var Post\ImagePostsArray
     */
    protected $currentPosts;

    /**
     * Router constructor.
     *
     * @param Config $_config
     *
     * @throws Template\Exceptions\DescriptionParsingErrorException
     * @throws Template\Exceptions\FileNotExistsException
     */
    public function __construct(Config $_config)
    {
        $this->config = $_config;
        $this->timer = new Timer();
        $this->template = new Template(Config::PATH_TEMPLATES . '/' . $this->config->getTemplateName());

        // Загрузим текущие посты из папки.
        $this->currentPosts = $this->getCurrentComboPosts(Config::PATH_IMAGES_TEMP, ...[
            Config::FILENAME_GITIGNORE,
            Config::FILENAME_KEEP,
        ]);
    }

    public function start(): void
    {
        $this->timer->start(self::TIMER_KEY_ALL_TIME);
        $this->callAction();
    }

    //-------------------------------------------------------------------------
    // Действия.
    //-------------------------------------------------------------------------

    /**
     * @return string
     */
    protected function getDefaultAction(): string
    {
        return 'index';
    }

    protected function actionIndex(): void
    {
        echo '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body {
      margin: auto;
      background: #20262e;
      color: #cfd0d2;
    }
    a {
      text-decoration: none;
      color: lightgreen;
      display: block;
      margin: 3px;
      padding: 3px;
      border: 2px solid darkgray;
      float: left;
    }
</style>
<title>Выберите действие</title>
</head>

<body>
    <h1>
    2ch Posts Pictures Roulette
    </h1>

    <table>
    <tr>
      <td>✔</td>
      <td>Imageboard: </td>
      <td>' . $this->config->getImageboardName() . '</td>
    </tr>
    <tr>
      <td>✔</td>
      <td>Board: </td>
      <td>' . $this->config->getBoard() . '</td>
    </tr>
    <tr>
      <td>✔</td>
      <td>Thread Number: </td>
      <td>' . $this->config->getThreadNumber() . '</td>
    </tr>
    <tr>
      <td>✔</td>
      <td>Template: </td>
      <td>' . $this->template->getDescription() . '</td>
    </tr>
    <tr>
      <td>✔</td>
      <td>Template Version: </td>
      <td>' . $this->template->getVersion() . '</td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td><br></td>
    </tr>
    <tr>
      <td>✔</td>
      <td>PHP Memory Limit: </td>
      <td>' . \ini_get('memory_limit') . '</td>
    </tr>
    <tr>
      <td>✔</td>
      <td>PHP Time Limit: </td>
      <td>' . \ini_get('max_execution_time') . '</td>
    </tr>
    </table><br>

    <a href="?action=LoadNewPosts" target="_blank" style="">Загрузить новые посты</a>
    <a href="?action=FillImage" target="_blank" style="">Заполнить изображение</a><br>
</body>
</html>
';
    }

    /**
     * @throws ImageboardLoaders\Exceptions\UnknownImageboardLoaderException
     */
    protected function actionLoadNewPosts(): void
    {
        // Создадим класс лоадера на фабрике.
        $factory = new ImageboardLoaderFactory();
        $loader = $factory->getImageboardLoader($this->config->getImageboardName(), $this->config->getBoard(),
            $this->config->getThreadNumber(), ...$this->config->getPostsExclude());

        // Загрузим НОВЫЕ посты с изображениями.
        $newPosts = $loader->getNewPostsWithImage($this->currentPosts, $this->template,
            $this->config->getMagnetRadius());

        // Скачаем их пикчи в папку.
        echo "New Posts:<br>\n";
        $i = 0;
        foreach ($newPosts->getAll() as $post) {
            // Если с текущим магнитным комбо есть новый пост, то старый удаляем.
            $oldPost = $this->currentPosts->getByMagnetCombo($post->getMagnetCombo());
            if ($oldPost !== null) {
                \unlink($oldPost->getImageUrl());
            }

            ++$i;
            echo $i . '/' . $newPosts->getSize()
                . ') Loading <b>' . $post->getMagnetCombo() . '</b> (' . $post->getCombo() . ')...';

            $this->downloadPost($post, $loader, Config::PATH_IMAGES_TEMP);
            echo " OK.<br>\n";
        }

        echo '<br>' . $this->getGen();
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function actionFillImage(): void
    {
        echo "Process images...<br>\n";

        // Задание параметров
        $imgInBase = $this->imagecreatefromfile($this->template->getImageFilename());

        // Создадим изображение такого же размера, как исходное.
        [$widthInBase, $heightInBase] = \getimagesize($this->template->getImageFilename());
        $out = \imagecreatetruecolor($widthInBase, $heightInBase);

        // Копируем. Очередность можно чередовать. Чтобы управлять слоями изображения.
        // Обходим массив координат и вставляем картинки.
        $i = 0;
        foreach ($this->template->getCoordinatesCells() as $comb => $value) {
            $post = $this->currentPosts->getByMagnetCombo($comb);
            if ($post === null) {
                continue;
            }

            try {
                ++$i;
                echo $i . '/' . $this->currentPosts->getSize()
                    . ') <b>' . $post->getMagnetCombo() . '</b> (' . $post->getCombo() . ')...';

                $fileInTarget = $post->getImageUrl();
                $imgInTarget = $this->imagecreatefromfile($fileInTarget);
                [$widthInTarget, $heightInTarget] = \getimagesize($fileInTarget);
                \imagecopyresampled($out, $imgInTarget, $value[0], $value[1], 0, 0, $this->template->getCellSize()[0],
                    $this->template->getCellSize()[1], $widthInTarget, $heightInTarget);

                // Накладываем иконку легитимности.
                $iconFile = ($post->getMagnetCombo() == $post->getCombo() ? Config::FILENAME_ICON_CHECKED
                    : Config::FILENAME_ICON_WARNING);
                $this->fillIcon($out, $iconFile, $value[0], $value[1], 48, 15);

                echo " OK.<br>\n";
            } catch (\Throwable $t) {
                echo 'Ошибка во время обработки поста "' . $post->getImageUrl() . '":' . "\n"
                    . 'Message:         "' . $t->getMessage() . '".' . "\n"
                    . 'Code:            "' . $t->getCode() . '".' . "<br>\n";
            }
        }

        // Накладываем шаблон.
        echo "<br>Generating result image...<br>\n";
        \imagecopyresampled($out, $imgInBase, 0, 0, 0, 0, $widthInBase, $heightInBase, $widthInBase, $heightInBase);

        // Сохраняем.
        \imagejpeg($out, Config::PATH_IMAGES_RESULT . '/out_'
            . $this->config->getTemplateName()
            . '_' . $this->template->getVersion()
            . '.jpg', (int)$this->config->getOutJpgQuality());

        // Выводим генерацию.
        echo 'OK.<br><br>' . $this->getGen();
    }

    /**
     * @param        $_out_image_resource
     * @param string $_file_icon
     * @param int    $_cell_x
     * @param int    $_cell_y
     * @param int    $_size_icon
     * @param int    $_margin_icon
     *
     * @throws \InvalidArgumentException
     */
    protected function fillIcon(
        $_out_image_resource,
        string $_file_icon,
        int $_cell_x,
        int $_cell_y,
        int $_size_icon,
        int $_margin_icon
    ): void {
        $imgIcon = $this->imagecreatefromfile($_file_icon);
        [$widthIcon, $heightIcon] = \getimagesize($_file_icon);
        \imagecopyresampled($_out_image_resource, $imgIcon, $_cell_x + $_margin_icon, $_cell_y + $_margin_icon, 0, 0,
            $_size_icon, $_size_icon, $widthIcon, $heightIcon);

    }

    //-------------------------------------------------------------------------
    // Системные методы.
    //-------------------------------------------------------------------------
    /**
     * @return string
     */
    protected function getGen(): string
    {
        $this->timer->stop(self::TIMER_KEY_ALL_TIME);
        return 'Time: ' . $this->timer->getFormatted(self::TIMER_KEY_ALL_TIME) . ', Memory: '
            . $this->getHumanReadableFileSize(\memory_get_usage())
            . ' (' . $this->getHumanReadableFileSize(\memory_get_usage(true)) . ').';
    }

    /**
     * @param string $_error
     */
    protected function showError(string $_error): void
    {
        echo $_error;
    }

    protected function callAction(): void
    {
        $action = \filter_input(\INPUT_GET, self::ACTION_PARAM_NAME);
        if ($action === false) {
            // Так как фильтр не задан, эта секция никогда не должна вызываться.
            $this->showError('Ошибка фильтрации GET-параметра "' . self::ACTION_PARAM_NAME . '"!');
            return;
        }

        if ($action === null || empty($action)) {
            $action = $this->getDefaultAction();
        }

        // Вызываем метод дейсвтия.
        $actionMethod = 'action' . \ucfirst($action);
        if (!\method_exists($this, $actionMethod)) {
            $this->showError('Ошибка! Действие "' . $action . '" не существует!');
        } else {
            \call_user_func_array([$this, $actionMethod], []);
        }
    }
}
