<?php
require_once('Models/Bid_functions.php');
require_once('Models/User_actions.php');
$user = new User_actions();
$view = new stdClass();
$view->pageTitle = "My Basket";

$bid_access = new Bid_functions();

$view->myBids = $bid_access->getUserBids($user->getID());
$view->numberOfBids = $bid_access->getUserTotalBids();


require_once('Views/basket.phtml');