<?php

namespace Andromeda\Belajar\PHP\MVC\Service;

use Andromeda\Belajar\PHP\MVC\App\View;
use Andromeda\Belajar\PHP\MVC\Config\Database;
use Andromeda\Belajar\PHP\MVC\Domain\User;
use Andromeda\Belajar\PHP\MVC\Exception\ValidationException;
use Andromeda\Belajar\PHP\MVC\Model\UserRegisterRequest;
use Andromeda\Belajar\PHP\MVC\Model\UserRegisterResponse;
use Andromeda\Belajar\PHP\MVC\Model\UserLoginRequest;
use Andromeda\Belajar\PHP\MVC\Model\UserLoginResponse;
use Andromeda\Belajar\PHP\MVC\Repository\UserRepository;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(UserRegisterRequest $request): UserRegisterResponse
    {
        $this->validateUserRegistrationRequest($request);

        try {
            Database::beginTransaction();
            $user = $this->userRepository->findById($request->id);
            if ($user != null) {
                throw new ValidationException("User Id already exists");
            }
    
            $user = new User();
            $user->id = $request->id;
            $user->name = $request->name;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);
    
            $this->userRepository->save($user);
    
            $response = new UserRegisterResponse();
            $response->user = $user;
    
            Database::commitTransaction();
            return $response;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserRegistrationRequest(UserRegisterRequest $request)
    {
        if ($request->id == null || $request->name == null || $request->password == null || 
            trim($request->id) == "" || trim($request->name) == "" || trim($request->password) == "") {
            throw new ValidationException("Id, Name, Password can not blank");
        }
    }

    public function login(UserLoginRequest $request): UserLoginResponse
    {
        $this->validateUserLoginRequest($request);

        $user = $this->userRepository->findById($request->id);
        if ($user == null) {
            throw new ValidationException("Id or password is wrong");
        }

        if (password_verify($request->password, $user->password)) {
            $response = new UserLoginResponse();
            $response->user = $user;
            return $response;
        } else {
            throw new ValidationException("Id or password is wrong");
        }
    }

    private function validateUserLoginRequest(UserLoginRequest $request)
    {
        if ($request->id == null || $request->password == null || 
            trim($request->id) == "" || trim($request->password) == "") {
            throw new ValidationException("Id, Password can not blank");
        }
    }
} 

