<?php

namespace PicturesRoulette\ImageboardLoaders\Dvach;

use PicturesRoulette\ImageboardLoaders\Dvach\Exceptions\ThreadParsingErrorException;
use PicturesRoulette\ImageboardLoaders\ImageboardLoaderInterface;
use PicturesRoulette\Post\ImagePost;
use PicturesRoulette\Post\ImagePostsArray;
use PicturesRoulette\Template\Template;
use PicturesRoulette\Utils\CurlUtils;
use PicturesRoulette\Utils\StringUtils;

class DvachLoader implements ImageboardLoaderInterface
{
    use StringUtils;
    use CurlUtils;

    /**
     * @var string
     */
    protected $board;

    /**
     * @var int
     */
    protected $threadNumber;

    /**
     * @var int[]
     */
    protected $postsExclude;


    /**
     * @return string
     */
    public static function getImageboardName(): string
    {
        return '2ch.hk';
    }

    /**
     * @return array
     */
    public static function getAcceptedImageExtensions(): array
    {
        return ['jpg', 'jpeg', 'png', 'gif'];
    }

    /**
     * DvachLoader constructor.
     *
     * @param string $_board
     * @param int    $_thread_number
     * @param int[]  $_posts_exclude
     */
    public function __construct(string $_board, int $_thread_number, int... $_posts_exclude)
    {
        $this->board = $_board;
        $this->threadNumber = $_thread_number;
        $this->postsExclude = $_posts_exclude;
    }

    /**
     * @return ImagePostsArray
     * @throws ThreadParsingErrorException
     */
    public function getAllPostsWithImage(): ImagePostsArray
    {
        $result = [];
        $threadArr = $this->getThreadArr();

        foreach ($threadArr['threads'][0]['posts'] as $index => $post) {
            // Получим ссылку на изображение или NULL, если файла нет.
            $filepath = $post['files'][0]['path'] ?? null;

            // Если это оп-пост, пропускаем.
            if ((int)$index === 0) {
                continue;
            }

            // Обрабатываем только те посты, где есть файлы.
            if ($filepath === null
                || !\in_array($this->getExtensionFromPath($filepath),
                    self::getAcceptedImageExtensions(), true)
            ) {
                continue;
            }

            // Некоторые посты следует сразу же исключить.
            if (\in_array($post['num'], $this->postsExclude, true)) {
                continue;
            }

            //----------------------
            $result[] = new ImagePost ($post['num'], 'http://2ch.hk' . $filepath,
                $this->getExtensionFromPath($filepath));
        }

        $imagePostsArray = new ImagePostsArray ();
        $imagePostsArray->fill(...$result);
        return $imagePostsArray;
    }


    /**
     * @param ImagePostsArray $_current_posts
     * @param Template        $_template
     * @param int             $_magnet_radius
     *
     * @return ImagePostsArray
     * @throws ThreadParsingErrorException
     */
    public function getNewPostsWithImage(
        ImagePostsArray $_current_posts,
        Template $_template,
        int $_magnet_radius
    ): ImagePostsArray {
        $resultImagePostsArray = new ImagePostsArray();
        $allPostsArr = $this->getAllPostsWithImage()->getAll();

        foreach ($allPostsArr as $post) {
            // Пытаемся найти комбо, начиная с самых крупных.
            for ($i = \strlen((string)$post->getPostNumber()); $i > 0; $i--) {
                // Получаем последние несколько цифр номера поста.
//                $realCombo = $this->getLastNSymbolsFromPostNumber ($post->getPostNumber (), $i);
                $realComboString = $this->getLastNSymbolsFromString($post->getPostNumber(), $i);

                // Обходим радиус магнита, все больше удаляясь по сторонам от фактического комбо.
                for ($currentRadius = 0; $currentRadius <= $_magnet_radius; $currentRadius++) {
                    foreach ([-1, 1] as $direction) {

                        //$magnetCombo = $realCombo + ($currentRadius * $direction);
                        $magnetComboString = $this->getLastNSymbolsFromString($post->getPostNumber()
                            + ($currentRadius * $direction), $i);

                        // Если такого комбо в шаблоне вообще нет.
                        if (empty ($_template->getCellByCombo($magnetComboString))) {
                            continue;
                        }

                        // Если такой номер поста уже задейстован.
                        if ($_current_posts->getByPostNumber($post->getPostNumber()) !== null
                            || $resultImagePostsArray->getByPostNumber($post->getPostNumber()) !== null
                        ) {
                            continue;
                        }

                        // Если текущее магнитное комбо есть в имеющихся файлах,
                        // И комбо-разница текущего поста не больше чем того, то продолжаем.
                        $oldCurrentPostIndex = $_current_posts->getIndexByMagnetCombo($magnetComboString);
                        if ($oldCurrentPostIndex !== null) {
                            $oldCurrentPost = $_current_posts->getByIndex($oldCurrentPostIndex);
                            if ($oldCurrentPost->getComboDiff() <= $currentRadius) {
                                continue;
                            }
                        }

                        // Если текущее магнитное комбо уже есть в текущих постах,
                        // Вычисляем его близость к реальному и заменяем, если надо.
                        $oldResultPostIndex = $resultImagePostsArray->getIndexByMagnetCombo($magnetComboString);
                        if ($oldResultPostIndex !== null) {
                            $oldResultPost = $resultImagePostsArray->getByIndex($oldResultPostIndex);
                            if ($post->getComboDiff() < $oldResultPost->getComboDiff()) {
                                // Если у текущего поста комбо меньше расходится с реальным, чем у старого
                                // То удаляем старый.
                                $resultImagePostsArray->deleteByIndex($oldResultPostIndex);
                            } else {
                                // А если близость больше, то продолжаем.
                                continue;
                            }
                        }

                        // Если все гуд, добавляем пост.
                        $resultImagePostsArray->add(new ImagePost ($post->getPostNumber(), $post->getImageUrl(),
                            $post->getImageUrlExtension(), $realComboString, $magnetComboString));

                        // Выходим из всех циклов.
                        break 3;
                    }
                }
            }
        }

        return $resultImagePostsArray;
    }

    /**
     * @param ImagePost $_image_post
     * @param string    $_output_filename
     */
    public function downloadPostImage(ImagePost $_image_post, string $_output_filename): void
    {
        $this->curlDownloadFile($_image_post->getImageUrl(), $_output_filename);
    }

    /**
     * @return array
     * @throws ThreadParsingErrorException
     */
    protected function getThreadArr(): array
    {
        $json = $this->curlQuery('https://2ch.hk/' . $this->board . '/res/' . $this->threadNumber . '.json');
        $threadParsed = \json_decode($json, true);

        if ($threadParsed === null) {
            throw new ThreadParsingErrorException ('Ошибка декодирования треда из json!'
                . ' Возможно, он просто не существует.');
        }

        return $threadParsed;
    }

}
