<?php

namespace tdt4237\webapp;
use Symfony\Component\Config\Definition\Exception\Exception;
use tdt4237\webapp\repository\UserRepository;

class Hash
{
    private $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this ->userRepository = $userRepository;
    }

    // Using php 5.5 hashing api
    // see https://gist.github.com/nikic/3707231
    public function CheckAPIpassword($password, $user)
    {
        $hash = $this->userRepository->getHash($user);
        if (!password_verify($password, $hash))
        {
            return false;
        }
        if (password_needs_rehash($hash, PASSWORD_DEFAULT)){
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $this->userRepository->setHash($hash, $user);
        }
        return true;
    }
    public static function createAPIHash($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
