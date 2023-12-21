<?php






namespace ProgrammerZamanNow\Belajar\PHP\MVC\Controller {

    require_once __DIR__ . '/../Helper/helper.php';

    use PHPUnit\Framework\TestCase;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Controller\UserController;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\Session;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Service\SessionService;

    class UserControllerTest extends TestCase
    {
        private UserController $userController;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp(): void
        {
            $this->userController = new UserController();

            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->sessionRepository->deleteAll();

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->userRepository->deleteAll();

            putenv("mode=test");
        }

        public function testRegister()
        {
            $this->userController->register();

            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Register new user]');
        }

        public function testPostRegister()
        {
            $_POST['id'] = 'eko';
            $_POST['name'] = 'eko';
            $_POST['password'] = 'eko';

            $this->userController->postRegister();

            $this->expectOutputRegex("[Location: /users/login]");
        }

        public function testRegisterValidationError()
        {
            $_POST['id'] = '';
            $_POST['name'] = '';
            $_POST['password'] = '';

            $this->userController->postRegister();
            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[Register new user]');
            $this->expectOutputRegex('[id, name, password cannot blank]');
        }

        public function testPostRegisterDuplicate()
        {
            $user = new User();
            $user->id = "eko";
            $user->name = "eko";
            $user->password = "eko";

            $this->userRepository->save($user);

            $_POST['id'] = 'eko';
            $_POST['name'] = 'eko';
            $_POST['password'] = 'eko';

            $this->userController->postRegister();
            $this->expectOutputRegex('[Register]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Name]');
            $this->expectOutputRegex('[User already exists]');
        }

        public function testLogin()
        {
            $this->userController->login();
            $this->expectOutputRegex('[Login user]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Password]');
        }
        public function testLoginSuccess()
        {
            $user = new User();
            $user->id = "eko";
            $user->name = "eko";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $_POST['id'] = 'eko';
            $_POST['password'] = 'rahasia';


            $this->userController->postLogin();


            $this->expectOutputRegex('[Location: /]');
            $this->expectOutputRegex("[X-PZN-SESSION : ]");
        }
        public function testLoginValidationError()
        {
            $_POST['id'] = '';
            $_POST['password'] = '';

            $this->userController->postLogin();
            $this->expectOutputRegex('[Login user]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[id, password cannot blank]');
        }
        public function testLoginUserNotFound()
        {
            $_POST['id'] = 'notfound';
            $_POST['password'] = 'teing';

            $this->userController->postLogin();
            $this->expectOutputRegex('[Login user]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Id or Password Is Wrong]');
        }
        public function testLoginWrongPassword()
        {
            $user = new User();
            $user->id = "eko";
            $user->name = "eko";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $_POST['id'] = 'eko';
            $_POST['password'] = 'teing';

            $this->userController->postLogin();
            $this->expectOutputRegex('[Login user]');
            $this->expectOutputRegex('[Id]');
            $this->expectOutputRegex('[Password]');
            $this->expectOutputRegex('[Id or Password Is Wrong]');
        }

        public function testUpdatePassword(): void
        {
            $user = new User();
            $user->id = 'eko';
            $user->name = 'Eko sulisto';
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $sesi = new Session();
            $sesi->id = uniqid();
            $sesi->user_id = $user->id;
            $this->sessionRepository->save($sesi);

            $_COOKIE[SessionService::$cookieName] = $sesi->id;

            // $_POST['oldPassword'] = 'rahasia';
            // $_POST['newPassword'] = 'budi';

            $this->userController->updatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[1]");
        }
        public function testPostUpdatePassword(): void
        {
            $user = new User();
            $user->id = 'brando';
            $user->name = 'bando';
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $sesi = new Session();
            $sesi->id = uniqid();
            $sesi->user_id = $user->id;
            $this->sessionRepository->save($sesi);

            $_COOKIE[SessionService::$cookieName] = $sesi->id;

            $_POST['oldPassword'] = 'rahasia';
            $_POST['newPassword'] = 'budi';

            $this->userController->postUpdatePassword();
            $this->expectOutputRegex("[Location: /]");

            $result = $this->userRepository->findById($user->id);
            self::assertTrue(password_verify('budi', $result->password));
        }

        public function testPostUpdatePasswordValidationError(): void
        {
            $user = new User();
            $user->id = 'brando';
            $user->name = 'bando';
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $sesi = new Session();
            $sesi->id = uniqid();
            $sesi->user_id = $user->id;
            $this->sessionRepository->save($sesi);

            $_COOKIE[SessionService::$cookieName] = $sesi->id;

            $_POST['oldPassword'] = '';
            $_POST['newPassword'] = '';

            $this->userController->postUpdatePassword();
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[1]");
            $this->expectOutputRegex("[id, old and new password is required]");
        }
        public function testPostUpdatePasswordOldPasswordWrong(): void
        {
            $user = new User();
            $user->id = 'brando';
            $user->name = 'bando';
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $sesi = new Session();
            $sesi->id = uniqid();
            $sesi->user_id = $user->id;
            $this->sessionRepository->save($sesi);

            $_COOKIE[SessionService::$cookieName] = $sesi->id;

            $_POST['oldPassword'] = 'testing';
            $_POST['newPassword'] = 'anjay';

            $this->userController->postUpdatePassword();
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[1]");
            $this->expectOutputRegex("[Old Password wrong]");
        }

        public function testLogout()
        {

            $user = new User();
            $user->id = "eko";
            $user->name = "eko";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $sesi = new Session();
            $sesi->id = uniqid();
            $sesi->user_id = $user->id;

            $this->sessionRepository->save($sesi);

            $_COOKIE[SessionService::$cookieName] = $sesi->id;
            $this->userController->logout();

            $this->expectOutputRegex("[Location: ]");
            $this->expectOutputRegex("[X-PZN-SESSION : ]");
        }
    }
}
