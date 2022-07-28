<?php 
 
namespace Andromeda\Belajar\PHP\MVC\App{

    function header(string $value) {
        echo $value;
    }
}

namespace Andromeda\Belajar\PHP\MVC\Service{

    function setcookie(string $name, string $value): void {
        echo "$name: $value";
    }
}

namespace Andromeda\Belajar\PHP\MVC\Controller{

    use Andromeda\Belajar\PHP\MVC\Config\Database;
    use Andromeda\Belajar\PHP\MVC\Domain\Session;
    use Andromeda\Belajar\PHP\MVC\Domain\User;
    use Andromeda\Belajar\PHP\MVC\Exception\ValidationException;
    use Andromeda\Belajar\PHP\MVC\Repository\SessionRepository;
    use Andromeda\Belajar\PHP\MVC\Repository\UserRepository;
    use PHPUnit\Framework\TestCase;

    class UserControllerTest extends TestCase{

        public UserController $userController;
        public UserRepository $userRepository;
        public SessionRepository $sessionRepository;

        public function setUp(): void
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

        public function testLogin()
        {
            $this->userController->login();
            
            $this->expectOutputRegex("[User login]");
            $this->expectOutputRegex("[Login]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
        }

        public function testPostLoginSuccess()
        {
            $user = new User();
            $user->id = "andro";
            $user->name = "Andro";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $_POST['id'] = "andro";
            $_POST['password'] = "rahasia";

            $this->userController->postLogin();
            $this->expectOutputRegex("[Location: /]");
            $this->expectOutputRegex("[X-PZN-SESSION: ]");
        }

        public function testLoginValidationError()
        {
            $_POST['id'] = "";
            $_POST['password'] = "rahasia";

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id, Password can not blank]");
        }

        public function testLoginUserNotFound()
        {
            $_POST['id'] = "notfound";
            $_POST['password'] = "notfound";

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id or password is wrong]");
        }

        public function testLoginWrongPassword()
        {
            $user = new User();
            $user->id = "andro";
            $user->name = "Andro";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $_POST['id'] = "andro";
            $_POST['password'] = "salah";

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login user]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id or password is wrong]");
        }

        public function testLogout()
        {
            $user = new User();
            $user->id = "andro";
            $user->name = "Andro";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $this->userController->logout();

            $this->expectOutputRegex("[Location: /]");
            $this->expectOutputRegex("[X-PZN-SESSION]");
        }
    }
}

