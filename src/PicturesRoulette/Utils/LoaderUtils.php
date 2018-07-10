<?php

namespace PicturesRoulette\Utils;

use PicturesRoulette\Post\ImagePostsArray;
use PicturesRoulette\Post\ImagePost;
use PicturesRoulette\ImageboardLoaders\ImageboardLoaderInterface;

trait LoaderUtils
{

    protected function getCurrentComboPosts (string $_temp_dir, string... $_files_exclude): ImagePostsArray
    {
        $result = [];
        $files  = $this->getDirectoryFilesRecursive ($_temp_dir);
        foreach ($files as $file)
        {
            if (\in_array (\basename ($file), $_files_exclude))
            {
                continue;
            }

            $parsed   = $this->parseCurrentComboFilename ($file);
            $result[] = new ImagePost ((int) $parsed[1], $file, \pathinfo ($file, \PATHINFO_EXTENSION), (int) $parsed[0]);
        }

        $imagePostsArray = new ImagePostsArray();
        $imagePostsArray->fill (...$result);
        return $imagePostsArray;
    }

    protected function parseCurrentComboFilename (string $_filename): array
    {
        return \explode ('.', \basename ($_filename));
    }

    protected function downloadPost (ImagePost $_post, ImageboardLoaderInterface $_loader, string $_download_dir)
    {
        $_loader->downloadPostImage ($_post, $_download_dir . '/'
                . $_post->getCombo ()
                . '.' . $_post->getPostNumber ()
                . '.' . $_post->getImageUrlExtension ());
    }

}
