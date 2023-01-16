<?php
require_once('Models/AuctionDataSet.php');
require_once('Models/User_actions.php');
$user = new User_actions();
$view = new stdClass();
$auction = new AuctionDataSet();
$view->pageTitle = "Auctions";
if(isset($_SESSION['id_auction'])) {unset($_SESSION['id_auction']);}
if(!isset($_POST['filter_auctions']) || !isset($_SESSION['date']) || isset($_POST['all']))
{
    $view->numberOfData = $auction->getNumberOfAuctions();
}
if((isset($_POST['date']) || isset($_SESSION['date'])) && !isset($_POST['all']))
{
    if(isset($_POST['date']))
    {
        $_SESSION['date'] = $_POST['date'];
    }
    $view->numberOfData = $auction->get_filter_return_auctions($_SESSION['date']);
}
require_once('Pagination.php');
if(!isset($_POST['filter_auctions']) && !isset($_SESSION['date']) || isset($_POST['all']))
{
    unset($_SESSION['date']);
    $view->numberOfData = $auction->getNumberOfAuctions();
    $view->result = $auction->howManyToDisplay($page_first_result, $_SESSION['lot_number'],2,"");
}
if(isset($_POST['filter_auctions']) || isset($_SESSION['date']))
{
    if(isset($_SESSION['date']))
    {
        $view->result = $auction->howManyToDisplay($page_first_result, $_SESSION['lot_number'],1,$_SESSION['date']);
    }
}
require_once('Views/view_auctions.phtml');
