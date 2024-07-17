<?php

namespace Yogaap\PHP\MVC\Controller;

use Yogaap\PHP\MVC\App\View;

class HomeController
{
    function index() : void
    {
        $model = [
            'title' => 'Home Page',
            'content' => 'Welcome to the home page'
        ];

        View::render('Home/index', $model);
    }

    function about() : void
    {
        echo "HomeController.about()";
    }

    function contact() : void
    {
        echo "HomeController.contact()";
    }

    function login() : void
    {
        echo "HomeController.login()";
    }
}