<?php
require('Bid.php');
require_once('Database.php');

class BidsDataSet
{
    protected $_dbHandle, $_dbInstance, $userTotalBids;

// dataset constructor
    public function __construct()
    {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
    }

    /**
     * Retrieve all bidding records and store them into an array of
     * type Bid object which is returned.
     * @return  Array of type Bid.
     */
    public function fetchAllBids()
    {
        $sqlQuery = "SELECT * FROM bids;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute();
        $dataSet = [];
        while($row = $statement->fetch())
        {
            $dataSet[] = new Bid($row);
        }
        return $dataSet;
    }

    public function getUserTotalBids()
    {
        return $this->userTotalBids;
    }

    /**
     * Return the number of times specific item been bid for by users.
     * @param $item
     * @return  int
     */
    public function getNumberOfItemBids($item)
    {
        $sqlQuery = "SELECT COUNT(item_id)as count FROM bids WHERE item_id = :var;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":var",$item);
        $statement->execute();
        return $statement->fetchColumn();
    }

    /**
     * Return all bidding placed by a particular user. Used for
     * user when clicking on Basket button to check their
     * bidding list.
     * @param $user_id
     * @return  Array of type Bid object
     */
    public function getUserBids($user_id)
    {
        $sqlQuery = "SELECT * FROM bids WHERE user_id = :var;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":var",$user_id);
        $statement->execute();
        $dataSet = [];
        while($row = $statement->fetch())
        {
            $dataSet[] = new Bid($row);
            $this->userTotalBids++;
        }
        return $dataSet;
    }

    /**
     * Private method used by place_Bd() method for checking user bidding.
     * @param $user_id
     * @param $item_id
     * @return  boolean
     */
    private function userAlreadyBid($user_id, $item_id)
    {
        $sqlQuery = "SELECT bid_id FROM bids WHERE item_id = :item AND user_id = :user;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":item",$item_id);
        $statement->bindParam(":user",$user_id);
        $statement->execute();
        $row = $statement->fetchColumn();
        if($row > 0){ return true;}
        return false;
    }

    /**
     * Return all details of an item which is in the bidding table.
     * @param $item_id
     * @return  Array
     */
    public function getAllDetailsOfAnItemForBid($item_id)
    {
        $sqlQuery = "SELECT Items.title, Items.description, Items.image, Items.Max_price, Items.Min_price, countries.country, auctions.title as auction_title, auctions.end_date, auctions.end_time,
            COUNT(bids.item_id) as number_of_bids FROM bids INNER JOIN Items ON Items.item_id = bids.item_id INNER JOIN auctions ON auctions.auction_id = Items.auction_id
            INNER JOIN countries ON auctions.country_id = countries.country_id WHERE bids.item_id = :item_id; ";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":item_id",$item_id);
        $statement->execute();
        return $statement->fetch();
    }

    /**
     * Update item amount in the bidding table.
     * @param $item
     * @param $new_amount
     */
    private function updateBidValueForItem($item, $new_amount)
    {
        $sqlQuery = "UPDATE bids SET item_bid_price = :amount WHERE item_id = :item;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":item",$item);
        $statement->bindParam(":amount",$new_amount);
        $statement->execute();
    }

    /**
     * Checks if user has already placed a bid on same item or not.
     * If yes then it runs and update on previous given price. If not
     * then it places new bid into bidding table.
     * @param $user_id
     * @param $item_id
     * @param $user_amount
     * @return  boolean
     */
    public function place_Bd($user_id, $item_id, $user_amount)
    {
        $today_date = date('Y-m-d');
        $user_amount = "£" . (number_format($user_amount, 2)); //Makes user amount with two decimal places.
        if($this->userAlreadyBid($user_id, $item_id))
        {
            $sqlQuery = "UPDATE bids SET user_bid_price = :amount , item_bid_price= :amount, bid_date = '$today_date' WHERE user_id = :user AND item_id = :item;";
        }
        else
        {
            $sqlQuery = "INSERT INTO bids (user_id, item_id, user_bid_price, item_bid_price, bid_date) VALUES (:user,:item,:amount,:amount,'$today_date');";
        }
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":item",$item_id);
        $statement->bindParam(":user",$user_id);
        $statement->bindParam(":amount",$user_amount);
        $statement->execute();
        $this>$this->updateBidValueForItem($item_id,$user_amount);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if(!is_array($row)){return true;}
        return false;
    }

    /**
     * Return maximum prices has been placed so far on a particular item
     * in the bid table.
     * @param $item
     * @return  int
     */
    public function getMaxBidPriceOfItem($item)
    {
        $sqlQuery = "SELECT MAX(item_bid_price) FROM bids WHERE item_id = :var;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":var",$item);
        $statement->execute();
        return $statement->fetchColumn();
    }

    /**
     * Remove a record from bid table using the bid id.
     * @param $bid_id
     * @return  boolean
     */
    public function removeBid($bid_id)
    {
        $sqlQuery = "DELETE FROM bids WHERE bid_id = :bid";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":bid",$bid_id);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if(!is_array($row)){return false;}
        return true;
    }

    public function getUserPriceOnBid($user_id, $item_id)
    {
        $sqlQuery = "SELECT user_bid_price FROM bids WHERE user_id = :Uid AND item_id = :Iid;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":Uid",$user_id);
        $statement->bindParam(":Iid",$item_id);
        $statement->execute();
        return $statement->fetchColumn();
    }

    public function num_of_bids_on_item($item_id)
    {
        $sqlQuery = "SELECT COUNT(bids.item_id) as number_of_bids FROM aee953_auction_system.bids WHERE item_id = :id;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":id",$item_id);
        $statement->execute();
        return $statement->fetchColumn();
    }

    /**
     * Return all bids of particular user by the use of user ID.
     * Used for pushing notification when item is expired to
     * determine if user has won or lost a bid out of all.
     */
    public function get_bid_details_of_a_user($user_id)
    {
        $sqlQuery = "SELECT bids.item_id, bids.user_bid_price, bids.item_bid_price, auctions.end_date, Items.title, bids.notified
                    FROM aee953_auction_system.bids
                    INNER JOIN Items ON Items.item_id = bids.item_id
                    INNER JOIN auctions ON Items.auction_id = auctions.auction_id
                    WHERE user_id = :id;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":id",$user_id);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Update the a bid associated based on user ID and item to make the
     * notification cell "1" which means the user has been notified
     * about this bid and no longer needed to be notified.
     * @param $item_id
     * @param $user_id
     */
    public function update_bid_notification($item_id, $user_id)
    {
        $sqlQuery = "UPDATE bids SET notified = 1 WHERE user_id = :user_id AND item_id = :item_id;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":item_id",$item_id);
        $statement->bindParam(":user_id",$user_id);
        $statement->execute();
    }
}