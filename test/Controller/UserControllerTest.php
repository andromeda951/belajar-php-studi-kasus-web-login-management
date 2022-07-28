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
    use Andromeda\Belajar\PHP\MVC\Service\SessionService;
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
            $temp = $session->id;
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->logout();

            $this->expectOutputRegex("[Location: /]");
            $this->expectOutputRegex("[X-PZN-SESSION]");

            // tambahan
            $result = $this->sessionRepository->findById($temp);
            $this->assertNull($result);
        }

        public function testUpdateProfile()
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
            
            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->updateProfile();

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[andro]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Andro]");
        }

        public function testPostUpdateProfileSuccess()
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
            
            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['name'] = "Budi";
            $this->userController->postUpdateProfile();

            $this->expectOutputRegex("[Location: /]");

            $result = $this->userRepository->findById("andro");
            $this->assertEquals("Budi", $result->name);
        }

        public function testPostUpdateProfileValidationError()
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
            
            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['name'] = "";
            $this->userController->postUpdateProfile();

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[andro]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Andro]");
            $this->expectOutputRegex("[Id, name can not blank]");
        }

        public function testUpdatePassword()
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
            
            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->updatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[andro]");   
        }

        public function testPostUpdatePasswordSuccess()
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
            
            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = "rahasia";
            $_POST['newPassword'] = "new";
            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Location: /]");

            $result = $this->userRepository->findById($user->id);
            $this->assertTrue(password_verify("new", $result->password));
        }

        public function testUpdatePasswordValidateError()
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
            
            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = "rahasia";
            $_POST['newPassword'] = "";
            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[andro]");   
            $this->expectOutputRegex("[Id, Old Password, New Password can not blank]");
        }

        public function testUpdatePasswordWrongOldPassword()
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
            
            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = "salah";
            $_POST['newPassword'] = "new";
            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[andro]");   
            $this->expectOutputRegex("[Password is wrong]");
        }        
    }
}

