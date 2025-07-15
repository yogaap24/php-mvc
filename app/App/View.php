<?php

namespace Yogaap\PHP\MVC\App;

use Yogaap\PHP\MVC\Config\Environment;
use Yogaap\PHP\MVC\Helper\FlashMessage;

class View
{
    public static function render(string $view, $data = []): void
    {
        $flashMessages = FlashMessage::getMessages();
        if (!empty($flashMessages)) {
            $data = array_merge($data, $flashMessages);
        }

        require __DIR__ . '/../View/partials/header.php';
        require __DIR__ . '/../View/' . $view . '.php';
        require __DIR__ . '/../View/partials/footer.php';
    }

    public static function redirect(string $path, array $flashMessages = []): void
    {
        foreach ($flashMessages as $type => $message) {
            FlashMessage::addMessage($type, $message);
        }

        header('Location: ' . $path);

        $debug = Environment::get('APP_DEBUG', 'false') === 'true';
        if (!$debug) {
            exit();
        }
    }
}