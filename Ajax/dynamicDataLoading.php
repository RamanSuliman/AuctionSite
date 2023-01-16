<?php
require_once('../Models/ItemDataSet.php');
$items= new ItemDataSet();

if(isset($_GET['id']) && $_GET['pr'] == 'none')
{
    echo json_encode($items->getItem_auction_detials_forView($_GET['id'] , $_GET['start'] , $_GET['limit']));
}
else
{
    $max_min = 0;
    if(substr($_GET['pr'], 0, 2) == "ma")
    {
        $max_min = 2;
    }
    elseif (substr($_GET['pr'], 0, 2) == "mi")
    {
        $max_min = 1;
    }
    echo json_encode($items->howManyToDisplay($_GET['start'] , $_GET['limit'], $_GET['id'], $max_min));
}