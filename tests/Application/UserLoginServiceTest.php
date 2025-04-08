<?php

declare(strict_types=1);

namespace UserLoginService\Tests\Application;

use Mockery;
use PHPUnit\Framework\TestCase;
use UserLoginService\Application\SessionManager;
use UserLoginService\Application\UserLoginService;
use UserLoginService\Domain\User;
use UserLoginService\Infrastructure\FacebookSessionManager;
use function mysql_xdevapi\getSession;


final class UserLoginServiceTest extends TestCase
{
    private $userLoginService;

    protected function setUp(): void
    {
        parent::setUp();
        $sessionManager = Mockery::mock(SessionManager::class);

        $sessionManager->allows('getSessions')->andReturn(5);

        $this->userLoginService = new UserLoginService($sessionManager);
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

        $this->assertEquals("User not found", $this->userLoginService->logout($user->getUserName()));
    }
}
