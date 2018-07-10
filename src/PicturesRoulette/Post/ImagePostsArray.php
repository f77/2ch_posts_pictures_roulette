<?php

namespace PicturesRoulette\Post;

/**
 * Так как в PHP нельзя задавать сложные условия тайп-хинтинга, такие как массивы объектов,
 * приходится писать подобные костыли.
 */
class ImagePostsArray
{

    protected $arr;



    public function __construct ()
    {
        $this->arr = [];
    }

    /**
     * Заполнить массив.
     * Чтобы он объект мог быть пустым, выносим заполнение в отдельный метод, а не в конструктор.
     */
    public function fill (ImagePost... $_arr)
    {
        $this->arr = $_arr;
    }

    public function add (ImagePost $_post)
    {
        $this->arr[] = $_post;
    }

    public function getAll (): array
    {
        return $this->arr;
    }

    /**
     * Получить пост по его комбо или номеру поста.
     * 
     * @param   string  $_combo         Комбо.
     * @param   int     $_post_number   Номер поста.
     * 
     * @return ImagePost|NULL   Пост или NULL, если такое комбо или номер поста не найдены.
     */
    public function getByComboOrPostNumber (string $_combo, int $_post_number)
    {
        foreach ($this->arr as $post)
        {
            if ($post->getCombo () == $_combo || $post->getPostNumber () == $_post_number)
            {
                return $post;
            }
        }

        return NULL;
    }

    /**
     * Получить пост по его комбо.
     * 
     * @param   string  $_combo         Комбо.
     * 
     * @return ImagePost|NULL   Пост или NULL, если такое комбо не найдено.
     */
    public function getByCombo (string $_combo)
    {
        foreach ($this->arr as $post)
        {
            if ($post->getCombo () == $_combo)
            {
                return $post;
            }
        }

        return NULL;
    }

}
