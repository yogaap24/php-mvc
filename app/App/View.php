<?php

namespace Yogaap\PHP\MVC\App;

class View
{
    public static function render(string $view, $model) : void
    {
        require __DIR__ . '/../View/' . $view . '.php';
    }
}