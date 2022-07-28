<?php 

namespace Andromeda\Belajar\PHP\MVC\App{

    function header(string $value) {
        echo $value;
    }
}


namespace Andromeda\Belajar\PHP\MVC\Middleware {

    use Andromeda\Belajar\PHP\MVC\Config\Database;
    use Andromeda\Belajar\PHP\MVC\Domain\Session;
    use Andromeda\Belajar\PHP\MVC\Domain\User;
    use Andromeda\Belajar\PHP\MVC\Repository\SessionRepository;
    use Andromeda\Belajar\PHP\MVC\Repository\UserRepository;
    use Andromeda\Belajar\PHP\MVC\Service\SessionService;
    use PHPUnit\Framework\TestCase;

    class MustNotLoginMiddlewareTest extends TestCase {

        private MustNotLoginMiddleware $middleware;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        public function setUp(): void
        {
            $this->middleware = new MustNotLoginMiddleware();

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->sessionRepository = new SessionRepository(Database::getConnection());

            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();

            putenv("mode=test");
        }

        public function testBeforeGuest()
        {
            $this->middleware->before();

            $this->expectOutputString("");;
        }

        public function testBeforeLogin()
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
            
            $this->middleware->before();

            $this->expectOutputRegex("[Location: /]");            
        }
    }
}