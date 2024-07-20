<?php

namespace Yogaap\PHP\MVC\Tests\View;

use PHPUnit\Framework\TestCase;
use Yogaap\PHP\MVC\App\View;

class ViewTest extends TestCase
{
    public function testRender()
    {
        View::render('Home/index', [
            'title' => 'Home Page Test',
        ]);

        $this->expectOutputRegex('[Home Page Test]');
        $this->expectOutputRegex('[<body>]');
        $this->expectOutputRegex('[<h1>]');
        $this->expectOutputRegex('[Login]');
        $this->expectOutputRegex('[Register]');
    }
}