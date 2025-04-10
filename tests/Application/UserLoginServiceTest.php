<?php

declare(strict_types=1);

namespace UserLoginService\Tests\Application;

use Exception;
use Mockery;
use Mockery\ExpectationInterface;
use PHPUnit\Framework\TestCase;
use UserLoginService\Application\SessionManager;
use UserLoginService\Application\UserLoginService;
use UserLoginService\Domain\User;


final class UserLoginServiceTest extends TestCase
{
    private SessionManager $sessionManager;
    private User $usuario;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sessionManager = Mockery::mock(SessionManager::class);

        $this->usuario = new User("usuario", "password");
    }


    /**
     * @test
     * @throws Exception
     */
    public function userAlreadyLoggedIn()
    {
        $this->expectExceptionMessage("User already logged in");

        $userLoginService = new UserLoginService($this->sessionManager);

        $userLoginService->manualLogin($this->usuario);
        $userLoginService->manualLogin($this->usuario);
    }

    /**
     * @test
     * @throws Exception
     */
    public function userIsLoggedIn(): void
    {
        $userLoginService = new UserLoginService($this->sessionManager);
        $userLoginService->manualLogin($this->usuario);

        $this->assertEquals("user logged", $userLoginService->getLoggedUser($this->usuario));
    }

    /**
     * @test
     */
    public function returnedNumberOfSessionsIsCorrect(): void
    {
        $this->sessionManager->allows('getSessions')->andReturn(5);

        $userLoginService = new UserLoginService($this->sessionManager);

        $this->assertEquals(5, $userLoginService->getExternalSessions());
    }

    /**
     * @test
     */
    public function unloggedUserLoggingOutReturnsNotFound(): void
    {
        $this->sessionManager->allows('logout')->andReturn(false);

        $userLoginService = new UserLoginService($this->sessionManager);

        $this->assertEquals("User not found", $userLoginService->logout($this->usuario));
    }

    /**
     * @test
     * @throws Exception
     */
    public function loggedUserLoggingOutReturnsOk(): void
    {
        $this->sessionManager->allows('logout')->andReturn(true);

        $userLoginService = new UserLoginService($this->sessionManager);

        $userLoginService->manualLogin($this->usuario);

        $this->assertEquals("Ok", $userLoginService->logout($this->usuario));
    }

    /**
     * @test
     * @throws Exception
     */
    public function userIsLoggedInApiIsSuccessful(): void
    {
        $sessionManagerSpy = Mockery::spy(SessionManager::class);

        $sessionManagerSpy->allows('login')->andReturn(true);
        $userLoginService = new UserLoginService($sessionManagerSpy);

        $this->assertEquals("Login correcto", $userLoginService->login($this->usuario->getUserName(), $this->usuario->getPassword()));

        $sessionManagerSpy->shouldHaveReceived('login')->with($this->usuario->getUserName(), $this->usuario->getPassword());
    }

    /**
     * @test
     * @throws Exception
     */
    public function userIsLoggedInApiIsUnsuccessful(): void
    {
        $sessionManagerSpy = Mockery::spy(SessionManager::class);

        $sessionManagerSpy->allows('login')->andReturn(false);
        $userLoginService = new UserLoginService($sessionManagerSpy);

        $this->assertEquals("Login incorrecto", $userLoginService->login($this->usuario->getUserName(), $this->usuario->getPassword()));

        $sessionManagerSpy->shouldHaveReceived('login')->with($this->usuario->getUserName(), $this->usuario->getPassword());
    }

    /**
     * @test
     * @throws Exception
     */
    public function logoutServiceUnavailable(): void
    {
        $this->expectExceptionMessage("ServiceNotAvailable");

        $sessionManagerSpy = Mockery::spy(SessionManager::class);


        $sessionManagerSpy->allows('logout')->andReturn(false);

        $sessionManagerSpy->allows('login')->andReturn(false);
        $userLoginService = new UserLoginService($sessionManagerSpy);

        $this->assertEquals("Login incorrecto", $userLoginService->login($this->usuario->getUserName(), $this->usuario->getPassword()));

        $sessionManagerSpy->shouldHaveReceived('login')->with($this->usuario->getUserName(), $this->usuario->getPassword());
    }
}
