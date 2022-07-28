<?php 
 
namespace Andromeda\Belajar\PHP\MVC\Controller;

use Andromeda\Belajar\PHP\MVC\App\View;
use Andromeda\Belajar\PHP\MVC\Config\Database;
use Andromeda\Belajar\PHP\MVC\Model\UserRegisterRequest;
use Andromeda\Belajar\PHP\MVC\Service\UserService;
use Andromeda\Belajar\PHP\MVC\Exception\ValidationException;
use Andromeda\Belajar\PHP\MVC\Model\UserLoginRequest;
use Andromeda\Belajar\PHP\MVC\Repository\SessionRepository;
use Andromeda\Belajar\PHP\MVC\Repository\UserRepository;
use Andromeda\Belajar\PHP\MVC\Service\SessionService;

class UserController{

    public UserService $userService;
    public SessionService $sessionService;

    public function __construct() {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);

        $sessionRepository = new SessionRepository($connection);
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function register()
    {
        View::render("User/register", [
            "title" => "Register new User"
        ]);
    }

    public function postRegister()
    {
        $request = new UserRegisterRequest();
        $request->id = $_POST["id"];   
        $request->name = $_POST["name"];   
        $request->password = $_POST["password"];   

        try {
            $this->userService->register($request);
            View::redirect("/users/login");
        } catch (ValidationException $exception) {
            View::render("User/register", [
                "title" => "Register new User",
                "error" => $exception->getMessage()
            ]);
        }
    }

    public function login()
    {
        View::render("User/login", [
            "title" => "User login"
        ]);
    }

    public function postLogin()
    {
        $request = new UserLoginRequest();
        $request->id = $_POST['id'];
        $request->password = $_POST['password'];
        
        try {
            $response = $this->userService->login($request);
            $this->sessionService->create($response->user->id);

            View::redirect("/");
        } catch (ValidationException $exception) {
            View::render("User/login", [
                "title" => "Login user",
                "error" => $exception->getMessage()
            ]);
        }
    }

    public function logout()
    {
        $this->sessionService->destroy();
        View::redirect("/");
    }
}

