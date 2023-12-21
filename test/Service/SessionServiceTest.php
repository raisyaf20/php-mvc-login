<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Service;


require_once __DIR__ . '/../Helper/helper.php';

use PHPUnit\Framework\TestCase;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\Session;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Service\SessionService;



class SessionServiceTest extends  TestCase
{
    private SessionService $sessionService;
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->id = "eko";
        $user->name = "Eko";
        $user->password = "Rahasia";

        $this->userRepository->save($user);
    }

    public function testCrete()
    {
        $session =  $this->sessionService->create("eko");
        $this->expectOutputRegex("[X-PZN-SESSION : $session->id]");

        $result = $this->sessionRepository->findById($session->id);
        self::assertEquals('eko', $result->user_id);
    }
    public function testDestroy()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->user_id = "eko";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$cookieName] = $session->id;

        $this->sessionService->destroy();

        $this->expectOutputRegex("[X-PZN-SESSION : ]");

        $result = $this->sessionRepository->findById($session->id);
        self::assertNull($result);
    }

    public function testCurrentSession()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->user_id = "eko";

        $this->sessionRepository->save($session);
        $_COOKIE[SessionService::$cookieName] = $session->id;

        $user = $this->sessionService->currentSession();
        self::assertEquals($session->user_id, $user->id);
    }
}
