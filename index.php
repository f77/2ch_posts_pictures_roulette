<?php

namespace PicturesRoulette;

/**
 * Точка входа.
 */
\error_reporting(E_ALL);
\ini_set('display_errors', 1);
\set_time_limit(120);

\spl_autoload_register(static function ($_class) {
    require_once 'src/' . str_replace('\\', '/', $_class) . '.php';
});

try {
    // Грузим настройки.
    $config = new Config('config.ini');

    // Запускаем роутер.
    $router = new Router($config);
    $router->start();
} catch (\Throwable $t) {
    $prefix = ($t instanceof \Exception ? 'Непойманное исключение' : 'Непойманная ошибка');
    $msg = $prefix . ' "' . \get_class($t) . '":' . "\n"
        . 'Message:         "' . $t->getMessage() . '".' . "\n"
        . 'Code:            "' . $t->getCode() . '".' . "\n"
        . 'File:            "' . $t->getFile() . '" (Line ' . $t->getLine() . ').' . "\n";

    echo $msg;
}
