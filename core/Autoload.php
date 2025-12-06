<?php
// core/Autoload.php

spl_autoload_register(function ($classe) {

    $pastas = [
        __DIR__ . '/../controles/',
        __DIR__ . '/../modelos/',
        __DIR__ . '/'
    ];

    foreach ($pastas as $pasta) {
        $arquivo = $pasta . $classe . '.php';

        if (file_exists($arquivo)) {
            require_once $arquivo;
            return;
        }
    }
});
