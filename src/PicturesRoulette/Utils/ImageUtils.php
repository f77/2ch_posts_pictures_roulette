<?php

namespace PicturesRoulette\Utils;

trait ImageUtils
{

    /**
     * Открыть изображение любого типа.
     * http://stackoverflow.com/questions/10233577/create-image-from-url-any-file-type
     */
    function imagecreatefromfile ($_filename)
    {
        if (!\file_exists ($_filename))
        {
            throw new \InvalidArgumentException ('File "' . $_filename . '" not found.');
        }
        switch (\strtolower (\pathinfo ($_filename, \PATHINFO_EXTENSION)))
        {
            case 'jpg':
            case 'jpeg':
                return \imagecreatefromjpeg ($_filename);

            case 'png':
                return \imagecreatefrompng ($_filename);

            case 'gif':
                return \imagecreatefromgif ($_filename);

            default:
                throw new \InvalidArgumentException ('File "' . $_filename . '" is not valid jpg, png or gif image.');
        }
    }

}
