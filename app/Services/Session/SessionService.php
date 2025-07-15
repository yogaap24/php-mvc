<?php

namespace Yogaap\PHP\MVC\Services\Session;

use Ulid\Ulid;
use Yogaap\PHP\MVC\Config\Environment;
use Yogaap\PHP\MVC\Domain\Session;
use Yogaap\PHP\MVC\Domain\User;
use Yogaap\PHP\MVC\Repository\SessionRepository;
use Yogaap\PHP\MVC\Repository\UserRepository;

class SessionService
{
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    public function __construct(SessionRepository $sessionRepository, UserRepository $userRepository)
    {
        $this->sessionRepository = $sessionRepository;
        $this->userRepository = $userRepository;
        $this->startSession();
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $cookieName = Environment::get('SESSION_COOKIE_NAME', 'X-YOGAAP-SESSION');
            $lifetime = (int) Environment::get('SESSION_LIFETIME', 43200);

            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
            ]);

            session_name($cookieName);
            session_start();
        }
    }

    public function store(string $user_id): Session
    {
        $session = new Session();
        $session->id = Ulid::generate();
        $session->user_id = $user_id;

        $this->sessionRepository->save($session);

        $_SESSION['session_id'] = $session->id;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['last_activity'] = time();

        return $session;
    }

    public function destroy(): void
    {
        $sessionId = $_SESSION['session_id'] ?? null;

        if ($sessionId) {
            $this->sessionRepository->deleteSession($sessionId);
        }

        session_destroy();
        session_start();
        session_regenerate_id(true);
    }

    public function current(): ?User
    {
        $sessionId = $_SESSION['session_id'] ?? null;
        $lastActivity = $_SESSION['last_activity'] ?? 0;

        if (!$sessionId) {
            return null;
        }

        // Check session timeout
        $lifetime = (int) Environment::get('SESSION_LIFETIME', 43200);
        if ((time() - $lastActivity) > $lifetime) {
            $this->destroy();
            return null;
        }

        $session = $this->sessionRepository->findSession($sessionId);
        if (!$session) {
            $this->destroy();
            return null;
        }

        // Update last activity
        $_SESSION['last_activity'] = time();

        return $this->userRepository->findUserById($session->user_id);
    }

    public function regenerate(): void
    {
        session_regenerate_id(true);
    }
}