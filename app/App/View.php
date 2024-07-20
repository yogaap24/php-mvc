<?php

namespace Yogaap\PHP\MVC\App;

use Symfony\Component\Yaml\Yaml;
use Yogaap\PHP\MVC\Helper\FlashMessage;

class View
{
    public static function render(string $view, $data = []) : void
    {
        $flashMessages = FlashMessage::getMessages();
        if (!empty($flashMessages)) {
            $data = array_merge($data, $flashMessages);
        }

        require __DIR__ . '/../View/partials/header.php';
        require __DIR__ . '/../View/' . $view . '.php';
        require __DIR__ . '/../View/partials/footer.php';
    }

    public static function redirect(string $path, array $flashMessages = []) : void
    {
        foreach ($flashMessages as $type => $message) {
            FlashMessage::addMessage($type, $message);
        }
        
        header('Location: ' . $path);
        
        $config = self::loadConfig();
        if (!$config['app']['debug']) {
            exit();
        }
    }

    private static function loadConfig(): array
    {
        $configFile = __DIR__ . '/../../config.yml';
        return Yaml::parseFile($configFile);
    }
}