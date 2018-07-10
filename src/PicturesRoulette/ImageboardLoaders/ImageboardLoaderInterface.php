<?php

namespace PicturesRoulette\ImageboardLoaders;

use PicturesRoulette\Template\Template;
use PicturesRoulette\Post\ImagePostsArray;
use PicturesRoulette\Post\ImagePost;

interface ImageboardLoaderInterface
{

    /**
     * Имя форума.
     */
    public static function getImageboardName (): string;

    /**
     * Получить ВСЕ посты с картинками с треда.
     */
    public function getAllPoststWithImage (): ImagePostsArray;

    /**
     * 
     * Получить НОВЫЕ посты (для нововыпавших комбинаций) с треда.
     * 
     * @param ImagePostsArray $_current_posts   Уже имеющиеся посты с комбинациями
     *                                          (чтобы новые пикчи не перебивали старые).

     * @param Template $_template               Шаблон, чтобы можно было выявить новые посты.
     */
    public function getNewPoststWithImage (ImagePostsArray $_current_posts, Template $_template): ImagePostsArray;

    /**
     * Скачать изображение поста.
     * 
     * @param ImagePost $_image_post    Целевой пост.
     */
    public function downloadPostImage (ImagePost $_image_post, string $_output_filename);
}
