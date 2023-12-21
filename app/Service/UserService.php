<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Service;

use Exception;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
use ProgrammerZamanNow\Belajar\PHP\MVC\Exception\ValidationException;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserLoginRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserLoginResponse;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserPasswordUpdateRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserPasswordUpdateResponse;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserProfileUpdateResponse;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserRegisterRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserRegisterResponse;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    public function register(UserRegisterRequest $req): UserRegisterResponse
    {
        $this->validateUserRegistrationRequest($req);

        try {

            Database::beginTransaction();

            $user = $this->userRepository->findById($req->id);

            if ($user != null) {
                throw new ValidationException("User already exists");
            }

            $user = new User();
            $user->id = $req->id;
            $user->name = $req->name;
            $user->password = password_hash($req->password, PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $reponse = new UserRegisterResponse();
            $reponse->user = $user;

            Database::commitTransaction();

            return $reponse;
        } catch (\Exception $err) {
            Database::rollbackTransaction();
            throw $err;
        }
    }

    private function validateUserRegistrationRequest(UserRegisterRequest $request)
    {
        if ($request->id == null || $request->name == null || $request->password == null || trim($request->id == "") || trim($request->name == "") || trim($request->password == "")) {
            throw new ValidationException("id, name, password cannot blank");
        }
    }


    public function login(UserLoginRequest $request): UserLoginResponse
    {
        $this->validateUserLoginRequest($request);
        $user = $this->userRepository->findById($request->id);
        if ($user == null) throw new ValidationException("Id or Password Is Wrong");
        if (password_verify($request->password, $user->password)) {
            $res = new UserLoginResponse();
            $res->user = $user;
            return $res;
        } else {
            throw new ValidationException("Id or Password Is Wrong");
        }
    }

    private function validateUserLoginRequest(UserLoginRequest $request)
    {
        if ($request->id == null || $request->password == null || trim($request->id == "") || trim($request->password == "")) {
            throw new ValidationException("id, password cannot blank");
        }
    }

    public function updateProfile(UserProfileUpdateRequest $req): UserProfileUpdateResponse
    {
        $this->validateUserProfileUpdateRequest($req);
        try {
            Database::beginTransaction();

            $user = $this->userRepository->findById($req->id);
            if ($user == null) {
                throw new ValidationException("User is not found", 1);
            }

            $user->name = $req->name;
            $this->userRepository->updateUser($user);
            Database::commitTransaction();

            $res = new UserProfileUpdateResponse();
            $res->user = $user;
            return $res;
        } catch (\Exception $exc) {
            Database::rollbackTransaction();
            throw $exc;
        }
    }

    private function validateUserProfileUpdateRequest(UserProfileUpdateRequest $request)
    {
        if ($request->id == null || $request->name == null || trim($request->id == "") || trim($request->name == "")) {
            throw new ValidationException("name is required");
        }
    }
    public function updatePassword(UserPasswordUpdateRequest $req): UserPasswordUpdateResponse
    {
        $this->validateUserPasswordUpdateRequest($req);

        try {
            Database::beginTransaction();

            $user = $this->userRepository->findById($req->id);
            if ($user == null) {
                throw new ValidationException("User not found");
            }

            if (!password_verify($req->oldPassword, $user->password)) {
                throw new ValidationException("Old Password wrong");
            }

            $user->password = password_hash($req->newPassword, PASSWORD_BCRYPT);
            $this->userRepository->updateUser($user);
            Database::commitTransaction();

            $res = new UserPasswordUpdateResponse();
            $res->user = $user;
            return $res;
        } catch (Exception $exc) {
            Database::rollbackTransaction();
            throw $exc;
        }
    }

    private function validateUserPasswordUpdateRequest(UserPasswordUpdateRequest $request)
    {
        if ($request->id == null || $request->oldPassword == null || $request->newPassword == null || trim($request->id == "") || trim($request->oldPassword == "") || trim($request->newPassword == "")) {
            throw new ValidationException("id, old and new password is required");
        }
    }
}
