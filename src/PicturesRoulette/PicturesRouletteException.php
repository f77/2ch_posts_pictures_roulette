<?php

namespace PicturesRoulette;

/**
 * Базовый класс исключений для приложения.
 */
class PicturesRouletteException extends \Exception
{
    /**
     * PicturesRouletteException constructor.
     *
     * @param                 $_message
     * @param int             $_code
     * @param \Exception|null $_previous
     */
    public function __construct($_message, $_code = 0, \Exception $_previous = null)
    {
        parent::__construct($_message, $_code, $_previous);
    }
}
