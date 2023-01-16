<?php


class User
{
    private $userID, $firstName, $lastName, $email, $password;

    public function __construct($dbRow)
    {
        $this->userID = $dbRow['user_id'];
        $this->firstName = $dbRow['first_name'];
        $this->lastName = $dbRow['last_name'];
        $this->email = $dbRow['email'];
        $this->password = $dbRow['password'];
    }

    public function getUserID()
    {
        return $this->userID;
    }
    public function getFirstName()
    {
        return $this->firstName;
    }
    public function getLastName()
    {
        return $this->lastName;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getPassword()
    {
        return $this->password;
    }
}