<?php

namespace PicturesRoulette\Post;

/**
 * Так как в PHP нельзя задавать сложные условия тайп-хинтинга, такие как массивы объектов,
 * приходится писать подобные костыли.
 */
class ImagePostsArray
{
    /**
     * @var ImagePost[]
     */
    protected $arr;

    /**
     * ImagePostsArray constructor.
     */
    public function __construct()
    {
        $this->arr = [];
    }

    /**
     * Заполнить массив.
     * Чтобы он объект мог быть пустым, выносим заполнение в отдельный метод, а не в конструктор.
     *
     * @param ImagePost[] $_arr
     */
    public function fill(ImagePost... $_arr): void
    {
        $this->arr = $_arr;
    }

    /**
     * @param ImagePost $_post
     */
    public function add(ImagePost $_post): void
    {
        $this->arr[] = $_post;
    }

    /**
     * @return ImagePost[]
     */
    public function getAll(): array
    {
        return $this->arr;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return \count($this->arr);
    }

    /**
     * Получить пост по его комбо или номеру поста.
     *
     * @param   string $_combo       Комбо.
     * @param   int    $_post_number Номер поста.
     *
     * @return ImagePost|NULL   Пост или NULL, если такое комбо или номер поста не найдены.
     */
    public function getByComboOrPostNumber(string $_combo, int $_post_number): ?ImagePost
    {
        foreach ($this->arr as $post) {
            if ($post->getCombo() === $_combo || $post->getPostNumber() === $_post_number) {
                return $post;
            }
        }

        return null;
    }

    /**
     * Получить элемент по его индексу.
     *
     * @param   int $_index Индекс элемента.
     *
     * @return  ImagePost|NULL          Элемент или NULL, если такого не найдено.
     */
    public function getByIndex(int $_index): ?ImagePost
    {
        return ($this->arr[$_index] ?? null);
    }

    /**
     * Удалить элемент по его индексу.
     *
     * @param   int $_index Индекс элемента.
     *
     * @return  bool                    Был ли удален элемент или же такой индекс не найден.
     */
    public function deleteByIndex(int $_index): bool
    {
        $elem = $this->getByIndex($_index);
        if ($elem === null) {
            return false;
        }

        unset ($this->arr[$_index]);
        return true;
    }

    /**
     * Вернуть индекс элемента по его магнитному комбо.
     *
     * @param   string $_magnet_combo Магнитное комбо.
     *
     * @return  int|NULL                    Индекс элемента или NULL, если такого магнитного комбо не найдено.
     */
    public function getIndexByMagnetCombo(string $_magnet_combo): ?int
    {
        foreach ($this->arr as $key => $post) {
            // === очень важно строгое сравнение.
            if ($post->getMagnetCombo() === $_magnet_combo) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Получить пост по его магнитному комбо.
     *
     * @param   string $_magnet_combo Магнитное комбо.
     *
     * @return ImagePost|NULL           Пост или NULL, если такое магнитное комбо не найдено.
     */
    public function getByMagnetCombo(string $_magnet_combo): ?ImagePost
    {
        $index = $this->getIndexByMagnetCombo($_magnet_combo);
        if ($index === null) {
            return null;
        }

        return $this->getByIndex($index);
    }

    /**
     * Получить пост по его реальному комбо.
     *
     * @param   string $_real_combo Реальное комбо.
     *
     * @return ImagePost|NULL           Пост или NULL, если такое реальное комбо не найдено.
     */
    public function getByRealCombo(string $_real_combo): ?ImagePost
    {
        foreach ($this->arr as $post) {
            if ($post->getCombo() === $_real_combo) {
                return $post;
            }
        }

        return null;
    }

    /**
     * Получить пост по его комбо.
     *
     * @param   int $_post_number Номер поста.
     *
     * @return ImagePost|NULL   Пост или NULL, если такого номера поста не найдено.
     */
    public function getByPostNumber(int $_post_number): ?ImagePost
    {
        foreach ($this->arr as $post) {
            if ($post->getPostNumber() === $_post_number) {
                return $post;
            }
        }

        return null;
    }
}
