<?php

namespace PicturesRoulette\Utils;

/**
 * Класс профайлера для измерения времени выполнения участков кода.
 */
class Timer
{

    /**
     * Идентификатор точки времени начала замера.
     */
    const TIME_START = 0;

    /**
     * Идентификатор точки времени конца замера.
     */
    const TIME_END = 1;

    /**
     * Идентификатор длительности выполенния отрезка.
     */
    const TIME_TOTAL = 2;



    /**
     * Массив с информацией о замерах.
     */
    protected $timeSections = [];

    /**
     * Длина дополнения названий секций при общем выводе.
     */
    protected $padLength = 30;



    public function __construct ()
    {
        
    }

    /**
     * Начать замер времени для отрезка.
     * @param string $_section_name     Имя отрезка.
     * @return float                    Временная точка начала замера.
     */
    public function start (string $_section_name): float
    {
        $this->timeSections[$_section_name][self::TIME_START] = \microtime (TRUE);
        return $this->timeSections[$_section_name][self::TIME_START];
    }

    /**
     * Закончить замер времени для отрезка.
     * @param string $_section_name     Имя отрезка.
     * @return float                    Общее время выполнения отрезка.
     */
    public function stop (string $_section_name): float
    {
        $this->timeSections[$_section_name][self::TIME_END]   = \microtime (TRUE);
        $this->timeSections[$_section_name][self::TIME_TOTAL] = $this->timeSections[$_section_name][self::TIME_END] - $this->timeSections[$_section_name][self::TIME_START];

        return $this->timeSections[$_section_name][self::TIME_TOTAL];
    }

    /**
     * Получить общее время выполнения в форматированном виде.
     * 
     * @param string    $_section_name  Имя отрезка.
     * @param int       $_precision     Степень округления.
     * @return string                   Округленное значение общего времени выполнения отрезка.
     */
    public function getFormatted (string $_section_name, int $_precision = 2): string
    {
        return (\round ($this->timeSections[$_section_name][self::TIME_TOTAL], $_precision) . ' s');
    }

    /**
     * Получить общее время выполнения в исходном float-виде.
     * 
     * @param string    $_section_name  Имя отрезка.
     * @param int       $_precision     Степень округления.
     * @return float                    Округленное значение общего времени выполнения отрезка.
     */
    public function getRaw (string $_section_name, int $_precision = 3): float
    {
        return \round ($this->timeSections[$_section_name][self::TIME_TOTAL], $_precision);
    }

    /**
     * Получить общее время выполнения ВСЕХ участков кода в форматированном виде.
     * 
     * @return string
     */
    public function getAllSections (): string
    {
        $result = '';
        foreach ($this->timeSections as $key => $value)
        {
            $result .= \str_pad ($key . ': ', $this->padLength) . $this->getFormatted ($key) . "\n";
        }

        return $result;
    }

}
