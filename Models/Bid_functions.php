<?php
require_once('BidsDataSet.php');
require_once('ItemDataSet.php');

class Bid_functions
{
    private $bidDataSet;
    public function __construct()
    {
            $this->bidDataSet = new BidsDataSet();
    }

    public function fetchAllBids()
    {
        return $this->bidDataSet->fetchAllBids();
    }

    public function getNumberOfItemBids($item)
    {
        return $this->bidDataSet->getNumberOfitemBids($item);
    }

    public function getUserBids($user_id)
    {
        return $this->bidDataSet->getUserBids($user_id);
    }

    public function getUserTotalBids()
    {
        $total = $this->bidDataSet->getUserTotalBids();
        if($total == 0)
        {
            return 0;
        }
        return $total;
    }

    /**
     * Uses method of bidDataSet to get maximum price, if its zero
     * it means no bid has been set for and returns zero. Otherwise, the
     * max bid amount.
     * @param $item
     * @return  String
     */
    public function getMaxBidPriceOfItem($item)
    {
        $price = $this->bidDataSet->getMaxBidPriceOfItem($item);
        if(strlen($price) > 0)
        {
            return $price;
        }
        return "£0.00";
    }

    /**
     * Ensures user only provide valid amount which can only be
     * number and a single dot for decimal specifying purposes.
     * @param $amount
     * @return  boolean
     */
    public function checkIfNumber($amount)
    {
        if(preg_match("/^[\d]*\.{0,1}[\d]+$/", $amount) && preg_match("/^[^\.]/", $amount))
        {
            return true;
        }
        return false;
    }

    /**
     * Gets the current bidding price of an item and compares it
     * with the amount given by user. It should be at least one pound
     * higher and must not exceed 6000 pounds.
     * @param $amount
     * @param $current_Amount
     * @return  boolean
     */
    public function checkInput($amount, $current_Amount)
    {
        $current_Amount = substr($current_Amount,2,strlen($current_Amount));
        $current_Amount = floatval(str_replace(',', '', $current_Amount));
        if($amount < ($current_Amount + 1) || $amount >= 6000)
        {
               return false;
        }
        return true;
    }

    public function place_Bid($user_id, $item_id, $amount)
    {
        return $this->bidDataSet->place_Bd($user_id, $item_id, $amount);
    }

    public function remove_bid($bid)
    {
        return $this->bidDataSet->removeBid($bid);
    }

    public function getAuction_item_bid($item_id)
    {
        return $this->bidDataSet->getAllDetailsOfAnItemForBid($item_id);
    }

    public function user_bid_price($user_id, $item_id)
    {
        $price = $this->bidDataSet->getUserPriceOnBid($user_id, $item_id);
        if($price)
        {
            return $price;
        }
        return "£0.00";
    }

    public function num_of_bids_on_item($item_id)
    {
        $bids = $this->bidDataSet->num_of_bids_on_item($item_id);
        if($bids)
        {
            return $bids;
        }
        return "0";
    }

    public function get_user_bids_details($user_id)
    {
        return $this->bidDataSet->get_bid_details_of_a_user($user_id);
    }

    public function turn_off_bid_notification($item_id, $user_id)
    {
        $this->bidDataSet->update_bid_notification($item_id, $user_id);
    }
}