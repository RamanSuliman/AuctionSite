<?php
require('Models/Auction.php');
require_once('Models/Database.php');

class AuctionDataSet
{
    protected $_dbHandle, $_dbInstance;

// dataset constructor
    public function __construct()
    {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
    }

    public function fetchAllAuctions()
    {
        $sqlQuery = "SELECT auction_id, title, end_time, end_date, start_date, image, countries.country as country_id FROM auctions 
                        INNER JOIN countries ON countries.country_id = auctions.country_id; ";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute();
        $dataSet = [];
        while($row = $statement->fetch())
        {
            $dataSet[] = new Auction($row);
        }
        return $dataSet;
    }

    /**
     * Retrieve all details of an auction based on given id.
     * @param $auction_ID
     * @return  Object type auction
     */
    public function getSpecificAuction($auction_ID)
    {
        $sqlQuery = "SELECT auction_id, title, end_time, end_date, start_date, image, countries.country as country_id FROM auctions 
                    INNER JOIN countries ON countries.country_id = auctions.country_id WHERE auction_id = :var;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":var",$auction_ID);
        $statement->execute();
        return new Auction($statement->fetch(PDO::FETCH_ASSOC));
    }

    /**
     * Retrieve only fixed number of records, this method is used for sorting and
     * normal auction retrieval. The sorting option is getting auctions of a
     * particular period of time defend by user.
     * @param $offset
     * @param $total_records_per_page
     * @param $query_type
     * @param $interval
     * @return  Array
     */
    public function howManyToDisplay($offset, $total_records_per_page, $query_type, $interval)
    {
        switch ($query_type)
        {
            case 1:
                $sqlQuery = "SELECT auction_id, title, end_time, end_date, start_date, image, countries.country as country_id FROM auctions 
                        INNER JOIN countries ON countries.country_id = auctions.country_id 
                        WHERE end_date BETWEEN DATE_SUB(curdate(), INTERVAL " . $interval . ") AND curdate() LIMIT " .  $total_records_per_page . " OFFSET " .  $offset;
                break;
            case 2:
                $sqlQuery = "SELECT auction_id, title,end_time, end_date, start_date, image, countries.country as country_id FROM auctions 
                        INNER JOIN countries ON countries.country_id = auctions.country_id LIMIT " .  $total_records_per_page . " OFFSET " .  $offset;
        }
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute();
        $dataSet = [];
        while($row = $statement->fetch())
        {
            $dataSet[] = new Auction($row);
        }
        return $dataSet;
    }

    /**
     * Used for filtering and pagination purposes to return the number
     * of records auctions have found based on given interval period.
     * @param $interval
     * @return  int
     */
    public function get_filter_return_auctions($interval)
    {
        $sqlQuery = "SELECT COUNT(auction_id) FROM auctions WHERE end_date
                    BETWEEN DATE_SUB(curdate(), INTERVAL " . $interval . ") AND curdate();";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute();
        return $statement->fetchColumn();
    }

    public function getNumberOfAuctions()
    {
        $sqlQuery = "SELECT COUNT(auction_id) FROM auctions;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute();
        return $statement->fetchColumn();
    }
}