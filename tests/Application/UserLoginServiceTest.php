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
    /**
     * @test
     */
    public function userAlreadyLoggedIn()
    {
        $user = new User("usuario");

        $userLoginService = new UserLoginService(new FacebookSessionManager());
        $this->expectExceptionMessage("User already logged in");

        $userLoginService->manualLogin($user);
        $userLoginService->manualLogin($user);
    }

    /**
     * @test
     */
    public function userIsLoggedIn(): void
    {
        $user = new User("usuario");

        $userLoginService = new UserLoginService(new FacebookSessionManager());
        $userLoginService->manualLogin($user);

        $this->assertEquals("user logged", $userLoginService->getLoggedUser($user));
    }

    /**
     * @test
     */
    public function returnedNumberOfSessionsIsCorrect(): void
    {
        $sessionManager = Mockery::mock(SessionManager::class);

        $sessionManager->allows('getSessions')->andReturn(5);

        $userLoginService = new UserLoginService($sessionManager);

        $this->assertEquals(5, $userLoginService->getExternalSessions());
    }

    /**
     * @test
     */
    public function unloggedUserLoggingOutReturnsNotFound(): void
    {
        $user = new User("usuario");

        $userLoginService = new UserLoginService(new FacebookSessionManager());

        $this->assertEquals("User not found", $userLoginService->logout($user->getUserName()));
    }

    /**
     * @test
     */
    public function loggedUserLoggingOutReturnsOk(): void
    {
        $user = new User("usuario");

        $userLoginService = new UserLoginService(new FacebookSessionManager());
        $userLoginService->manualLogin($user);

        $this->assertEquals("User not found", $userLoginService->logout($user->getUserName()));
    }
}
