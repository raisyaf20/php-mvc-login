<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\MiddlewareTest {
    require_once __DIR__ . '/../Helper/helper.php';

    use PHPUnit\Framework\TestCase;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\Session;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Middleware\MustLoginMiddleware;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Service\SessionService;

    class MustLoginMiddlewareTest extends TestCase
    {
        private MustLoginMiddleware $loginMidlleware;

        private UserRepository $userRepo;
        private SessionRepository $SessionRepo;

        protected function setUp(): void
        {
            $this->loginMidlleware = new MustLoginMiddleware();
            putenv("mode=test");

            $conn = Database::getConnection();
            $this->userRepo = new UserRepository($conn);
            $this->SessionRepo = new SessionRepository($conn);

            $this->SessionRepo->deleteAll();
            $this->userRepo->deleteAll();
        }

        public function testBeforeGuest()
        {
            $this->loginMidlleware->before();

            $this->expectOutputRegex("[Location: /users/login]");
        }
        public function testBeforeMember()
        {

            $user = new User();
            $user->id = 'sandi';
            $user->name = 'Sandika';
            $user->password = 'sandika gal';

            $this->userRepo->save($user);

            $sesi = new Session();
            $sesi->id = uniqid();
            $sesi->user_id = $user->id;

            $this->SessionRepo->save($sesi);

            $_COOKIE[SessionService::$cookieName] = $sesi->id;
            $this->loginMidlleware->before();
            $this->expectOutputString("");
        }
    }
}
