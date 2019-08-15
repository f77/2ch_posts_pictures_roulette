<?php

namespace PicturesRoulette\Post;

/**
 * Класс поста с изображением.
 */
class ImagePost
{
    /**
     * @var int
     */
    protected $postNumber;

    /**
     * @var string
     */
    protected $imageUrl;

    /**
     * @var string
     */
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
     *
     * @var int
     */
    protected $magnetCombo;


    /**
     * ImagePost constructor.
     *
     * @param int         $_post_number
     * @param string      $_image_url
     * @param string      $_image_url_extension
     * @param string|null $_combo
     * @param string|null $_magnet_combo
     */
    public function __construct(
        int $_post_number,
        string $_image_url,
        string $_image_url_extension,
        string $_combo = null,
        string $_magnet_combo = null
    ) {
        $this->postNumber = $_post_number;
        $this->imageUrl = $_image_url;
        $this->imageUrlExtension = $_image_url_extension;
        $this->combo = $_combo;
        $this->magnetCombo = $_magnet_combo;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return "[\n"
            . '    postNumber:          ' . $this->getPostNumber() . ",\n"
            . '    imageUrl:            ' . $this->getImageUrl() . ",\n"
            . '    imageUrlExtension:   ' . $this->getImageUrlExtension() . "\n"
            . ']';
    }

    //--------------------------------------------------------------------------
    // Геттеры.
    //--------------------------------------------------------------------------
    /**
     * @return int
     */
    public function getPostNumber(): int
    {
        return $this->postNumber;
    }

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    /**
     * @return string
     */
    public function getImageUrlExtension(): string
    {
        return $this->imageUrlExtension;
    }

    /**
     * @return int|string|null
     */
    public function getCombo()
    {
        return $this->combo;
    }

    /**
     * @return int|string|null
     */
    public function getMagnetCombo()
    {
        return $this->magnetCombo;
    }

    /**
     * Вернуть близость магнитного и реального комбо.
     */
    public function getComboDiff(): int
    {
        return \abs((int)$this->combo - (int)$this->magnetCombo);
    }
}
