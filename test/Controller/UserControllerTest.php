<?php 
 
namespace Andromeda\Belajar\PHP\MVC\App{

    function header(string $value) {
        echo $value;
    }
}

namespace Andromeda\Belajar\PHP\MVC\Controller{

    use Andromeda\Belajar\PHP\MVC\Config\Database;
    use Andromeda\Belajar\PHP\MVC\Domain\User;
    use Andromeda\Belajar\PHP\MVC\Repository\UserRepository;
    use PHPUnit\Framework\TestCase;

    class UserControllerTest extends TestCase{

        public UserController $userController;
        public UserRepository $userRepository;

        public function setUp(): void
        {
            $this->userController = new UserController();

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->userRepository->deleteAll();

            putenv("mode=test");
        }

        public function testRegister()
        {
            $this->userController->register();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
        }

        public function testPostRegisterSuccess()
        {
            $_POST['id'] = "eko";
            $_POST['name'] = "Eko";
            $_POST['password'] = "rahasia";

            $this->userController->postRegister();

            // $this->expectOutputString("Location: /users/login");
            $this->expectOutputRegex("[Location: /users/login]");

        }

        public function testPostRegisterValidationError()
        {
            $_POST['id'] = "";
            $_POST['name'] = "Eko";
            $_POST['password'] = "rahasia";

            $this->userController->postRegister();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
            $this->expectOutputRegex("[Id, Name, Password can not blank]");   
        }

        public function testPostRegisterDuplicate()
        {
            $user = new User();
            $user->id = "eko";
            $user->name = "Eko";
            $user->password = "rahasia";

            $this->userRepository->save($user) ;

            $_POST['id'] = "eko";
            $_POST['name'] = "Eko";
            $_POST['password'] = "rahasia";

            $this->userController->postRegister();
            
            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
            $this->expectOutputRegex("[User Id already exists]"); 
        }
    }
}

