<?php

namespace PicturesRoulette\Utils;

use PicturesRoulette\Post\ImagePostsArray;
use PicturesRoulette\Post\ImagePost;
use PicturesRoulette\ImageboardLoaders\ImageboardLoaderInterface;

trait LoaderUtils
{
    /**
     * @param string   $_temp_dir
     * @param string[] $_files_exclude
     *
     * @return ImagePostsArray
     */
    protected function getCurrentComboPosts(string $_temp_dir, string... $_files_exclude): ImagePostsArray
    {
        $result = [];
        $files = $this->getDirectoryFilesRecursive($_temp_dir);
        foreach ($files as $file) {
            if (\in_array(\basename($file), $_files_exclude)) {
                continue;
            }

            $parsed = $this->parseCurrentComboFilename($file);
            $result[] = new ImagePost ((int)$parsed[2], $file, \pathinfo($file, \PATHINFO_EXTENSION), $parsed[1],
                $parsed[0]);
        }

        $imagePostsArray = new ImagePostsArray();
        $imagePostsArray->fill(...$result);
        return $imagePostsArray;
    }

    /**
     * @param string $_filename
     *
     * @return array
     */
    protected function parseCurrentComboFilename(string $_filename): array
    {
        return \explode('.', \basename($_filename));
    }

    /**
     * @param ImagePost                 $_post
     * @param ImageboardLoaderInterface $_loader
     * @param string                    $_download_dir
     */
    protected function downloadPost(ImagePost $_post, ImageboardLoaderInterface $_loader, string $_download_dir): void
    {
        $_loader->downloadPostImage($_post, $_download_dir . '/'
            . $_post->getMagnetCombo()
            . '.' . $_post->getCombo()
            . '.' . $_post->getPostNumber()
            . '.' . $_post->getImageUrlExtension());
    }
}
