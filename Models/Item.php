<?php


class Item implements JsonSerializable
{
    private $item_id, $title, $description, $auction_id, $max_price, $min_price, $image;

    public function __construct()
    {
        $GivenArguments = func_get_args();
        $numberOfArguments = func_num_args();

        if (method_exists($this, $function = '__construct'.$numberOfArguments)) {
            call_user_func_array(array($this, $function), $GivenArguments);
        }
    }

    public function __construct1($dbRow)
    {
        $this->item_id = $dbRow['item_id'];
        $this->title = $dbRow['title'];
        $this->description = $dbRow['description'];
        $this->auction_id = $dbRow['auction_id'];
        $this->max_price = $dbRow['Max_price'];
        $this->min_price = $dbRow['Min_price'];
        $this->image = $dbRow['image'];
    }

    public function __construct2($row, $nullParam)
    {
        $this->item_id = $row['item_id'];
        $this->title = $row['title'];
        $this->image = $row['image'];
    }

    public function getItem_id()
    {
        return $this->item_id;
    }
    public function getTitle()
    {
        return $this->title;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function getAuction_id()
    {
        return $this->auction_id;
    }
    public function getMax_Price()
    {
        return $this->max_price;
    }
    public function getImage()
    {
        return $this->image;
    }
    public function getMin_Price()
    {
        return $this->min_price;
    }

    public function jsonSerialize()
    {
        return [
                'itemID' => $this->item_id,
                'itemName' => $this->title,
                'itemImage' => $this->image
               ];
    }
}