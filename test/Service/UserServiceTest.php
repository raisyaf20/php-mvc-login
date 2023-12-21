<?php

use PHPUnit\Framework\TestCase;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
use ProgrammerZamanNow\Belajar\PHP\MVC\Exception\ValidationException;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserLoginRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserPasswordUpdateRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserRegisterRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Service\UserService;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepo;

    protected function setUp(): void
    {
        $conn = Database::getConnection();
        $this->userRepository = new UserRepository($conn);
        $this->userService = new UserService($this->userRepository);
        $this->sessionRepo = new SessionRepository($conn);
        $this->sessionRepo->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testRegisterSuccess()
    {
        $req = new UserRegisterRequest();

        $req->id = "test1";
        $req->name = "test eko";
        $req->password = "testing";

        $res = $this->userService->register($req);

        self::assertEquals($req->id, $res->user->id);
        self::assertEquals($req->name, $res->user->name);
        self::assertNotEquals($req->password, $res->user->password);

        self::assertTrue(password_verify($req->password, $res->user->password));
    }
    public function testRegisterFailed()
    {
        $this->expectException(ValidationException::class);

        $req = new UserRegisterRequest();
        $req->id = "";
        $req->name = "";
        $req->password = "";

        $this->userService->register($req);
    }

    public function testRegisterDuplicate()
    {
        $req = new User();
        $req->id = "test1";
        $req->name = "test eko";
        $req->password = "testing";

        $this->userRepository->save($req);

        $this->expectException(ValidationException::class);


        $user = new UserRegisterRequest();
        $user->id = "test1";
        $user->name = "test eko";
        $user->password = "testing";

        $this->userService->register($user);
    }

    public function testLoginNotFound()
    {
        $this->expectException(ValidationException::class);

        $req = new UserLoginRequest();
        $req->id = "eko";
        $req->password = "eko";

        $this->userService->login($req);
    }

    public function testLoginWrongPassword()
    {

        $user = new User();
        $user->id = "eko";
        $user->name = "eko";
        $user->password = password_hash('eko', PASSWORD_BCRYPT);

        $this->expectException(ValidationException::class);

        $req = new UserLoginRequest();
        $req->id = "eko";
        $req->password = "eko";

        $this->userService->login($req);
    }

    public function testLoginSuccess()
    {

        $user = new User();
        $user->id = "eko";
        $user->name = "eko";
        $user->password = password_hash('eko', PASSWORD_BCRYPT);

        $this->expectException(ValidationException::class);

        $req = new UserLoginRequest();
        $req->id = "eko";
        $req->password = "eko";

        $res =  $this->userService->login($req);

        self::assertEquals($req->id, $res->user->id);
        self::assertTrue(password_verify($req->password, $res->user->password));
    }

    public function testUpdateSuccess()
    {
        $user = new User();
        $user->id = "eko";
        $user->name = "Eko";
        $user->password = password_hash('iyadong', PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $req = new UserProfileUpdateRequest();
        $req->id = $user->id;
        $req->name = 'Budikah';
        $this->userService->updateProfile($req);

        $result = $this->userRepository->findById($user->id);
        self::assertEquals($req->name, $result->name);
    }

    public function testUpdateValidationError()
    {
        $this->expectException(ValidationException::class);

        $req = new UserProfileUpdateRequest();
        $req->id = "";
        $req->name = 'Budikah';
        $this->userService->updateProfile($req);
    }

    public function testUpdateNotFound()
    {

        $this->expectException(ValidationException::class);

        $req = new UserProfileUpdateRequest();
        $req->id = "tidak ada";
        $req->name = 'Budikah';
        $this->userService->updateProfile($req);
    }

    public function testUpdatePasswordSuccess()
    {
        $user = new User();
        $user->id = "eko";
        $user->name = "Eko";
        $user->password = password_hash('iyadong', PASSWORD_BCRYPT);
        $this->userRepository->save($user);

        $request = new UserPasswordUpdateRequest();
        $request->id = $user->id;
        $request->oldPassword = 'iyadong';
        $request->newPassword = 'baru euy';

        $this->userService->updatePassword($request);

        $result = $this->userRepository->findById($user->id);
        self::assertTrue(password_verify($request->newPassword, $result->password));
    }
}
