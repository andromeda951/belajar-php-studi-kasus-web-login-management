<?php 
 
namespace Andromeda\Belajar\PHP\MVC\Service;

use Andromeda\Belajar\PHP\MVC\Config\Database;
use Andromeda\Belajar\PHP\MVC\Domain\Session;
use Andromeda\Belajar\PHP\MVC\Domain\User;
use Andromeda\Belajar\PHP\MVC\Repository\SessionRepository;
use Andromeda\Belajar\PHP\MVC\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

function setcookie(string $name, string $value): void {
    echo "$name: $value";
}

class SessionServiceTest extends TestCase{

    public SessionRepository $sessionRepository;
    public SessionService $sessionService;
    public UserRepository $userRepository;

    public function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User;
        $user->id = "andro";
        $user->name = "Andro";
        $user->password = "rahasia";

        $this->userRepository->save($user);

    }

    public function testCreate()
    {
        $session = $this->sessionService->create("andro");                
        $this->expectOutputRegex("[X-PZN-SESSION: $session->id]");

        $result = $this->sessionRepository->findById($session->id);
        $this->assertEquals($session->id, $result->id);
    }

    public function testDestroy()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "andro";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
        $this->sessionService->destroy();

        $this->expectOutputRegex("[X-PZN-SESSION: ]");

        $result = $this->sessionRepository->findById($session->id);
        $this->assertNull($result);
    }

    public function testCurrent()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "andro";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
        
        $user = $this->sessionService->current();
        $this->assertEquals($session->userId, $user->id);
    }

    // tambah sendiri
    public function testCurrentNotFound()
    {
        $result = $this->sessionService->current();
        $this->assertNull($result);
    }
}

