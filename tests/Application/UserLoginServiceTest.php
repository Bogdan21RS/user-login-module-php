<?php

declare(strict_types=1);

namespace UserLoginService\Tests\Application;

use Mockery;
use PHPUnit\Framework\TestCase;
use UserLoginService\Application\SessionManager;
use UserLoginService\Application\UserLoginService;
use UserLoginService\Domain\User;
use UserLoginService\Infrastructure\FacebookSessionManager;


final class UserLoginServiceTest extends TestCase
{
    private $userLoginService;
    private $sessionManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sessionManager = Mockery::mock(SessionManager::class);

        $this->sessionManager->allows('getSessions')->andReturn(5);

        $this->userLoginService = new UserLoginService($this->sessionManager);
    }


    /**
     * @test
     */
    public function userAlreadyLoggedIn()
    {
        $user = new User("usuario");

        $this->expectExceptionMessage("User already logged in");

        $this->userLoginService->manualLogin($user);
        $this->userLoginService->manualLogin($user);
    }

    /**
     * @test
     */
    public function userIsLoggedIn(): void
    {
        $user = new User("usuario");

        $this->userLoginService->manualLogin($user);

        $this->assertEquals("user logged", $this->userLoginService->getLoggedUser($user));
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
        $user = new User("usuario");

        $this->assertEquals("User not found", $this->userLoginService->logout($user->getUserName()));
    }

    /**
     * @test
     */
    public function loggedUserLoggingOutReturnsOk(): void
    {
        $user = new User("usuario");

        $this->userLoginService->manualLogin($user);

        $this->assertEquals("Ok", $this->userLoginService->logout($user->getUserName()));
    }

    /**
     * @test
     */
    public function userIsLoggedInApiIsSuccessful(): void
    {
        $user = new User("usuario", "password");
        $sessionManagerSpy = Mockery::spy(SessionManager::class);

        $this->sessionManager->allows('login')->andReturn(true);
        $userLoginService = new UserLoginService($this->sessionManager);

        $this->assertEquals("Login correcto", $userLoginService->login($user->getUserName(), $user->getPassword()));

        $sessionManagerSpy->shouldNotHaveBeenCalled(login, $user->getPassword());
    }

    /**
     * @test
     */
    public function userIsLoggedInApiIsUnsuccessful(): void
    {
        $user = new User("usuario", "password");
        $sessionManager = Mockery::spy(SessionManager::class);

        $this->sessionManager->allows('login')->andReturn(false);
        $userLoginService = new UserLoginService($this->sessionManager);

        $this->assertEquals("Login incorrecto", $userLoginService->login($user->getUserName(), $user->getPassword()));

        $sessionManager->shouldHaveReceived()->login($user->getUserName(), $user->getPassword());
    }
}
