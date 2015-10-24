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

        if (!empty($row['totalpayed'])) {
            $user->setTotalpayed($row['totalpayed']);
        }

        if (!empty($row['totalearned'])) {
            $user->setTotalearned($row['totalearned']);
        }
        if (!empty($row['banr'])) {
            $user->setBnr($row['banr']);
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

    public function getIdByUsername($username)
    {
        $stmt = $this->pdo->prepare("SELECT id FROM USERS WHERE user=:username");

        $stmt->bindParam(":username", $username);

        return $stmt->execute();
    }

    public function findByUser($username)
    {
        // Prepare SQL statement
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE user=:username");
        // Bind parameters to their respective values
        $stmt->bindParam(":username", $username);
        // Execute query
        $stmt->execute();
        $users = $stmt->fetch();

        $stmt = $this->pdo->prepare("SELECT * FROM payingusers WHERE id=:userid");
        $stmt->bindParam(":userid", $users['id']);
        $stmt->execute();
        $payingusers = $stmt->fetch();

        $stmt = $this->pdo->prepare("SELECT * FROM doctors WHERE id=:userid");
        $stmt->bindParam(":userid", $users['id']);
        $stmt->execute();
        $doctors = $stmt->fetch();

        if ($users === false){
            return false;
        } elseif (!($payingusers === false)) {
            if(!($doctors === false)){
                return $this->makeUserFromRow($users+$payingusers+$doctors);
            } else {
                return $this->makeUserFromRow($users+$payingusers);
            }
        } elseif (!($doctors === false)) {
                return $this->makeUserFromRow($users+$doctors);
        } else {
            if($users['id'] == null){
                return false;
            } else {
                return $this->makeUserFromRow($users);
            }
        }

        // Don't ask
        /*
         * But if you must
         * I have no idea how the $stmt-syntax works.
         * $stmt->execute() returns an object, but makeUserFromRow requires a row.
         * Therefore, loop through the (one) result, if it's nothing return false,
         * otherwise, create a user.
         * Sure. Why not.
         */

        /*foreach ($stmt as $row) {
            if ($row === false) {
                return false;
            }

            return $this->makeUserFromRow($row);
        }*/

    }

        public function setIsPaying($user)
    {
        $query = "UPDATE ispaying
                  FROM payingusers
                  INNER JOIN users
                  ON users.id = payingusers.id
                  SET ispaying = 1
                  WHERE user=:user";
        $statement = $this->pdo->prepare($query);
        $statement->execute(['user'=>$user]);
        return $statement->fetchColumn();
    }

    public function getIsPaying($user)
    {

        $query = "SELECT ispaying
                  FROM payingusers
                  INNER JOIN users
                  ON users.id = payingusers.id
                  WHERE user=:user";
        $statement = $this->pdo->prepare($query);
        $statement->execute(['user'=>$user]);
        return $statement->fetchColumn();
    }

    public function getIsDoctor($user)
    {
        $query = "SELECT CASE WHEN EXISTS (
                  SELECT *
                  FROM doctors
                  INNER JOIN users
                  ON users.id = doctors.id
                  WHERE  user=:user
                  )
                  THEN CAST(1 AS BIT)
                  ELSE CAST(0 AS BIT) END";
        $statement = $this->pdo->prepare($query);
        $statement->execute(['user'=>$user]);
        return $statement->fetchColumn();
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

    public function getIsAdmin($user)
    {
        $query = "SELECT isadmin FROM users WHERE user=:user";
        $statement = $this->pdo->prepare($query);
        $statement->execute(['user'=>$user]);
        return $statement->fetchColumn();
    }



    public function all()
    {
        $rows = $this->pdo->query("SELECT * FROM users;");
        $payinguser = $this->pdo->query("SELECT * FROM users INNER JOIN payingusers ON users.id=payingusers.id;");
        
        if ($rows === false) {
            return [];
            throw new \Exception('PDO error in all()');
        }

        return array_map([$this, 'makeUserFromRow'], $rows->fetchAll()) + array_map([$this, 'makeUserFromRow'], $payinguser->fetchAll());
    }

    public function save(User $user)
    {;
        if ($user->getUserId() === null) {
            return $this->saveNewUser($user);
        }

        return $this->saveExistingUser($user);
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
        return $stmt->execute([
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

    public function saveSpendings($user, $amount)
    {
        $stmt = $this->pdo->prepare("UPDATE payingusers
                SET totalpayed= totalpayed + :totalpayed WHERE id=:userid"
            );
        return $stmt->execute(['totalpayed'=>$amount,
                        'userid'=>$user->getUserId()
        ]);
    }

    public function saveIsPaying($user){
        $a = $this->checkIsPayingExists($user);
        if ($a == 1) {
            $stmt = $this->pdo->prepare("UPDATE payingusers
                SET ispaying=:ispaying, banr=:banr WHERE id=:userid"
            );
            return $stmt->execute(['ispaying'=>$user->getIspayinguser(),
                'userid'=>$user->getUserId(),
                'banr'=>$user->getBnr()
            ]);
        } else {
            $q = "INSERT INTO payingusers (id , banr , ispaying , totalpayed ) " . "VALUES(:id, :banr, :ispaying, :totalpayed)";
            $stmt = $this->pdo->prepare($q);
            return $stmt->execute([
                'id'=>$user->getUserId(),
                'banr'=>$user->getBnr(),
                'ispaying'=>$user->getIspayinguser(),
                'totalpayed'=>$user->getTotalpayed()
            ]);
        }
    }

    public function checkIsPayingExists($user){
        $stmt = "SELECT CASE WHEN EXISTS(
                SELECT * FROM payingusers WHERE id =:id)
                THEN CAST(1 AS BIT)
                ELSE CAST(0 AS BIT) END";

        $statment = $this->pdo->prepare($stmt);
        $statment->execute(["id"=>$user->getUserId()]);
        return $statment->fetchColumn();
    }

    public function saveEarnings($user, $amount)
    {
        $stmt = $this->pdo->prepare("UPDATE doctors
            SET totalearned= totalearned + :totalearned WHERE id=:userid"
        );

        return $stmt->execute(['userid'=>$user->getUserId(),
                        'totalearned'=>$amount
        ]);
    }

    public function grantStatus($userid)
    {
        $a = $this->getDoctorById($userid);
        if ($a == 1)
        {
            return;
        }

        $q = "INSERT INTO doctors (id, totalearned) " . "VALUES(:id, :totalearned)";
        // Prepare SQL statement
        $stmt = $this->pdo->prepare($q);
        // Execute query
        return $stmt->execute(["id"=> $userid, "totalearned"=>0]);

    }
    public function revokeStatus($userid)
    {
        $q = "DELETE FROM doctors WHERE id=:id";
        // Prepare SQL statement
        $stmt = $this->pdo->prepare($q);
        // Execute query
        return $stmt->execute(["id" => $userid]);
    }

    public function getDoctorById($id)
    {
        $q = "SELECT CASE WHEN EXISTS(
              SELECT * FROM doctors WHERE id =:id)
              THEN CAST(1 AS BIT)
              ELSE CAST(0 AS BIT) END";
        $statment = $this->pdo->prepare($q);
        $statment->execute(["id"=>$id]);
        return $statment->fetchColumn();
    }
}
