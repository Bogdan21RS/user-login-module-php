<?php

namespace UserLoginService\Application;

use UserLoginService\Domain\User;

class UserLoginService
{
    private array $loggedUsers = [];
    private SessionManager $sessionManager;

    public function __construct (SessionManager $sessionManager) {
        $this->sessionManager = $sessionManager;
    }
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
        return $this->sessionManager->getSessions();
    }

    public function logout(string $user)
    {
        if (!in_array($user, $this->loggedUsers)) {
            return "User not found";
        }

        $this->sessionManager->logout($user);

        return "Ok";
    }


}