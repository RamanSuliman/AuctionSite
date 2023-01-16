<?php
require_once('UserDataSet.php');

class User_actions
{
    private $userID, $is_Logged, $admin, $userDataSet, $attempts;
    public function __construct()
    {
        $this->userDataSet = new UserDataSet();
        session_start();
        $this->userID = '0';
        $this->is_Logged = false;
        $this->admin = false;
        if(!isset($_SESSION['URLtoken'])) {$_SESSION['URLtoken'] = bin2hex(openssl_random_pseudo_bytes(12));}
        if (isset($_SESSION['logged'])) {
            $this->userID = $_SESSION['u_id'];
            if(isset($_SESSION['admin']))
            {
                $this->admin = true;
            }
            else
            {
                $this->is_Logged = true;
            }
        }
        if(!isset($_SESSION['user_ip'])){$this->register_IP();}
        date_default_timezone_set("Europe/London");
    }

    /**
     * Checks if users is logged in or not
     * @return  Boolean
     */
    public function isLoggedIn()
    {
        return $this->is_Logged;
    }
    public function getID()
    {
        return $this->userID;
    }
    public function login_attempts()
    {
        return $this->attempts;
    }
    /**
     * Checks if admin is logged in
     * @return  Boolean
     */
    public function isAdminLogged()
    {
        return $this->admin;
    }

    /**
     * Method used by login controller to authentication user for
     * logging in to the site and place bids. Password varification is used
     * to check the user given password against the hashed password which
     * is associated with given email.
     * @param $email
     * @param $pass
     * @return  Boolean
     */
    public function check_login($email, $pass)
    {
        $password = $this->userDataSet->getLogInDetails($email);
        if (!empty($password)) {
            if(password_verify($pass, $password[1]))
            {
                $_SESSION['u_id'] = $password[0];
                $this->userID = $password[0];
                $_SESSION['logged'] = true;
                switch ($password[2]){
                    case 1:
                        $this->is_Logged = true;
                        break;
                    case 2:
                        $_SESSION['admin'] = true;
                        $this->admin = true;
                        break;
                }
                return true;
            }
        }
        return false;
    }
    public function numberOfUsers()
    {
        return $this->userDataSet->getNumberOfUsers();
    }

    /**
     * This method ensure password is clean from white spaces, backslashes
     * and html elements. It ends up with hashing the password using before
     * being sent to be stored in database.
     * @param $pass
     * @return  String of type User Object.
     */
    private function cleanInput_encrypt($pass)
    {
        $pass = trim($pass);
        $pass = stripslashes($pass);
        $pass = htmlspecialchars($pass);
        $pass = password_hash($pass, PASSWORD_DEFAULT);
        return $pass;
    }
    public function getUserById($user_id)
    {
        return $this->userDataSet->getSpecficuser_searchFacility($user_id);
    }
    public function getUserName($user)
    {
        return $this->userDataSet->getUserName($user);
    }

    /**
     * Responsible for adding new user into database.
     * It also assign new values to the sessions that allow
     * user to be logged in after successful sign up.
     * @param $email
     * @param $firstName
     * @param $lastName
     * @param $pass
     * @return  Boolean
     */
    public function addUser($email, $firstName, $lastName, $pass)
    {
        $new_user = $this->userDataSet->insert_user($email, $firstName, $lastName, $this->cleanInput_encrypt($pass));
        if($new_user === true)
        {
            $id = $this->userDataSet->getUserID($email);
            $_SESSION['u_id'] = $id[0];
            $_SESSION['logged'] = true;
            return true;
        }
        return false;
    }

    /**
     * Execution of this method is linked to logout button which if
     * triggered, it will end up current loghing sessions and prevent
     * user/admin from certain services.
     */
    public function logout()
    {
        unset($_SESSION['u_id']);
        unset($_SESSION['logged']);
        $this->is_Logged = false;
        $this->userID = 0;
        session_destroy();
    }

    /**
     * It validate user entered password, if it doesn't specify security
     * rules it will return false. This method provide extra benefit to
     * ensure strong passwords are given and no special characters are used.
     * @param $password
     * @return  Boolean
     */
    public function validatePassword($password)
    {
        $uppercase = preg_match('/[A-Z]/', $password);
        $lowercase = preg_match('/[a-z]/', $password);
        $number    = preg_match('/[0-9]/', $password);
        $specialChars = preg_match('/[^\w]/', $password);
        if(!$uppercase || !$lowercase || !$number || $specialChars || strlen($password) < 6) {
            return false;
        }
        return true;
    }
    public function isEmailExist($email)
    {
        if($this->userDataSet->checkEmailExist($email))
        {
            return true;
        }
        return false;
    }
    public function remove_user($user)
    {
        $this->userDataSet->removeUser($user);
    }
    public function getAllUsers($offset, $number_of_records)
    {
        return $this->userDataSet->howManyToDisplay($offset, $number_of_records);
    }

    /* -------------------------- Fail login report-------------------------- */
    public function invalid_login_report($email)
    {
        $this->attempts += 1;
        $email = str_pad($email, strlen($email) + (35 - strlen($email)));  //Create padding/empty spaces
        $ip = str_pad($_SESSION['user_ip'], strlen($_SESSION['user_ip']) + (20 - strlen($_SESSION['user_ip'])));
        $current = file_get_contents("login_report");  //Copy current file content.
        $current .= "\n" . $email . $ip . date('Y-m-d h:i:sa'); //Append new line of data to captured content.
        file_put_contents("login_report", $current);   //Write new content to file again
    }
    /* -------------------------- Visitors IP ADDRESS SECTION -------------------------- */

    private function checkIf_IP_Exist($ip)
    {
        return $this->userDataSet->check_IP($ip);
    }

    private function add_new_IP($ip)
    {
        $this->userDataSet->add_IP($ip); //Changes this to user ID
    }

    public function add_location($lat, $lon)
    {
        $this->userDataSet->add_User_Location($this->getID(), $lat, $lon);
    }

    public function register_IP()
    {
        $ip = $this->getIPAddress();
        if(!$this->checkIf_IP_Exist($ip))
        {
            $this->add_new_IP($ip);
        }
        $_SESSION['user_ip'] = $ip;
    }

    private function getIPAddress()
    {
        //whether ip is from the share internet
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $user_ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        //whether ip is from the proxy
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        //whether ip is from the remote address
        else{
            $user_ip = $_SERVER['REMOTE_ADDR'];
        }
        return $user_ip;
    }
}