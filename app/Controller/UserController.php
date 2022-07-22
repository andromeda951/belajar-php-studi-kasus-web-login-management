<?php 
 
namespace Andromeda\Belajar\PHP\MVC\Controller;

use Andromeda\Belajar\PHP\MVC\App\View;
use Andromeda\Belajar\PHP\MVC\Config\Database;
use Andromeda\Belajar\PHP\MVC\Model\UserRegisterRequest;
use Andromeda\Belajar\PHP\MVC\Service\UserService;
use Andromeda\Belajar\PHP\MVC\Exception\ValidationException;
use Andromeda\Belajar\PHP\MVC\Repository\UserRepository;

class UserController{

    public UserService $userService;
    public UserRepository $userRepository;

    public function __construct() {
        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->userService = new UserService($this->userRepository);

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

}

