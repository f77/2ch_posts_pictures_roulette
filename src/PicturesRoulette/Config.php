<?php

namespace PicturesRoulette;

class Config
{
    public const PATH_IMAGES_RESULT = 'IMAGES_RESULT';
    public const PATH_IMAGES_TEMP = 'IMAGES_TEMP';
    public const PATH_TEMPLATES = 'TEMPLATES';
    public const FILENAME_ICON_CHECKED = 'res/checked_3.png';
    public const FILENAME_ICON_WARNING = 'res/warning.png';
    public const FILENAME_GITIGNORE = '.gitignore';
    public const FILENAME_KEEP = '.keep';


    /**
     * @var string
     */
    protected $imageboardName;

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
     * @var int
     */
    protected $magnetRadius;

    /**
     * @var int
     */
    protected $outJpgQuality;

    /**
     * @var string
     */
    protected $templateName;

    /**
     * Config constructor.
     *
     * @param string $_config_ini_filename
     */
    public function __construct(string $_config_ini_filename)
    {
        $conf = \parse_ini_file($_config_ini_filename, true);
        $this->imageboardName = $conf['Thread']['IMAGEBOARD'];
        $this->board = $conf['Thread']['BOARD'];
        $this->threadNumber = $conf['Thread']['THREAD_NUMBER'];
        $this->postsExclude = $conf['Thread']['POSTS_EXCLUDE'];
        $this->magnetRadius = $conf['Working']['MAGNET_RADIUS'];
        $this->outJpgQuality = $conf['Image']['OUT_JPG_QUALITY'];
        $this->templateName = $conf['Template']['TEMPLATE_NAME'];
    }

    //--------------------------------------------------------------------------
    // Геттеры.
    //--------------------------------------------------------------------------
    /**
     * @return string
     */
    public function getImageboardName(): string
    {
        return $this->imageboardName;
    }

    /**
     * @return string
     */
    public function getBoard(): string
    {
        return $this->board;
    }

    /**
     * @return int
     */
    public function getThreadNumber(): int
    {
        return $this->threadNumber;
    }

    /**
     * @return array
     */
    public function getPostsExclude(): array
    {
        return $this->postsExclude;
    }

    /**
     * @return int
     */
    public function getMagnetRadius(): int
    {
        return $this->magnetRadius;
    }

    /**
     * @return int
     */
    public function getOutJpgQuality(): int
    {
        return $this->outJpgQuality;
    }

    /**
     * @return string
     */
    public function getTemplateName(): string
    {
        return $this->templateName;
    }
}
