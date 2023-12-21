<?php

use PHPUnit\Framework\TestCase;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;

class UserRepositoryTest extends TestCase
{
    private SessionRepository $sessionRepo;
    private UserRepository $userRepository;

    public function setUp(): void
    {
        $this->sessionRepo = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepo->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testSaveSuccess()
    {
        $usr = new User();
        $usr->id = '1k';
        $usr->name = 'test';
        $usr->password = 'testing';

        $this->userRepository->save($usr);


        $result = $this->userRepository->findById($usr->id);

        self::assertEquals($usr->id, $result->id);
        self::assertEquals($usr->name, $result->name);
        self::assertEquals($usr->password, $result->password);
    }

    public function testFindByIdNotFound()
    {
        $user = $this->userRepository->findById("notfound");
        self::assertNull($user);
    }

    public function testUpdateUser()
    {
        $usr = new User();
        $usr->id = '1k';
        $usr->name = 'test';
        $usr->password = 'testing';

        $this->userRepository->save($usr);

        $usr->name = "budi";

        $this->userRepository->updateUser($usr);

        $result = $this->userRepository->findById($usr->id);

        self::assertEquals($usr->id, $result->id);
        self::assertEquals($usr->name, $result->name);
        self::assertEquals($usr->password, $result->password);
    }
}
