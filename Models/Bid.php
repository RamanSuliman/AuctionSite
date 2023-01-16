<?php


class Bid
{
    private $bidID, $userID, $itemID, $userPrice, $bidPrice, $bidDate;

    public function __construct($dbRow)
    {
        $this->bidID = $dbRow['bid_id'];
        $this->userID = $dbRow['user_id'];
        $this->itemID = $dbRow['item_id'];
        $this->userPrice = $dbRow['user_bid_price'];
        $this->bidPrice = $dbRow['item_bid_price'];
        $this->bidDate = $dbRow['bid_date'];
    }

    public function getBidID()
    {
        return $this->bidID;
    }
    public function getUserID()
    {
        return $this->userID;
    }
    public function getItemID()
    {
        return $this->itemID;
    }
    public function getUserPrice()
    {
        return $this->userPrice;
    }
    public function getBidPrice()
    {
        return $this->bidPrice;
    }
    public function getBidDate()
    {
        return $this->bidDate;
    }
}