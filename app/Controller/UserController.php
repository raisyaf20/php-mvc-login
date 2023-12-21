<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Controller;

use ProgrammerZamanNow\Belajar\PHP\MVC\App\View;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Exception\ValidationException;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserLoginRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserPasswordUpdateRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserRegisterRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Service\SessionService;
use ProgrammerZamanNow\Belajar\PHP\MVC\Service\UserService;

class UserController
{
    private UserService $userService;
    private SessionService $sessionService;
    private array $model = [];


    public function __construct()
    {
        $conn = Database::getConnection();
        $userRepo = new UserRepository($conn);
        $this->userService = new UserService($userRepo);

        $sessionRepo = new SessionRepository($conn);
        $this->sessionService = new SessionService($sessionRepo, $userRepo);
    }

    public function register()
    {
        $this->model = [
            'title' => 'Register new user',
        ];
        View::render("User/register", $this->model);
    }
    public function postRegister()
    {
        $request = new UserRegisterRequest();
        $request->id = $_POST['id'];
        $request->name = $_POST['name'];
        $request->password = $_POST['password'];

        try {
            $this->userService->register($request);
            View::redirect('/users/login');
        } catch (ValidationException $exception) {
            $this->model = [
                'error' => $exception->getMessage(),
                'title' => 'Register Fail',
            ];
            View::render("User/register", $this->model);
        }
    }

    public function login()
    {
        View::render("User/login", ['title' => 'Login User']);
    }
    public function postLogin()
    {
        $request = new UserLoginRequest();
        $request->id = $_POST['id'];
        $request->password = $_POST['password'];

        try {
            $res = $this->userService->login($request);
            $this->sessionService->create($res->user->id);
            View::redirect('/');
        } catch (ValidationException $err) {
            $this->model = [
                'error' => $err->getMessage(),
                'title' => 'Register Fail',
            ];
            View::render("User/login", $this->model);
        }
    }

    public function updateProfile(): void
    {

        $user = $this->sessionService->currentSession();
        $model = [
            'title' => "Update Profile $user->name",
            "user" => [
                'id' => $user->id,
                'name' => $user->name,
            ],
        ];

        View::render("User/profile", $model);
    }

    public function postUpdateProfile()
    {
        $user = $this->sessionService->currentSession();


        $req = new UserProfileUpdateRequest();
        $req->id = $user->id;
        $req->name = $_POST['name'];

        try {
            $this->userService->updateProfile($req);
            View::redirect('/');
        } catch (ValidationException $exc) {
            $model = [
                'title' => $exc->getMessage(),
                "error" => $exc->getMessage(),
                "user" => [
                    'id' => $user->id,
                    'name' => $_POST['name'],
                ],
            ];

            View::render("User/profile", $model);
        }
    }

    public function updatePassword(): void
    {
        $user = $this->sessionService->currentSession();
        View::render('User/password', [
            'title' => 'Update user password',
            "user" => [
                "id" => $user->id,

            ]
        ]);
    }
    public function postUpdatePassword(): void
    {
        $user = $this->sessionService->currentSession();
        $reques = new UserPasswordUpdateRequest();
        $reques->id = $user->id;

        $reques->oldPassword = $_POST['oldPassword'];
        $reques->newPassword = $_POST['newPassword'];

        try {
            $this->userService->updatePassword($reques);
            View::redirect('/');
        } catch (ValidationException $exc) {
            View::render('User/password', [
                'title' => 'Update user password',
                'error' => $exc->getMessage(),
                "user" => [
                    "id" => $user->id,
                ]
            ]);
        }
    }

    public function logout()
    {
        $this->sessionService->destroy();
        View::redirect('/');
    }
}
