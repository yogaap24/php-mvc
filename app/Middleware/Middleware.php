<?php

namespace Yogaap\PHP\MVC\Middleware;

interface Middleware
{
    function before() : void;
}