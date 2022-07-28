<?php 
 
namespace Andromeda\Belajar\PHP\MVC\Service;

use Andromeda\Belajar\PHP\MVC\Config\Database;
use Andromeda\Belajar\PHP\MVC\Domain\User;
use Andromeda\Belajar\PHP\MVC\Exception\ValidationException;
use Andromeda\Belajar\PHP\MVC\Model\UserLoginRequest;
use Andromeda\Belajar\PHP\MVC\Model\UserPasswordUpdateRequest;
use Andromeda\Belajar\PHP\MVC\Model\UserPasswordUpdateResponse;
use Andromeda\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;
use Andromeda\Belajar\PHP\MVC\Model\UserRegisterRequest;
use Andromeda\Belajar\PHP\MVC\Repository\SessionRepository;
use Andromeda\Belajar\PHP\MVC\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase{

    private UserService $userService;
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    public function setUp(): void
    {
        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->userService = new UserService($this->userRepository);
        $this->sessionRepository = new SessionRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
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

    public function testUpdateSuccess()
    {
        $user = new User();
        $user->id = "andro";
        $user->name = "Andro";
        $user->password = "rahasia";
        $this->userRepository->save($user);

        $request = new UserProfileUpdateRequest();
        $request->id = "andro";
        $request->name = "Budi";

        $result = $this->userService->updateProfile($request);

        $this->assertEquals($request->name, $result->user->name);
    }

    public function testValidatinError()
    {
        $this->expectException(ValidationException::class);
        $request = new UserProfileUpdateRequest();
        $request->id = "";
        $request->name = "";

        $result = $this->userService->updateProfile($request);
    }

    public function testUpdateNotFound()
    {
        $this->expectException(ValidationException::class);
        $request = new UserProfileUpdateRequest();
        $request->id = "andro";
        $request->name = "Andro";

        $result = $this->userService->updateProfile($request);
    }

    public function testUpdatePasswordSuccess()
    {
        $user = new User();
        $user->id = "andro";
        $user->name = "Andro";
        $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
        $this->userRepository->save($user);
        
        $request = new UserPasswordUpdateRequest();
        $request->id = "andro";
        $request->oldPassword = "rahasia";
        $request->newPassword = "rahasiabaru";

        $this->userService->updatePassword($request);
        $result = $this->userRepository->findById($user->id);

        $this->assertTrue(password_verify($request->newPassword, $result->password));
    }

    public function testUpdatePasswordValidateError() {
        
        $this->expectException(ValidationException::class);

        $user = new User();
        $user->id = "andro";
        $user->name = "Andro";
        $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
        $this->userRepository->save($user);
        
        $request = new UserPasswordUpdateRequest();
        $request->id = "andro";
        $request->oldPassword = "rahasia";
        $request->newPassword = "";

        $this->userService->updatePassword($request);
    }
    
    public function testUpdatePasswordWrongOldPasword() {
        
        $this->expectException(ValidationException::class);

        $user = new User();
        $user->id = "andro";
        $user->name = "Andro";
        $user->password = password_hash("rahasia", PASSWORD_BCRYPT);
        $this->userRepository->save($user);
        
        $request = new UserPasswordUpdateRequest();
        $request->id = "andro";
        $request->oldPassword = "wrongpassword";
        $request->newPassword = "rahasiabaru";

        $this->userService->updatePassword($request);        

    }
    
    public function testUpdatePasswordNotFound() {

        $this->expectException(ValidationException::class);
        
        $request = new UserPasswordUpdateRequest();
        $request->id = "andro";
        $request->oldPassword = "rahasia";
        $request->newPassword = "rahasiabaru";

        $this->userService->updatePassword($request); 
    }

}

