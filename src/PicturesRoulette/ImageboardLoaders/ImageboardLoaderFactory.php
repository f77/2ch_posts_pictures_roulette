<?php

namespace PicturesRoulette\ImageboardLoaders;

use PicturesRoulette\ImageboardLoaders\Exceptions\UnknownImageboardLoaderException;
use PicturesRoulette\ImageboardLoaders\Dvach\DvachLoader;

class ImageboardLoaderFactory
{
    /**
     * @param string $imageboardName
     * @param string $_board
     * @param int    $_thread_number
     * @param int    ...$_posts_exclude
     *
     * @return ImageboardLoaderInterface
     * @throws UnknownImageboardLoaderException
     */
    public function getImageboardLoader(
        string $imageboardName,
        string $_board,
        int $_thread_number,
        int... $_posts_exclude
    ): ImageboardLoaderInterface {
        switch ($imageboardName) {
            case DvachLoader::getImageboardName():
                return new DvachLoader ($_board, $_thread_number, ...$_posts_exclude);

            default:
                throw new UnknownImageboardLoaderException ('Неизвестная имиджборда "' . $imageboardName . '"!');
        }
    }

}
