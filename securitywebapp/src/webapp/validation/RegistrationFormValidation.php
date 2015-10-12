<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\User;
use tdt4237\webapp\repository\UserRepository;

class RegistrationFormValidation
{
    const MIN_USER_LENGTH = 3;
    const MAX_USER_LENGTH = 16;
    
    private $validationErrors = [];
    
    public function __construct($username, $password, $fullname, $address, $postcode)
    {
        return $this->validate($username, $password, $fullname, $address, $postcode);
    }
    
    public function isGoodToGo()
    {
        return empty($this->validationErrors);
    }
    
    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    private function validate($username, $password, $fullname, $address, $postcode)
    {
        if (empty($password)) {
            $this->validationErrors[] = 'Password cannot be empty';
        }

        if(empty($fullname)) {
            $this->validationErrors[] = "Please write in your full name";
        }

        if(empty($address)) {
            $this->validationErrors[] = "Please write in your address";
        }

        if(empty($postcode)) {
            $this->validationErrors[] = "Please write in your post code";
        }

        if (strlen($postcode) != 4) {
            $this->validationErrors[] = "Post code must be exactly four digits";
        }

        if ($username === '-1') {
            $this->validationErrors[] = "Username already exists";
        }

        else {
            if (strlen($username) < $this::MIN_USER_LENGTH or strlen($username) >= $this::MAX_USER_LENGTH) {
                $this->validationErrors[] = 'Username must be between 3 and 16 characters';
            }

            if (preg_match('/^[A-Za-z0-9_]+$/', $username) === 0) {
                $this->validationErrors[] = 'Username can only contain letters and numbers';
            }

        }

    }
}
