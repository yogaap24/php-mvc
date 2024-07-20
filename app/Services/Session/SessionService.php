<?php

namespace Yogaap\PHP\MVC\Services\Session;

use Ulid\Ulid;
use Yogaap\PHP\MVC\Domain\Session;
use Yogaap\PHP\MVC\Domain\User;
use Yogaap\PHP\MVC\Repository\SessionRepository;
use Yogaap\PHP\MVC\Repository\UserRepository;

class SessionService
{

    public static string $COOKIE_NAME = "X-YOGAAP-SESSION";

    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    public function __construct(SessionRepository $sessionRepository, UserRepository $userRepository)
    {
        $this->sessionRepository = $sessionRepository;
        $this->userRepository = $userRepository;
    }

    public function store(string $user_id) : Session
    {
        $session = new Session();
        $session->id = Ulid::generate();
        $session->user_id = $user_id;

        $this->sessionRepository->save($session);

        setcookie(self::$COOKIE_NAME, $session->id, time() + (60 * 60 * 12 * 30), "/");

        return $session;
    }

    public function destroy() 
    {
        $session_id = $_COOKIE[self::$COOKIE_NAME] ?? null;
        if ($session_id) {
            $this->sessionRepository->deleteSession($session_id);
        }

        setcookie(self::$COOKIE_NAME, '', time() - 1, "/");

    }

    public function current() : ?User
    {
        $session_id = $_COOKIE[self::$COOKIE_NAME] ?? null;
        if (!$session_id) {
            return null;
        }

        $session = $this->sessionRepository->findSession($session_id);
        if (!$session) {
            return null;
        }

        return $this->userRepository->findUser($session->user_id);
    }
}