<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Controller;

use ProgrammerZamanNow\Belajar\PHP\MVC\App\View;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Service\SessionService;

class HomeController
{

    private SessionService $sessionService;

    public function __construct()
    {
        $conn = Database::getConnection();
        $sessionRepo = new SessionRepository($conn);
        $userRepo = new UserRepository($conn);
        $this->sessionService = new SessionService($sessionRepo, $userRepo);
    }
    function index(): void
    {

        $user = $this->sessionService->currentSession();

        if ($user == null) {
            $model = [
                "title" => "Login",
            ];

            View::render('Home/index', $model);
        } else {
            $model = [
                "title" => "Dashboard",
                "user" => [
                    'name' => $user->name,
                ]
            ];

            View::render('Home/dashboard', $model);
        }
    }
}
