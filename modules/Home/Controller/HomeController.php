<?php

namespace Modules\Home\Controller;

use Core\Http\BaseController;
use Core\Http\Request;
use Core\Http\Response;
use Core\View\View;

class HomeController extends BaseController
{
    public function index(Request $request): Response
    {
        return View::render('index', [
            'title' => 'Welcome to PHP MVC Framework'
        ]);
    }

    public function dashboard(Request $request): Response
    {
        return View::render('dashboard', [
            'title' => 'Dashboard'
        ]);
    }
}