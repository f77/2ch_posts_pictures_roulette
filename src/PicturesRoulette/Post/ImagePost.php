<?php

namespace PicturesRoulette\Post;

/**
 * Класс поста с изображением.
 */
class ImagePost
{

    protected $postNumber;
    protected $imageUrl;
    protected $imageUrlExtension;

    /**
     * Комбо нельзя вычислять просто по имени поста.
     * Ибо, допустим, трипл и дабл.
     * Сначала ищется наивысшее комбо, допустим, трипл.
     * Далее трипл может выпасть еще раз, но прежний трипл он не заменит, теперь это просто дабл.
     * Номера постов могут быть разными, но по результату вычисления оба будут триплом.
     * Поэтому надо сохранять комбо при создании объекта.
     * 
     * @var int
     */
    protected $combo;

    /**
     * Магнитное комбо.
     * @var int
     */
    protected $magnetCombo;



    public function __construct (int $_post_number, string $_image_url, string $_image_url_extension,
            int $_combo = NULL, int $_magnet_combo = NULL)
    {
        $this->postNumber        = $_post_number;
        $this->imageUrl          = $_image_url;
        $this->imageUrlExtension = $_image_url_extension;
        $this->combo             = $_combo;
        $this->magnetCombo       = $_magnet_combo;
    }

    public function __toString (): string
    {
        return "[\n"
                . "    postNumber:          " . $this->getPostNumber () . ",\n"
                . "    imageUrl:            " . $this->getImageUrl () . ",\n"
                . "    imageUrlExtension:   " . $this->getImageUrlExtension () . "\n"
                . "]";
    }

    //--------------------------------------------------------------------------
    // Геттеры.
    //--------------------------------------------------------------------------
    public function getPostNumber (): int
    {
        return $this->postNumber;
    }

    public function getImageUrl (): string
    {
        return $this->imageUrl;
    }

    public function getImageUrlExtension (): string
    {
        return $this->imageUrlExtension;
    }

    public function getCombo ()
    {
        return $this->combo;
    }

    public function getMagnetCombo ()
    {
        return $this->magnetCombo;
    }

    /**
     * Вернуть близость магнитного и реального комбо.
     */
    public function getComboDiff (): int
    {
        return \abs ($this->combo - $this->magnetCombo);
    }

}
