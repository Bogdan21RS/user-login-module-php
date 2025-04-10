<?php

declare(strict_types=1);

namespace UserLoginService\Tests\Application;

use Exception;
use Mockery;
use PHPUnit\Framework\TestCase;
use UserLoginService\Application\SessionManager;
use UserLoginService\Application\UserLoginService;
use UserLoginService\Domain\User;
use UserLoginService\Infrastructure\FacebookSessionManager;


final class UserLoginServiceTest extends TestCase
{
    private UserLoginService $userLoginService;
    private $sessionManager;
    private User $usuario;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sessionManager = Mockery::mock(SessionManager::class);

        $this->sessionManager->allows('getSessions')->andReturn(5);

        $this->userLoginService = new UserLoginService($this->sessionManager);

        $this->usuario = new User("usuario", "password");
    }


    /**
     * @test
     */
    public function userAlreadyLoggedIn()
    {

        $this->expectExceptionMessage("User already logged in");

        $this->userLoginService->manualLogin($this->usuario);
        $this->userLoginService->manualLogin($this->usuario);
    }

    /**
     * @test
     * @throws Exception
     */
    public function userIsLoggedIn(): void
    {
        $this->userLoginService->manualLogin($this->usuario);

        $this->assertEquals("user logged", $this->userLoginService->getLoggedUser($this->usuario));
    }

    /**
     * @test
     */
    public function returnedNumberOfSessionsIsCorrect(): void
    {
        $this->assertEquals(5, $this->userLoginService->getExternalSessions());
    }

    /**
     * @test
     */
    public function unloggedUserLoggingOutReturnsNotFound(): void
    {
        $this->assertEquals("User not found", $this->userLoginService->logout($this->usuario));
    }

    /**
     * @test
     */
    public function loggedUserLoggingOutReturnsOk(): void
    {
        $this->userLoginService->manualLogin($this->usuario);

        $this->assertEquals("Ok", $this->userLoginService->logout($this->usuario));
    }

    /**
     * @test
     * @throws Exception
     */
    public function userIsLoggedInApiIsSuccessful(): void
    {
        $sessionManagerSpy = Mockery::spy(SessionManager::class);

        $this->sessionManager->allows('login')->andReturn(true);
        $userLoginService = new UserLoginService($this->sessionManager);

        $this->assertEquals("Login correcto", $userLoginService->login($this->usuario->getUserName(), $this->usuario->getPassword()));

        $sessionManagerSpy->shouldHaveBeenCalled();
    }

    /**
     * @test
     * @throws Exception
     */
    public function userIsLoggedInApiIsUnsuccessful(): void
    {
        $sessionManager = Mockery::spy(SessionManager::class);

        $this->sessionManager->allows('login')->andReturn(false);
        $userLoginService = new UserLoginService($this->sessionManager);

        $this->assertEquals("Login incorrecto", $userLoginService->login($this->usuario->getUserName(), $this->usuario->getPassword()));

        $sessionManager->shouldHaveReceived()->login($this->usuario->getUserName(), $this->usuario->getPassword());
    }
}
