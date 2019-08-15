<?php

namespace PicturesRoulette\Template;

use PicturesRoulette\Template\Exceptions\FileNotExistsException;
use PicturesRoulette\Template\Exceptions\DescriptionParsingErrorException;

class Template
{
    public const FILENAME_DESCRIPTION = 'description.json';
    public const FILENAME_IMAGE = 'image.png';

    /**
     * @var string
     */
    protected $templateDescription;

    /**
     * @var string
     */
    protected $templateVersion;

    /**
     * @var array
     */
    protected $cellSize;

    /**
     * @var array
     */
    protected $coordinatesCells;

    /**
     * @var string
     */
    protected $fileImage;


    /**
     * Template constructor.
     *
     * @param string $_template_dir
     *
     * @throws DescriptionParsingErrorException
     * @throws FileNotExistsException
     */
    public function __construct(string $_template_dir)
    {
        $fileDescription = $_template_dir . '/' . self::FILENAME_DESCRIPTION;
        $this->fileImage = $_template_dir . '/' . self::FILENAME_IMAGE;
        if (!\file_exists($fileDescription)) {
            throw new FileNotExistsException ('Файл описания шаблона "' . $fileDescription . '" не найден!');
        }
        if (!\file_exists($this->fileImage)) {
            throw new FileNotExistsException ('Файл изображения шаблона "' . $this->fileImage . '" не найден!');
        }

        $descriptionArr = \json_decode(\file_get_contents($fileDescription), true);
        if ($descriptionArr === null) {
            throw new DescriptionParsingErrorException ('Ошибка парсинга файла описания шаблона!');
        }

        // Инициализируем.
        $this->templateDescription = $descriptionArr['templateDescription'];
        $this->templateVersion = $descriptionArr['templateVersion'];
        $this->cellSize = $descriptionArr['cellSize'];
        $this->coordinatesCells = $descriptionArr['coordinatesCells'];
    }

    /**
     * Получить массив ячейки по комбинации или пустой массив, если такой комбинации нет.
     *
     * @param   string $_combo Комбинация - последние несколько цифр поста.
     *
     * @return  array           Массив с координатами левого верхнего угла ячейки на шаблоне.
     */
    public function getCellByCombo(string $_combo): array
    {
        return ($this->coordinatesCells[$_combo] ?? []);
    }

    //--------------------------------------------------------------------------
    // Геттеры.
    //--------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->templateDescription;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->templateVersion;
    }

    /**
     * @return array
     */
    public function getCellSize(): array
    {
        return $this->cellSize;
    }

    /**
     * @return array
     */
    public function getCoordinatesCells(): array
    {
        return $this->coordinatesCells;
    }

    /**
     * @return string
     */
    public function getImageFilename(): string
    {
        return $this->fileImage;
    }
}
