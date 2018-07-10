<?php

namespace PicturesRoulette;

class Config
{

    const PATH_IMAGES_RESULT = 'IMAGES_RESULT';
    const PATH_IMAGES_TEMP   = 'IMAGES_TEMP';
    const PATH_TEMPLATES     = 'TEMPLATES';
    const FILENAME_GITIGNORE = '.gitignore';
    const FILENAME_KEEP      = '.keep';



    protected $imageboardName;
    protected $board;
    protected $threadNumber;
    protected $postsExclude;
    protected $magnetRadius;
    protected $outJpgQuality;
    protected $templateName;



    public function __construct (string $_config_ini_filename)
    {
        $conf                 = \parse_ini_file ($_config_ini_filename, TRUE);
        $this->imageboardName = $conf['Thread']['IMAGEBOARD'];
        $this->board          = $conf['Thread']['BOARD'];
        $this->threadNumber   = $conf['Thread']['THREAD_NUMBER'];
        $this->postsExclude   = $conf['Thread']['POSTS_EXCLUDE'];
        $this->magnetRadius   = $conf['Working']['MAGNET_RADIUS'];
        $this->outJpgQuality  = $conf['Image']['OUT_JPG_QUALITY'];
        $this->templateName   = $conf['Template']['TEMPLATE_NAME'];
    }

    //--------------------------------------------------------------------------
    // Геттеры.
    //--------------------------------------------------------------------------
    public function getImageboardName (): string
    {
        return $this->imageboardName;
    }

    public function getBoard (): string
    {
        return $this->board;
    }

    public function getThreadNumber (): int
    {
        return $this->threadNumber;
    }

    public function getPostsExclude (): array
    {
        return $this->postsExclude;
    }

    public function getMagnetRadius (): int
    {
        return $this->magnetRadius;
    }

    public function getOutJpgQuality (): int
    {
        return $this->outJpgQuality;
    }

    public function getTemplateName (): string
    {
        return $this->templateName;
    }

}
