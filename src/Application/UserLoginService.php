<?php

namespace UserLoginService\Application;

use Exception;
use UserLoginService\Domain\User;

class UserLoginService
{
    private array $loggedUsers = [];
    private SessionManager $sessionManager;

    public function __construct (SessionManager $sessionManager) {
        $this->sessionManager = $sessionManager;
    }
    /**
     * @throws Exception
     */
    public function manualLogin(User $user): void
    {
        if (in_array($user->getUserName(), $this->loggedUsers)) {
            throw new Exception("User already logged in");
        }

        $this->loggedUsers[] = $user->getUserName();
    }

    public function getLoggedUser(User $user): string
    {
        if (in_array($user->getUserName(), $this->loggedUsers)) {
            return "user logged";
        }
        return "user not logged";
    }

    public function getExternalSessions() : int
    {
        return $this->sessionManager->getSessions();
    }

    public function logout(User $user) : string
    {

        $this->sessionManager->logout($user->getUserName());

        if (!in_array($user->getUserName(), $this->loggedUsers)) {
            return "User not found";
        }

        return "Ok";
    }

    /**
     * @throws Exception
     */
    public function login(string $userName, string $password): string
    {
        if ($this->sessionManager->login($userName, $password)) {
            $this->manualLogin(new User($userName, $password));
            return "Login correcto";
        }
        return "Login incorrecto";
    }
}