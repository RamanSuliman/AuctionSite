<?php
require_once('../Models/User_actions.php');
require_once('../Models/Bid_functions.php');

$user = new User_actions();
$bid_Object = new stdClass();
$bid_access = new Bid_functions();

/*
 * Used to get a user bid information based on user_id argument.
 * It retrieve data from database, if empty result it sends nothing
 * but there are bids for this user then it passes the result to
 * validate_user_bids() function to deal with the rest.
 * This method is used to respond to ajax call that is used for
 * pushing notifications to a user.
 */
if(isset($_GET['user_ID']))
{
    $bid_Object->bids = $bid_access->get_user_bids_details($_GET['user_ID']);
    if(is_array($bid_Object->bids))
    {
        validate_user_bids($bid_Object->bids);
        return;
    }
    echo "";
}

/*
 * Used to get current bid information
 */
if(isset($_GET['item_id']))
{
    $bid_Object->currentMaxPrice = $bid_access->getMaxBidPriceOfItem($_GET['item_id']);
    $bid_Object->user_price = $bid_access->user_bid_price($user->getID(), $_GET['item_id']);
    $bid_Object->num_of_bids = $bid_access->num_of_bids_on_item($_GET['item_id']);
    echo json_encode($bid_Object);
}

/*
 * Placing new bid
 */
if(isset($_GET['bid_amount']))
{
    if($bid_access->checkIfNumber($_GET['bid_amount']))
    {
        if($bid_access->place_Bid($user->getID(), $_GET['item_ID'], $_GET['bid_amount']))
        {
            echo "true";
        }
        else
        {
            echo "false";
        }
    }
    else
    {
        echo "false";
    }
}

if(isset($_GET['rm_bid']))
{
    $bid_access->remove_bid($_GET['rm_bid']);
    echo json_encode("done");
}

/*
 * This function gets user bids details and loops over each lot
 * end_date and compare it with current date to check if expired.
 * If there are expired bids, it append them into an array with other
 * related bid details then turn trigger update action on appened bids
 * to turn the value of "notified" into "1" meaning user has been alerted
 * about this bid and finally send the expired bids array back to client side.
 */
function validate_user_bids($user_bids)
{
    $list_of_outdated_bids = array();
    foreach ($user_bids as $bid)
    {
        if (date("Y-m-d") > $bid['end_date'] && $bid['notified'] == 0)
        {
            array_push($list_of_outdated_bids, $bid);
            $GLOBALS['bid_access']->turn_off_bid_notification($bid['item_id'], $_GET['user_ID']);
        }
    }
    if (is_array($list_of_outdated_bids)) //Checks if there are expiry bids to send otherwise nothing is sent.
    {
        echo json_encode($list_of_outdated_bids);
    }
}