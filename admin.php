<?php
require_once('Models/User_actions.php');
require_once('Models/Bid_functions.php');
require_once('Models/AuctionDataSet.php');
$user = new User_actions();
$view = new stdClass();
$bid_access = new Bid_functions();
$auction = new AuctionDataSet();
$view->pageTitle = "Admin";
$target_page = 0;
if(!isset($_GET['bids']) && !isset($_GET['auctions']) && !isset($_GET['users']) && !isset($_GET['btn']) && !isset($_SESSION['users_pages']))
{
    require_once('Views/Forms/error_page.phtml');
    exit();
}
if(isset($_GET['btn']))
{
    if($_GET['btn'] == 'user')
    {
        var_dump($_GET['id']);
        $user->remove_user($_GET['id']);
        $target_page = 2;
    }
    if($_GET['btn'] == 'bid')
    {
        $bid_access->remove_bid($_GET['id']);
        $target_page = 1;
    }
}
if(isset($_POST['search']) && !empty($_POST['pattern']))
{
    $_SESSION['pattern'] = $_POST['pattern'];
    $view->result = $user->getUserById($_SESSION['pattern']);
    $view->tableType = 'Current Users';
    $view->table = 4;
    unset($_SESSION['users_pages']);
}
if(isset($_GET['bids']) || $target_page == 1)
{
    $view->result  = $bid_access->fetchAllBids();
    $view->numberOfBids = $bid_access->getUserTotalBids();
    $view->tableType = 'Current Bids';
    $view->table = 1;
    unset($_SESSION['users_pages']);
}
if(isset($_GET['users']) || $target_page == 2 || isset($_SESSION['users_pages']))
{
    if(isset($_GET['users'])){$_SESSION['users_pages'] = $_GET['users'];}
    $view->numberOfData = $user->numberOfUsers();
    require_once('Pagination.php');
    $view->result = $user->getAllUsers($page_first_result, 9);
    $view->tableType = 'Current Users';
    $view->table = 2;
}
if(isset($_GET['auctions']) || $target_page == 3)
{
    $view->result = $auction->fetchAllAuctions();
    $view->tableType = 'Current Auctions';
    $view->table = 3;
    unset($_SESSION['users_pages']);
}

require_once('Views/admin.phtml');
