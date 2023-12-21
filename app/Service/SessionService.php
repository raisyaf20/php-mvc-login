<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Service;

use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\Session;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;

class SessionService
{
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;
    public static string $cookieName = "X-PZN-SESSION";

    public function __construct(SessionRepository $sesiRepo, UserRepository $userRepo)
    {
        $this->sessionRepository = $sesiRepo;
        $this->userRepository = $userRepo;
    }

    public function create(string $userId): Session
    {
        $session = new Session();
        $session->id = uniqid();
        $session->user_id = $userId;
        $this->sessionRepository->save($session);
        setcookie(self::$cookieName, $session->id, time() + (60 * 60 * 24), '/');
        return $session;
    }
    public function destroy()
    {
        $sessionId = $_COOKIE[self::$cookieName] ?? '';
        $this->sessionRepository->deleteById($sessionId);
        setcookie(self::$cookieName, "",  1, '/');
    }

    public function currentSession(): ?User
    {
        $sessionId = $_COOKIE[self::$cookieName] ?? '';

        $session = $this->sessionRepository->findById($sessionId);
        if ($session == null) {
            return null;
        }

        return $this->userRepository->findById($session->user_id);
    }
}
