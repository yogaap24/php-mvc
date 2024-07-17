<?php

namespace Yogaap\PHP\MVC\Tests;

use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
    public function testRegex()
    {
        $path = "/products/12345/categories/abcde";
        $pattern = "#^/products/([0-9a-zA-Z]*)/categories/([0-9a-zA-Z]*)$#";

        $iterations = 1000; // Jumlah iterasi untuk pengujian
        $totalTime = 0;

        for ($i = 0; $i < $iterations; $i++) {
            $startTime = microtime(true);
            $result = preg_match($pattern, $path, $variables);
            $endTime = microtime(true);
            $totalTime += ($endTime - $startTime);
        }

        self::assertEquals(1, $result);
        var_dump($variables);
        array_shift($variables);
        var_dump($variables);

        echo "Regex Average Execution Time: " . ($totalTime / $iterations) . " seconds\n";
    }

    public function testPathMatchingWithoutRegex()
    {
        $path = "/products/12345/categories/abcde";
        $expectedSegments = ['products', 'categories'];

        $iterations = 1000; // Jumlah iterasi untuk pengujian
        $totalTime = 0;

        for ($i = 0; $i < $iterations; $i++) {
            $startTime = microtime(true);
            $pathSegments = explode('/', trim($path, '/'));

            self::assertCount(4, $pathSegments);

            $variables = [];
            $match = true;

            foreach ($expectedSegments as $index => $segment) {
                if ($segment === 'products') {
                    $variables['productId'] = $pathSegments[$index + 1];
                } elseif ($segment === 'categories') {
                    $variables['categoryId'] = $pathSegments[$index + 1];
                }
            }

            self::assertTrue($match);
            $totalTime += (microtime(true) - $startTime);
        }

        var_dump($variables);
        array_shift($variables);
        var_dump($variables);

        echo "Non-RegEx Average Execution Time: " . ($totalTime / $iterations) . " seconds\n";
    }
}
