<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Middleware;

use ProgrammerZamanNow\Belajar\PHP\MVC\App\View;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Middleware\Middleware;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Service\SessionService;

class MustLoginMiddleware implements Middleware
{
    private SessionService $sessionService;

    public function __construct()
    {
        $sessionRepo = new SessionRepository(Database::getConnection());
        $userRepo = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($sessionRepo, $userRepo);
    }

    function before(): void
    {
        $user = $this->sessionService->currentSession();
        if ($user === null) {
            View::redirect('/users/login');
        }
    }
}
