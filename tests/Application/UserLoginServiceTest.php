<?php

declare(strict_types=1);

namespace UserLoginService\Tests\Application;

use PHPUnit\Framework\TestCase;
use UserLoginService\Application\UserLoginService;
use UserLoginService\Domain\User;
use UserLoginService\Infrastructure\FacebookSessionManager;
use function mysql_xdevapi\getSession;

final class UserLoginServiceTest extends TestCase
{
    /**
     * @test
     */
    public function userAlreadywLoggedIn()
    {
        $user = new User("usuario");

        $userLoginService = new UserLoginService();
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

        $userLoginService = new UserLoginService();
        $userLoginService->manualLogin($user);

        $this->assertEquals("user logged", $userLoginService->getLoggedUser($user));
    }

    /**
     * @test
     */
    public function returnedNumberOfSessionsIsCorrect(): void
    {
        $user = new User("usuario");

        $userLoginService = new UserLoginService();
        $facebookSessionManager = $this->createMock(FacebookSessionManager::class);

        $facebookSessionManager->method('getSessions')->willReturn(5);

        $this->assertEquals(5,$facebookSessionManager->getSessions());
    }
}
