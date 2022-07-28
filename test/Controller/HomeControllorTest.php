<?php 
 
namespace Andromeda\Belajar\PHP\MVC\Controller;

use Andromeda\Belajar\PHP\MVC\Config\Database;
use Andromeda\Belajar\PHP\MVC\Domain\Session;
use Andromeda\Belajar\PHP\MVC\Domain\User;
use Andromeda\Belajar\PHP\MVC\Repository\SessionRepository;
use Andromeda\Belajar\PHP\MVC\Repository\UserRepository;
use Andromeda\Belajar\PHP\MVC\Service\SessionService;
use PHPUnit\Framework\TestCase;

class HomeControllorTest extends TestCase {

    private HomeController $homeController;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    public function setUp(): void
    {
        $this->homeController = new HomeController();
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testGuest()
    {
        $this->homeController->index();   

        $this->expectOutputRegex("[Login Management]");
    }

    public function testLoginUser()
    {
        $user = new User();
        $user->id = "andro";
        $user->name = "Andro";
        $user->password = "rahasia";
        $this->userRepository->save($user);

        $session = new Session();
        $session->id = uniqid();
        $session->userId = $user->id;
        $this->sessionRepository->save($session);
        
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->homeController->index();   

        $this->expectOutputRegex("[Hello Andro]");
    }
}