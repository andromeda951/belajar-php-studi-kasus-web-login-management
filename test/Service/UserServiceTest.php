<?php 
 
namespace Andromeda\Belajar\PHP\MVC\Service;

use Andromeda\Belajar\PHP\MVC\Config\Database;
use Andromeda\Belajar\PHP\MVC\Domain\User;
use Andromeda\Belajar\PHP\MVC\Exception\ValidationException;
use Andromeda\Belajar\PHP\MVC\Model\UserLoginRequest;
use Andromeda\Belajar\PHP\MVC\Model\UserRegisterRequest;
use Andromeda\Belajar\PHP\MVC\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase{

    private UserService $userService;
    private UserRepository $userRepository;

    public function setUp(): void
    {
        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->userService = new UserService($this->userRepository);

        $this->userRepository->deleteAll();
    }

    public function testRegisterSuccess()
    {
        $request = new UserRegisterRequest();
        $request->id = "eko";
        $request->name = "Eko";
        $request->password = "rahasia";

        $response = $this->userService->register($request);

        $this->assertEquals($request->id, $response->user->id);
        $this->assertEquals($request->name, $response->user->name);
        $this->assertNotEquals($request->password, $response->user->password);

        $this->assertTrue(password_verify($request->password, $response->user->password));
    }

    public function testRegisterFailed()
    {
        $this->expectException(ValidationException::class);
        $request = new UserRegisterRequest();
        $request->id = "";
        $request->name = "";
        $request->password = "";

        $this->userService->register($request);
    }

    public function testRegisterDuplicate()
    {
        $user = new User();
        $user->id = "eko";
        $user->name = "Eko";
        $user->password = "rahasia";

        $this->userRepository->save($user);
        
        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = "eko";
        $request->name = "Eko";
        $request->password = "rahasia";


        $this->userService->register($request);
    }

    public function testLoginNotFound()
    {
        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = "andro";
        $request->password = "rahasia";

        $this->userService->login($request);
    }

    public function testLoginWrongPassword()
    {
        $user = new User();
        $user->id = "andro";
        $user->name = "Andro";
        $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

        $this->userRepository->save($user);
        $this->expectException(ValidationException::class);

        $request = new UserLoginRequest();
        $request->id = "andro";
        $request->password = "salah";

        $this->userService->login($request);
    }

    public function testLoginSuccess()
    {
        $user = new User();
        $user->id = "andro";
        $user->name = "Andro";
        $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

        $this->userRepository->save($user);
        // $this->expectException(ValidationException::class);
        
        $request = new UserLoginRequest();
        $request->id = "andro";
        $request->password = "rahasia";

        $this->userService->login($request);

        $this->assertEquals($request->id, $user->id);
        $this->assertTrue(password_verify($request->password, $user->password));
    }


}

