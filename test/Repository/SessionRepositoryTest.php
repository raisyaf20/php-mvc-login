<?php

use PHPUnit\Framework\TestCase;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\Session;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;

class SessionRepositoryTest extends  TestCase
{
    private SessionRepository $sessionRepo;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepo = new SessionRepository(Database::getConnection());
        $this->sessionRepo->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->id = "eko";
        $user->name = "Eko";
        $user->password = "Rahasia";

        $this->userRepository->save($user);
    }

    public function testSaveSuccess()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->user_id = 'eko';


        $this->sessionRepo->save($session);

        $result = $this->sessionRepo->findById($session->id);

        self::assertEquals($session->id, $result->id);
        self::assertEquals($session->user_id, $result->user_id);
    }

    public function testDeleteByIdSuccess()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->user_id = 'eko';


        $this->sessionRepo->save($session);

        $result = $this->sessionRepo->findById($session->id);

        self::assertEquals($session->id, $result->id);
        self::assertEquals($session->user_id, $result->user_id);

        $this->sessionRepo->deleteById($session->id);
        $res = $this->sessionRepo->findById($session->id);
        self::assertNull($res);
    }

    public function testFindByIdNotFound()
    {
        $res = $this->sessionRepo->findById("id notfound");
        self::assertNull($res);
    }
}
