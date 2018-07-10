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
     * Получить элемент по его индексу.
     * @param   int             $_index Индекс элемента.
     * @return  ImagePost|NULL          Элемент или NULL, если такого не найдено.
     */
    public function getByIndex (int $_index)
    {
        return ($this->arr[$_index] ?? NULL);
    }

    /**
     * Удалить элемент по его индексу.
     * @param   int             $_index Индекс элемента.
     * @return  bool                    Был ли удален элемент или же такой индекс не найден.
     */
    public function deleteByIndex (int $_index): bool
    {
        $elem = $this->getByIndex ($_index);
        if ($elem === NULL)
        {
            return FALSE;
        }

        unset ($this->arr[$_index]);
        return TRUE;
    }

    /**
     * Вернуть индекс элемента по его магнитному комбо.
     * 
     * @param   string      $_magnet_combo  Магнитное комбо.
     * @return  int|NULL                    Индекс элемента или NULL, если такого магнитного комбо не найдено.
     */
    public function getIndexByMagnetCombo (string $_magnet_combo)
    {
        foreach ($this->arr as $key => $post)
        {
            if ($post->getMagnetCombo () == $_magnet_combo)
            {
                return $key;
            }
        }

        return NULL;
    }

    /**
     * Получить пост по его магнитному комбо.
     * 
     * @param   string  $_magnet_combo  Магнитное комбо.
     * 
     * @return ImagePost|NULL           Пост или NULL, если такое магнитное комбо не найдено.
     */
    public function getByMagnetCombo (string $_magnet_combo)
    {
        $index = $this->getIndexByMagnetCombo ($_magnet_combo);
        if ($index === NULL)
        {
            return NULL;
        }

        return $this->getByIndex ($index);
    }

    /**
     * Получить пост по его реальному комбо.
     * 
     * @param   string  $_real_combo    Реальное комбо.
     * 
     * @return ImagePost|NULL           Пост или NULL, если такое реальное комбо не найдено.
     */
    public function getByRealCombo (string $_real_combo)
    {
        foreach ($this->arr as $post)
        {
            if ($post->getCombo () == $_real_combo)
            {
                return $post;
            }
        }

        return NULL;
    }

    /**
     * Получить пост по его комбо.
     * 
     * @param   int     $_post_number   Номер поста.
     * 
     * @return ImagePost|NULL   Пост или NULL, если такого номера поста не найдено.
     */
    public function getByPostNumber (int $_post_number)
    {
        foreach ($this->arr as $post)
        {
            if ($post->getPostNumber () == $_post_number)
            {
                return $post;
            }
        }

        return NULL;
    }

}
