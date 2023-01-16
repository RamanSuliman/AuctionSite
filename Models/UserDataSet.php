<?php
require_once("User_Data.php");
require_once("Database.php");

class UserDataSet
{
    protected $_dbHandle, $_dbInstance, $date, $time;

// dataset constructor
    public function __construct()
    {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
        date_default_timezone_set("Europe/London");
        $this->date = date('Y-m-d');
        $this->time = date('h:i:sa');
    }

    /**
     * Retrieve all users data from database
     * @return  Array of type User Object
     */
    public function fetchAllStudents()
    {
        $sqlQuery = "SELECT * FROM users;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute();
        $dataSet = [];
        while($row = $statement->fetch())
        {
            $dataSet[] = new User($row);
        }
        return $dataSet;
    }

    /**
     * Counts the number of users in user table, this would be
     * used for pagination purposes.
     * @return  int numberOfUsers
     */
    public function getNumberOfUsers()
    {
        $sqlQuery = "SELECT COUNT(user_id) FROM users;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute();
        return $statement->fetchColumn();
    }

    /**
     * Retrieve log in information based on the unique given email.
     * @param $email
     * @return  Array holdingQueryResultsColumns OR false if not found.
     */
    public function getLogInDetails($email)
    {
        $sqlQuery = "SELECT user_id, password, user_type FROM users WHERE email = :email;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":email",$email);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_NUM);
    }

    /**
     * Retrieve first name of user based on given user ID as argument.
     * @param $user_id
     * @return  String
     */
    public function getUserName($user_id)
    {
        $sqlQuery = "SELECT first_name FROM users WHERE user_id = :user;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":user",$user_id);
        $statement->execute();
        return $statement->fetchColumn();
    }

    /**
     * Check if email already exist, used beside other sign up
     * processes for new users.
     * @param $email
     * @return  Boolean
     */
    public function checkEmailExist($email)
    {
        $sqlQuery = "SELECT email FROM users WHERE email = :email;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":email",$email);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if(is_array($row)){return true;}
        return false;
    }

    /**
     * Insert new user into database
     * @param $email
     * @param $f_name
     * @param $l_name
     * @param $pass
     * @return  Boolean to determine operation success or failure.
     */
    public function insert_user($email,$f_name, $l_name, $pass)
    {
        $sqlQuery = "INSERT INTO users (first_name, last_name, email, password) VALUES (?,?,?,?);";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute([$f_name, $l_name,$email, $pass]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if(!is_array($row)){return true;}
        return false;
    }

    /**
     * Retrieve user ID from user table which belongs to the given email.
     * @param $user_email
     * @return  String singleColumnHoldingID
     */
    public function getUserID($user_email)
    {
        $sqlQuery = "SELECT user_id FROM users WHERE email = :email;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":email",$user_email);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_NUM);
    }

    /**
     * Delete user record from user table in database based on the ID
     * given in the argument.
     * @param $user_id
     * @return  Boolean
     */
    public function removeUser($user_id)
    {
        $sqlQuery = "DELETE FROM users WHERE user_id = ?";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute([$user_id]);
    }

    /**
     * This method used for pagination purposes, it will return limited
     * number of records based on the limit and offset given in
     * argument.
     * @param $offset
     * @param $total_records_per_page
     * @return  Array of type User object.
     */
    public function howManyToDisplay($offset, $total_records_per_page)
    {
        $sqlQuery = "SELECT * FROM users LIMIT " .  $total_records_per_page . " OFFSET " .  $offset;
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute();
        $dataSet = [];
        while($row = $statement->fetch())
        {
            $dataSet[] = new User($row);
        }
        return $dataSet;
    }

    /**
     * Used in Admin page in order to only fetch specific user
     * details for viewing and user removal purposes.
     * @param $user_ID
     * @return  Object of type User
     */
    public function getSpecficuser_searchFacility($user_ID)
    {
        $sqlQuery = "SELECT * FROM users WHERE user_id = ?;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute([$user_ID]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if(!is_array($row))return null;
        return new User($row);
    }

    public function check_IP($ip)
    {
        $sqlQuery = "SELECT ip_address FROM visitor_location WHERE ip_address = :ip;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":ip",$ip);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if(!is_array($row))return false;
        return true;
    }

    public function add_IP($ip)
    {
        $sqlQuery = "INSERT INTO visitor_location (ip_address, date, time) VALUES (?,?,?);";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute([$ip, $this->date, $this->time]);
        $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function add_User_Location($user_id, $lat, $lon)
    {
        $sqlQuery = "INSERT INTO locations (user_id, latitude, longitude) VALUES (?,?,?);";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute([$user_id, $lat, $lon]);
        $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function get_user_latest_IP($userId)
    {
        $sqlQuery = "SELECT ip_address FROM user_location WHERE ip_address = :ip;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":ip",$ip);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if(!is_array($row)){return false;}
        return true;
    }
}