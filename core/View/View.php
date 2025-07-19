<?php

namespace Core\View;

use Core\Http\Response;

class View
{
    private static array $viewPaths = [];
    private static string $layout = 'layouts/main';
    private static array $sharedData = [];

    public static function setViewPaths(array $paths): void
    {
        self::$viewPaths = $paths;
    }

    public static function addViewPath(string $path): void
    {
        self::$viewPaths[] = rtrim($path, '/');
    }

    public static function setLayout(string $layout): void
    {
        self::$layout = $layout;
    }

    public static function share(string $key, $value): void
    {
        self::$sharedData[$key] = $value;
    }

    public static function render(string $view, array $data = [], ?string $layout = null): Response
    {
        $layout = $layout ?? self::$layout;

        // Merge shared data with view data
        $data = array_merge(self::$sharedData, $data);

        // Render the view content
        $content = self::renderView($view, $data);

        // If layout is specified, wrap content in layout
        if ($layout) {
            $layoutContent = self::renderView($layout, array_merge($data, ['content' => $content]));
            return new Response($layoutContent);
        }

        return new Response($content);
    }

    public static function renderPartial(string $partial, array $data = []): string
    {
        return self::renderView('partials/' . $partial, array_merge(self::$sharedData, $data));
    }

    private static function renderView(string $view, array $data): string
    {
        $filePath = self::findViewFile($view);

        if (!$filePath) {
            throw new \Exception("View file not found: {$view}");
        }

        // Extract data to variables
        extract($data, EXTR_SKIP);

        // Start output buffering
        ob_start();

        // Include the view file
        include $filePath;

        // Get and clean buffer
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    private static function findViewFile(string $view): ?string
    {
        $viewPaths = self::getViewPaths();
        $viewFile = $view . '.php';

        // Try to find view in each path
        foreach ($viewPaths as $path) {
            $fullPath = $path . '/' . $viewFile;
            if (file_exists($fullPath)) {
                return $fullPath;
            }
        }

        return null;
    }

    private static function getViewPaths(): array
    {
        if (empty(self::$viewPaths)) {
            self::initializeDefaultPaths();
        }

        return self::$viewPaths;
    }

    private static function initializeDefaultPaths(): void
    {
        $basePath = __DIR__ . '/../..';

        self::$viewPaths = [
            // 1. Resources views (shared layouts, partials, components)
            $basePath . '/resources/views',

            // 2. Module views (module specific)
            // Will be dynamically added based on view path
        ];

        // Add module view paths dynamically
        self::addModuleViewPaths();
    }

    private static function addModuleViewPaths(): void
    {
        $modulesPath = __DIR__ . '/../../modules';

        if (!is_dir($modulesPath)) {
            return;
        }

        $modules = array_filter(scandir($modulesPath), function($item) use ($modulesPath) {
            return $item !== '.' && $item !== '..' && is_dir($modulesPath . '/' . $item);
        });

        foreach ($modules as $module) {
            $moduleViewPath = $modulesPath . '/' . $module . '/View';
            if (is_dir($moduleViewPath)) {
                self::$viewPaths[] = $moduleViewPath;
            }
        }
    }

    // Helper methods for views
    public static function escape(string $data): string
    {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    public static function asset(string $path): string
    {
        return '/assets/' . ltrim($path, '/');
    }

    public static function url(string $path = ''): string
    {
        // Handle missing REQUEST_SCHEME and HTTP_HOST in CLI/test environments
        $scheme = $_SERVER['REQUEST_SCHEME'] ?? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http');
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        $baseUrl = $scheme . '://' . $host;
        return $baseUrl . '/' . ltrim($path, '/');
    }
}
