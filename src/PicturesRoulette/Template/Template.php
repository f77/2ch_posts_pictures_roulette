<?php

namespace PicturesRoulette\Template;

use PicturesRoulette\Template\Exceptions\FileNotExistsException;
use PicturesRoulette\Template\Exceptions\DescriptionParsingErrorException;

class Template
{

    const FILENAME_DESCRIPTION = 'description.json';
    const FILENAME_IMAGE       = 'image.png';



    protected $templateDescription;
    protected $templateVersion;
    protected $cellSize;
    protected $coordinatesCells;
    protected $fileImage;



    public function __construct (string $_template_dir)
    {
        $fileDescription = $_template_dir . '/' . self::FILENAME_DESCRIPTION;
        $this->fileImage = $_template_dir . '/' . self::FILENAME_IMAGE;
        if (!\file_exists ($fileDescription))
        {
            throw new FileNotExistsException ('Файл описания шаблона "' . $fileDescription . '" не найден!');
        }
        if (!\file_exists ($this->fileImage))
        {
            throw new FileNotExistsException ('Файл изображения шаблона "' . $this->fileImage . '" не найден!');
        }

        $descriptionArr = \json_decode (\file_get_contents ($fileDescription), TRUE);
        if ($descriptionArr === NULL)
        {
            throw new DescriptionParsingErrorException ('Ошибка парсинга файла описания шаблона!');
        }

        // Инициализируем.
        $this->templateDescription = $descriptionArr['templateDescription'];
        $this->templateVersion     = $descriptionArr['templateVersion'];
        $this->cellSize            = $descriptionArr['cellSize'];
        $this->coordinatesCells    = $descriptionArr['coordinatesCells'];
    }

    /**
     * Получить массив ячейки по комбинации или пустой массив, если такой комбинации нет.
     * 
     * @param   string  $_combo Комбинация - последние несколько цифр поста.
     * 
     * @return  array           Массив с координатами левого верхнего угла ячейки на шаблоне.
     */
    public function getCellByCombo (string $_combo): array
    {
        return ($this->coordinatesCells[$_combo] ?? []);
    }

    //--------------------------------------------------------------------------
    // Геттеры.
    //--------------------------------------------------------------------------
    public function getDescription (): string
    {
        return $this->templateDescription;
    }

    public function getVersion (): string
    {
        return $this->templateVersion;
    }

    public function getCellSize (): array
    {
        return $this->cellSize;
    }

    public function getCoordinatesCells (): array
    {
        return $this->coordinatesCells;
    }

    public function getImageFilename (): string
    {
        return $this->fileImage;
    }

}
