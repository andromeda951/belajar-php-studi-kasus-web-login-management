<?php 
 
namespace Andromeda\Belajar\PHP\MVC\Repository;

use Andromeda\Belajar\PHP\MVC\Config\Database;
use Andromeda\Belajar\PHP\MVC\Domain\User;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase{

    public UserRepository $userRepository;
    public SessionRepository $sessionRepository;

    public function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->sessionRepository->deleteAll();

        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();
    }

    public function testSaveSuccess()
    {
        $user = new User();
        $user->id = "eko";
        $user->name = "Ekosds";
        $user->password = "rahasia";

        $this->userRepository->save($user);

        $result = $this->userRepository->findById($user->id);    

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->password, $result->password);
        self::assertEquals($user->name, $result->name);
    }

    public function testFindByIdNotFound()
    {
        $user = $this->userRepository->findById("not found");
        self::assertNull($user);
    }

    public function testUpdate()
    {
        $user = new User();
        $user->id = "eko";
        $user->name = "Ekosds";
        $user->password = "rahasia";

        $this->userRepository->save($user);   

        $user->name = "Budi";
        $this->userRepository->update($user);

        $result = $this->userRepository->findById($user->id);    

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->password, $result->password);
        self::assertEquals($user->name, $result->name);
    }
}

