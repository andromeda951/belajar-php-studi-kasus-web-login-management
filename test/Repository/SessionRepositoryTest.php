<?php 
 
namespace Andromeda\Belajar\PHP\MVC\Repository;

use Andromeda\Belajar\PHP\MVC\Config\Database;
use Andromeda\Belajar\PHP\MVC\Domain\Session;
use PHPUnit\Framework\TestCase;

class SessionRepositoryTest extends TestCase{

    private SessionRepository $sessionRepository;

    public function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
    }

    public function testSaveSuccess()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "andro";

        $this->sessionRepository->save($session);
        
        $result = $this->sessionRepository->findById($session->id);
        $this->assertEquals($session->id, $result->id);
        $this->assertEquals($session->userId, $result->userId);
    }

    public function testDeleteByIdSuccess()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "andro";

        $this->sessionRepository->save($session);

        $result = $this->sessionRepository->findById($session->id);
        $this->assertEquals($session->id, $result->id);
        $this->assertEquals($session->userId, $result->userId);

        $this->sessionRepository->deleteById($session->id);
        $result = $this->sessionRepository->findById($session->id);

        $this->assertNull($result);
    }

    public function testFindByIdNotFound()
    {
        $result = $this->sessionRepository->findById("notfound");
        $this->assertNull($result);

    }
}

