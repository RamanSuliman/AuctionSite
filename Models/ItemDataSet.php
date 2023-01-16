<?php
require_once('Item.php');
require_once('Database.php');

class ItemDataSet
{
    protected $_dbHandle, $_dbInstance;

    // dataset constructor
    public function __construct()
    {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
    }

    /**
     * Used in search area on main menu, it retrieve all data from Item table
     * based on the given pattern to search on. The pattern extracts records
     * if found in item title or description.
     * @param $pattern
     * @param $offset
     * @param $total_records_per_page
     * @return  int
     */
    public function search_items($pattern,$offset, $total_records_per_page)
    {
        $sqlQuery = "SELECT Items.item_id, Items.title, Items.Max_price, Items.description, Items.image, countries.country, auctions.end_date, auctions.end_time
FROM Items INNER JOIN auctions ON auctions.auction_id = Items.auction_id INNER JOIN countries ON auctions.country_id = countries.country_id
WHERE Items.title LIKE :pattern OR Items.description LIKE :pattern LIMIT " . $total_records_per_page . " OFFSET " . $offset;
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute(array(':pattern'=>'%'.$pattern.'%'));
        return $statement->fetchAll();
    }

    /**
     * Used in search area on main menu, it retrieve all data from Item table
     * based on the given pattern to search on. The pattern extracts records
     * if found in item title or description.
     * @param $pattern
     * @param $offset
     * @param $total_records_per_page
     * @return  int
     */
    public function search_to_get_ItemID($pattern,$offset, $total_records_per_page)
    {
        $sqlQuery = "SELECT item_id, title, Items.image FROM Items WHERE title LIKE :pattern ORDER BY item_id LIMIT " . $total_records_per_page . " OFFSET " . $offset;
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute(array(':pattern'=>'%'.$pattern.'%'));
        $objectItems = [];
        while($row = $statement->fetch())
        {
            $objectItems[] = new Item($row, null);
        }
        return $objectItems;
    }

    /**
     * Return number of items that ara associated with an auction ID.
     * @param $auction_id
     * @return  int
     */
    public function getNumberOfItems($auction_id)
    {
        $sqlQuery = "SELECT COUNT(*) FROM Items WHERE auction_id = :var;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":var",$auction_id);
        $statement->execute();
        return $statement->fetchColumn();
    }

    /**
     * Get all columns of specific item ID.
     * @param $item
     * @return  Object Item
     */
    public function getSpecificItem($item)
    {
        $sqlQuery = "SELECT * FROM Items WHERE item_id = :var;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":var",$item);
        $statement->execute();
        return new Item($statement->fetch(PDO::FETCH_ASSOC));
    }

    /**
     * Return the number of records which matches the given pattern.
     * Its used for pagination purposes to support search box facility.
     * @param $pattern
     * @return  String of type User Object
     */
    public function getNumberOfSearchItems($pattern)
    {
        $sqlQuery = "SELECT COUNT(*) FROM Items WHERE title LIKE :pattern OR description LIKE :pattern;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute(array(':pattern'=>'%'.$pattern.'%'));
        return $statement->fetchColumn();
    }

    /**
     * Retrieve item picture based on given unique item ID.
     * @param $item_id
     * @return  String
     */
    public function getPictureById($item_id)
    {
        $sqlQuery = "SELECT image FROM Items WHERE item_id = :id;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":id",$item_id);
        $statement->execute();
        return $statement->fetchColumn();
    }

    /**
     * Return the auction id which an item belongs to. The method
     * require the item id in order to retrieve auction id.
     * @param $item
     * @return  String auction_ID
     */
    public function getAuctionId($item)
    {
        $sqlQuery = "SELECT auction_id FROM Items WHERE item_id = :var;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":var",$item);
        $statement->execute();
        return $statement->fetchColumn();
    }

    /**
     * Get auction details of a partucylar item by providing auction id.
     * @param $item_auctionID
     * @return  Array
     */
    public function getItem_auction_detials_forView($item_auctionID, $offset, $total_records_per_page)
    {
        $sqlQuery = "SELECT Items.item_id, Items.title, Items.Max_price, Items.description, Items.image, countries.country, auctions.end_date, auctions.end_time
FROM Items INNER JOIN auctions ON auctions.auction_id = Items.auction_id INNER JOIN countries ON auctions.country_id = countries.country_id
WHERE Items.auction_id = :auction_id LIMIT " . $total_records_per_page . " OFFSET " . $offset;
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute(array(':auction_id'=> $item_auctionID));
        return $statement->fetchAll();
    }

    public function howManyToDisplay($offset, $total_records_per_page, $id, $filter)
    {
        switch ($filter)
        {
            case 1:
                $filter = "ORDER BY Max_price";
                break;
            case 2:
                $filter = "ORDER BY Max_price DESC";
                break;
            default:
                $filter = '';
        }
        $sqlQuery = "SELECT Items.item_id, Items.title, Items.Max_price, Items.description, Items.image, countries.country, auctions.end_date, auctions.end_time
FROM Items INNER JOIN auctions ON auctions.auction_id = Items.auction_id INNER JOIN countries ON auctions.country_id = countries.country_id
WHERE Items.auction_id = " . $id . " " . $filter . " LIMIT " . $total_records_per_page . " OFFSET " . $offset .";";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Get auction details of a partucylar item by providing auction id.
     * @param $item_auctionID
     * @return  Array
     */
    public function getAuction_BasedOnItem($item_auctionID)
    {
        $sqlQuery = "SELECT countries.country, auctions.end_date, auctions.end_time, auctions.title, auctions.image FROM Items INNER JOIN auctions ON auctions.auction_id = Items.auction_id
                    INNER JOIN countries ON auctions.country_id = countries.country_id
                        WHERE Items.auction_id = :auction_id;";
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->bindParam(":auction_id",$item_auctionID);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_NUM);
    }

    /**
     * This methods returns the number of days left for items of
     * specific auction before it expires, it return 0 if date
     * is in past.
     * @param $expiry_date
     * @return  int
     */
    public function expires_in_days($expiry_date)
    {
        $item_expiry_date = date_create($expiry_date);
        $current_date = date_create(date("Y-m-d"));
        if($item_expiry_date > $current_date)
        {
            $days = date_diff($current_date, $item_expiry_date);
            return $days->format('%a');
        }
        else
        {
            return 0;
        }
    }

}