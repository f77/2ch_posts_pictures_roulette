<?php

namespace PicturesRoulette\ImageboardLoaders\Dvach;

use PicturesRoulette\Template\Template;
use PicturesRoulette\ImageboardLoaders\ImageboardLoaderInterface;
use PicturesRoulette\Utils\CurlUtils;
use PicturesRoulette\Utils\StringUtils;
use PicturesRoulette\Post\ImagePostsArray;
use PicturesRoulette\Post\ImagePost;
use PicturesRoulette\ImageboardLoaders\Dvach\Exceptions\ThreadParsingErrorException;

class DvachLoader implements ImageboardLoaderInterface
{

    use StringUtils;
    use CurlUtils;

    protected $board;
    protected $threadNumber;
    protected $postsExclude;



    public static function getImageboardName (): string
    {
        return '2ch.hk';
    }

    public static function getAcceptedImageExtensions (): array
    {
        return ['jpg', 'jpeg', 'png', 'gif'];
    }

    public function __construct (string $_board, int $_thread_number, int... $_posts_exclude)
    {
        $this->board        = $_board;
        $this->threadNumber = $_thread_number;
        $this->postsExclude = $_posts_exclude;
    }

    public function getAllPoststWithImage (): ImagePostsArray
    {
        $result    = [];
        $threadArr = $this->getThreadArr ();

        foreach ($threadArr['threads'][0]['posts'] as $index => $post)
        {
            // Получим ссылку на изображение или NULL, если файла нет.
            $filepath = $post['files'][0]['path'] ?? NULL;

            // Если это оп-пост, пропускаем.
            if ($index == 0)
            {
                continue;
            }

            // Обрабатываем только те посты, где есть файлы.
            if ($filepath === NULL || !\in_array ($this->getExtensionFromPath ($filepath), $this->getAcceptedImageExtensions ()))
            {
                continue;
            }

            // Некоторые посты следует сразу же исключить.
            if (\in_array ($post['num'], $this->postsExclude))
            {
                continue;
            }

            //----------------------
            $result[] = new ImagePost ($post['num'], 'http://2ch.hk' . $filepath, $this->getExtensionFromPath ($filepath));
        }

        $imagePostsArray = new ImagePostsArray ();
        $imagePostsArray->fill (...$result);
        return $imagePostsArray;
    }

    public function getNewPoststWithImage (ImagePostsArray $_current_posts, Template $_template): ImagePostsArray
    {
        $resultImagePostsArray = new ImagePostsArray();
        $allPostsArr           = $this->getAllPoststWithImage ()->getAll ();

        foreach ($allPostsArr as $post)
        {
            // Пытаемся найти комбо, начиная с самых крупных.
            for ($i = \strlen ((string) $post->getPostNumber ()); $i > 0; $i--)
            {
                // Получаем последние несколько цифр номера поста.
                $combo = $this->getLastNSymbolsFromPostNumber ($post->getPostNumber (), $i);

                // Если такого комбо в шаблоне вообще нет.
                if (empty ($_template->getCellByCombo ($combo)))
                {
                    continue;
                }
                // Если такое комбо или номер поста уже есть в текущих постах.
                if ($_current_posts->getByComboOrPostNumber ($combo, $post->getPostNumber ()) !== NULL)
                {
                    continue;
                }
                // Если такое комбо или номер поста уже есть в обнаруженных прямо сейчас постах.
                if ($resultImagePostsArray->getByComboOrPostNumber ($combo, $post->getPostNumber ()) !== NULL)
                {
                    continue;
                }

                // Если все гуд, добавляем пост.
                $resultImagePostsArray->add (new ImagePost ($post->getPostNumber (), $post->getImageUrl (), $post->getImageUrlExtension (), $combo));
            }
        }

        return $resultImagePostsArray;
    }

    public function downloadPostImage (ImagePost $_image_post, string $_output_filename)
    {
        $this->curlDownloadFile ($_image_post->getImageUrl (), $_output_filename);
    }

    protected function getThreadArr (): array
    {
        $threadParsed = \json_decode ($this->curlQuery ('https://2ch.hk/' . $this->board . '/res/' . $this->threadNumber . '.json'), TRUE);

        if ($threadParsed === NULL)
        {
            throw new ThreadParsingErrorException ("Ошибка декодирования треда из json! Возможно, он просто не существует.");
        }

        return $threadParsed;
    }

}
