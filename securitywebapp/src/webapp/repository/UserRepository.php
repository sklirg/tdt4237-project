<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Age;
use tdt4237\webapp\models\Email;
use tdt4237\webapp\models\NullUser;
use tdt4237\webapp\models\User;

class UserRepository
{
    /* Should not be required anymore
    const INSERT_QUERY   = "INSERT INTO users(user, pass, email, age, bio, isadmin, fullname, address, postcode) VALUES('%s', '%s' , '%s' , '%s', '%s', '%s', '%s', '%s', '%s')";
    const UPDATE_QUERY   = "UPDATE users SET email='%s', age='%s', bio='%s', isadmin='%s', fullname ='%s', address = '%s', postcode = '%s' WHERE id='%s'";
    const FIND_BY_NAME   = "SELECT * FROM users WHERE user='%s'";
    const DELETE_BY_NAME = "DELETE FROM users WHERE user='%s'";
    const SELECT_ALL     = "SELECT * FROM users";
    const FIND_FULL_NAME   = "SELECT * FROM users WHERE user='%s'";
    const FIND_USER_HASH = "SELECT pass FROM users WHERE user='@s'";
    const SET_USER_HASH = "UPDATE users SET pass='%s' WHERE user='%s'";
    */

    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function makeUserFromRow(array $row)
    {
        $user = new User($row['user'], $row['pass'], $row['fullname'], $row['address'], $row['postcode']);
        $user->setUserId($row['id']);
        $user->setFullname($row['fullname']);
        $user->setAddress(($row['address']));
        $user->setPostcode((($row['postcode'])));
        $user->setBio($row['bio']);
        $user->setIsAdmin($row['isadmin']);

        if (!empty($row['email'])) {
            $user->setEmail(new Email($row['email']));
        }

        if (!empty($row['age'])) {
            $user->setAge(new Age($row['age']));
        }

        return $user;
    }

    public function getNameByUsername($username)
    {
        // Prepare SQL statement
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE user=:username");
        // Bind parameters to their respective values
        $stmt->bindParam(":username", $username);
        // Execute query
        $stmt->execute();

        $row = $stmt;
        return $row['fullname'];
    }

    public function findByUser($username)
    {
        // Prepare SQL statement
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE user=:username");
        // Bind parameters to their respective values
        $stmt->bindParam(":username", $username);
        // Execute query
        $stmt->execute();

        // Don't ask
        /*
         * But if you must
         * I have no idea how the $stmt-syntax works.
         * $stmt->execute() returns an object, but makeUserFromRow requires a row.
         * Therefore, loop through the (one) result, if it's nothing return false,
         * otherwise, create a user.
         * Sure. Why not.
         */
        foreach ($stmt as $row) {
            if ($row === false) {
                return false;
            }
            return $this->makeUserFromRow($row);
        }


    }
    public function setHash($hash, $username)
    {
        $query = "UPDATE users SET pass=:password WHERE user=:user";
        $statement = $this->pdo->prepare($query);
        $statement->execute(['password'=>$hash, 'user'=>$username]);
    }

    public function getHash($user)
    {
        $query = "SELECT pass FROM users WHERE user=:user";
        $statement = $this->pdo->prepare($query);
        $statement->execute(['user'=>$user]);
        return $statement->fetchColumn();
    }
    public function deleteByUsername($username)
    {
        // Prepare SQL statement
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE user=:username");
        // Bind parameters to their respective values
        $stmt->bindParam(":username", $username);
        // Execute query
        return $stmt->execute();
    }

    /**
     * @param $user
     * @return string
     */
    public function getIsAdmin($user)
    {
        $query = "SELECT isadmin FROM users WHERE user=:user";
        $statement = $this->pdo->prepare($query);
        $statement->execute(['user'=>$user]);
        return $statement->fetchColumn();
    }



    public function all()
    {

        $rows = $this->pdo->query("SELECT * FROM users");

        if ($rows === false) {
            return [];
            throw new \Exception('PDO error in all()');
        }


        return array_map([$this, 'makeUserFromRow'], $rows->fetchAll());
    }

    public function save(User $user)
    {
        if ($user->getUserId() === null) {
            return $this->saveNewUser($user);
        }

        $this->saveExistingUser($user);
    }

    public function saveNewUser(User $user)
    {
        // Prepare SQL statement
        $stmt = $this->pdo->prepare("INSERT INTO users (user, pass, email, age, bio, isadmin, fullname, address, postcode) " .
            "VALUES (:user, :pass, :email, :age, :bio, :isadmin, :fullname, :address, :postcode)"
        );
        print_r($user->getUsername());
        // Bind parameters to their respective values
        // Execute and bind values all in one
        return $stmt->execute([
            'user'=>$user->getUsername(),
            'pass'=>$user->getHash(),
            'email'=>$user->getEmail(),
            'age'=>$user->getAge(),
            'bio'=>$user->getBio(),
            'isadmin'=>$user->isAdmin(),
            'fullname'=>$user->getFullname(),
            'address'=>$user->getAddress(),
            'postcode'=>$user->getPostcode()
        ]);
    }

    public function saveExistingUser(User $user)
    {
        // Prepare statement
        $stmt = $this->pdo->prepare("UPDATE users " .
            "SET email=:email, age=:age, bio=:bio, isadmin=:isadmin, fullname=:fullname, address=:address, postcode=:postcode WHERE id=:userid"
        );
        // Execute and bind values all in one
        $stmt->execute([
            'userid'=>$user->getUserId(),
            'email'=>$user->getEmail(),
            'age'=>$user->getAge(),
            'bio'=>$user->getBio(),
            'isadmin'=>$user->isAdmin(),
            'fullname'=>$user->getFullname(),
            'address'=>$user->getAddress(),
            'postcode'=>$user->getPostcode()
        ]);
    }

    public function grantStatus($username)
    {
        #TODO Change sql query
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE user=:username");
        // Bind parameters to their respective values
        $stmt->bindParam(":username", $username);
        // Execute query
        return $stmt->execute();

    }
    public function revokeStatus($username)
    {
        #TODO Change sql query
        // Prepare SQL statement
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE user=:username");
        // Bind parameters to their respective values
        $stmt->bindParam(":username", $username);
        // Execute query
        return $stmt->execute();
    }

    public function saveBankAccount()
    {

    }

    public function getBankAccount()
    {

    }

}
