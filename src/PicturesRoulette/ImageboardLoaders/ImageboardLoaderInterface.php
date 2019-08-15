<?php

namespace PicturesRoulette\ImageboardLoaders;

use PicturesRoulette\Post\ImagePost;
use PicturesRoulette\Post\ImagePostsArray;
use PicturesRoulette\Template\Template;

interface ImageboardLoaderInterface
{
    /**
     * Имя форума.
     */
    public static function getImageboardName(): string;

    /**
     * Получить ВСЕ посты с картинками с треда.
     */
    public function getAllPostsWithImage(): ImagePostsArray;

    /**
     *
     * Получить НОВЫЕ посты (для нововыпавших комбинаций) с треда.
     *
     * @param ImagePostsArray $_current_posts   Уже имеющиеся посты с комбинациями
     *                                          (чтобы новые пикчи не перебивали старые).
     * @param Template        $_template        Шаблон, чтобы можно было выявить новые посты.
     * @param int             $_magnet_radius   Радиус магнита, в округе которого будут искаться комбо.
     *
     * @return ImagePostsArray
     */
    public function getNewPostsWithImage(
        ImagePostsArray $_current_posts,
        Template $_template,
        int $_magnet_radius
    ): ImagePostsArray;

    /**
     * Скачать изображение поста.
     *
     * @param ImagePost $_image_post Целевой пост.
     * @param string    $_output_filename
     */
    public function downloadPostImage(ImagePost $_image_post, string $_output_filename);
}
