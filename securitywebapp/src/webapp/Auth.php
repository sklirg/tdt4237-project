<?php

namespace tdt4237\webapp;

use Exception;
use tdt4237\webapp\Hash;
use tdt4237\webapp\repository\UserRepository;

class Auth
{

    /**
     * @var Hash
     */
    private $hash;

    /**
     * @var UserRepository
     */
    private $userRepository;


    public function __construct(UserRepository $userRepository, Hash $hash)
    {
        $this->userRepository = $userRepository;
        $this->hash           = $hash;
    }

    public function checkCredentials($username, $password)
    {
        $user = $this->userRepository->findByUser($username);

        if ($user === false) {
            return false;
        }

        return $this->hash->CheckAPIpassword($password, $username);
    }

    /**
     * Check if is logged in.
     */
    public function check()
    {
        return isset($_SESSION['user']);
    }

    public function getUsername() {
        if(isset($_SESSION['user'])){
        return $_SESSION['user'];
        }
    }

    /**
     * Check if the person is a guest.
     */
    public function guest()
    {
        return $this->check() === false;
    }

    /**
     * Check if the person is a doctor
     */

    public function doctor()
    {
        if ($this->check()) {
            echo $this->userRepository->getIsDoctor($_SESSION['user']);
            return $this->userRepository->getIsDoctor($_SESSION['user']) == 1;
        }
    }

    /**
     * Check if the person is a paying user
     */

    public function payinguser()
    {
        if ($this->check()) {
            return $this->userRepository->getIsPaying($_SESSION['user']) == 1;
        }
    }

    /**
     * Get currently logged in user.
     */
    public function user()
    {
        if ($this->check()) {
            return $this->userRepository->findByUser($_SESSION['user']);
        }

        throw new Exception('Not logged in but called Auth::user() anyway');
    }

    /**
     * Is currently logged in user admin?
     */
    public function isAdmin()
    {
        if ($this->check()) {
            return $this->userRepository->getIsAdmin($_SESSION['user']) == 1;
            # return $_COOKIE['isadmin'] === 'yes';
        }

        throw new Exception('Not logged in but called Auth::isAdmin() anyway');
    }

    public function logout()
    {
        if($this->check()) {
            session_unset();
            session_destroy();
        }
    }

}
