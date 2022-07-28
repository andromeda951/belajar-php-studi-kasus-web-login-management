<?php 
 
namespace Andromeda\Belajar\PHP\MVC\Middleware;

use Andromeda\Belajar\PHP\MVC\App\View;
use Andromeda\Belajar\PHP\MVC\Config\Database;
use Andromeda\Belajar\PHP\MVC\Domain\Session;
use Andromeda\Belajar\PHP\MVC\Repository\SessionRepository;
use Andromeda\Belajar\PHP\MVC\Repository\UserRepository;
use Andromeda\Belajar\PHP\MVC\Service\SessionService;

class MustLoginMiddleware implements Middleware {

    private SessionService $sessionService;

    public function __construct() {
        
        $sessionRepository = new SessionRepository(Database::getConnection());
        $userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($sessionRepository, $userRepository);

    }

    public function before(): void
    {
        $user = $this->sessionService->current();        

        if($user == null) {
            View::redirect("/users/login");
        }
    }
    
}

