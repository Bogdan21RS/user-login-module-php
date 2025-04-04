<?php

namespace UserLoginService\Application;

use UserLoginService\Domain\User;
use UserLoginService\Infrastructure\FacebookSessionManager;

class UserLoginService
{
    private array $loggedUsers = [];

    /**
     * @throws \Exception
     */
    public function manualLogin(User $user): void
    {
        if (in_array($user, $this->loggedUsers)) {
            throw new \Exception("User already logged in");
        }
        $this->loggedUsers[] = $user;
    }

    public function getLoggedUser(User $user): string
    {
        if (in_array($user, $this->loggedUsers)) {
            return "user logged";
        }
        return "user not logged";
    }

    public function getExternalSessions() : int
    {
        $facebookSessionManager = new FacebookSessionManager();
        return $facebookSessionManager->getSessions();
    }

}