<?php

namespace tdt4237\webapp\models;

class User
{

    protected $userId  = null;
    protected $username;
    protected $fullname;
    protected $address;
    protected $postcode;
    protected $hash;
    protected $email   = null;
    protected $bio     = 'Bio is empty.';
    protected $age;
    protected $isAdmin = 0;
    protected $bnr = 0;
    protected $totalpayed = 0;
    protected $totalearned = 0;
    protected $isdoctor;
    protected $ispayinguser;

    function __construct($username, $hash, $fullname, $address, $postcode)
    {
        $this->username = $username;
        $this->hash = $hash;
        $this->fullname = $fullname;
        $this->address = $address;
        $this->postcode = $postcode;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getBio()
    {
        return $this->bio;
    }

    public function getAge()
    {
        return $this->age;
    }

    public function getFullname() {
        return $this->fullname;
    }

    public function setFullname($fullname) {
        $this->fullname = $fullname;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function getPostcode() {
        return $this->postcode;

    }

    public function setPostcode($postcode) {
        $this->postcode = $postcode;

    }

    public function isAdmin()
    {
        return $this->isAdmin === '1';
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setBio($bio)
    {
        $this->bio = $bio;
        return $this;
    }

    public function setAge($age)
    {
        $this->age = $age;
        return $this;
    }

    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    public function getBnr()
    {
        return $this->bnr;
    }

    public function setBnr($banr)
    {
        $this->bnr = $banr;
        return $this;
    }

    public function getIsdoctor()
    {
        return $this->isdoctor;
    }

    public function setIsdoctor($isdoctor)
    {
        $this->isdoctor = $isdoctor;
        return $this;
    }

    public function getIspayinguser()
    {
        return $this->ispayinguser;
    }

    public function setIspayinguser($ispayinguser)

    {   
        if ($ispayinguser == 1) {
            $this->ispayinguser = $ispayinguser;
            return $this;
        }
        else{
            $this->ispayinguser = 0;
            return $this;
        }
    }

    public function getTotalpayed()
    {
        return $this->totalpayed;
    }

    public function setTotalpayed($totalpayed)
    {
        $this->totalpayed = $totalpayed;
        return $this;
    }

    public function getTotalearned()
    {
        return $this->totalearned;
    }

    public function setTotalearned($totalearned)
    {
        $this->totalearned = $totalearned;
        return $this;
    }

}
