<?php
require_once('Models/AuctionDataSet.php');
require_once('Models/User_actions.php');
require_once('Models/Bid_functions.php');
require_once('Models/ItemDataSet.php');
$user = new User_actions();
if(!isset($_GET['id']))
{
    require_once('Views/Forms/error_page.phtml');
    exit();
}
$view = new stdClass();
$view->pageTitle = "Bidding";
$bid_access = new Bid_functions();
$item = new ItemDataSet();
$view->ItemImage = $item->getPictureById($_GET['id']);
$view->bid_MaxPrice = $bid_access->getMaxBidPriceOfItem($_GET['id']);
$view->bid_item_auction_details = $bid_access->getAuction_item_bid($_GET['id']);
$view->latest_user_price = $bid_access->user_bid_price($user->getID(), $_GET['id']);

if(isset($_GET['place_bid']))
{
    if($bid_access->checkIfNumber($_GET['bid_amount']))
    {
        $amount = floatval(number_format($_GET['bid_amount'], 2, '.', ''));
        if($bid_access->checkInput($amount, $view->bid_MaxPrice))
        {
            if($bid_access->place_Bid($user->getID(), $_GET['id'], $amount))
            {
                $view->success = 'Bid has been placed!!';
            }
            else
            {
                $view->error = 'Seems like a technical problem occurred, please contact support team.';
            }
        }
        else
        {
            $view->error = 'Your bidding amount must be at least £1 higher than current amount & less than 6k.';
        }
    }
    else
    {
        $view->error = 'Ops, please ensure only numbers used and in correct format!';
    }
}
require_once('Views/single_item_data.phtml');