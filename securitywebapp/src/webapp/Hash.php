<?php

namespace tdt4237\webapp;

use Symfony\Component\Config\Definition\Exception\Exception;
use tdt4237\webapp\repository\UserRepository;

class Hash
{
    // Using php 5.5 hashing api
    // see https://gist.github.com/nikic/3707231
    public function CheckAPIpassword($password, $hash)
    {
        //TODO find hash from database
        $hash = Null;
        if (!pasword_verify($password, $hash))
        {
            return false;
        }
        if (password_needs_rehash($hash, PASSWORD_DEFAULT)){
            $hash = password_hash($password, PASSWORD_DEFAULT);

        }
        return true;
    }

    public static function createAPIHash($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function __construct()
    {
    }

    public static function make($plaintext)
    {
        return hash('sha1', $plaintext . Hash::$salt);

    }

    public function check($plaintext, $hash)
    {
        return $this->make($plaintext) === $hash;
    }

}
