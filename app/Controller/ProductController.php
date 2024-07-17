<?php

namespace Yogaap\PHP\MVC\Controller;

class ProductController
{
    function categories(string $productId, string $categoryId) : void
    {
        echo "Product ID: $productId, Category ID: $categoryId";
    }
}