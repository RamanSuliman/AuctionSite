<?php


class Auction
{
    private $auction_id, $title, $start_date, $end_date, $end_time, $image, $country_id;

    public function __construct($dbRow)
    {
        $this->auction_id = $dbRow['auction_id'];
        $this->title = $dbRow['title'];
        $this->start_date = $dbRow['start_date'];
        $this->end_date = $dbRow['end_date'];
        $this->end_time = $dbRow['end_time'];
        $this->image = $dbRow['image'];
        $this->country_id = $dbRow['country_id'];
    }

    public function getAuction_id()
    {
        return $this->auction_id;
    }
    public function getTitle()
    {
        return $this->title;
    }
    public function getStart_date()
    {
        return $this->start_date;
    }
    public function getEnd_date()
    {
        return $this->end_date;
    }
    public function getEnd_time()
    {
        return $this->end_time;
    }
    public function getImage()
    {
        return $this->image;
    }
    public function getCountry_ID()
    {
        return $this->country_id;
    }
}