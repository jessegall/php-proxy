<?php

use JetBrains\PhpStorm\NoReturn;

if (! function_exists('dd')) {
    #[NoReturn] function dd(...$vars): void
    {
        foreach ($vars as $var) {
            dump($var);
        }

        die();
    }
}
