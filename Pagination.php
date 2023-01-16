<?php
if(isset($_SESSION['id_auction']))
{
    if(isset($_POST['lot_number']))
    {
        $_SESSION['lot_number'] = $_POST['lot_number'];
    }
    if(!isset($_SESSION['lot_number']))
    {
        $_SESSION['main'] = true;
        $_SESSION['lot_number'] = 8;
    }
    if(isset($_POST['price']))
    {
        $_SESSION['price'] = $_POST['price'];
    }
}
if(!isset($_POST['filter_auctions']) && !isset($_SESSION['id_auction'])){$_SESSION['lot_number'] = 8;}
//////// Pagination /////////
$view->totalNumber_of_page = ceil ($view->numberOfData / $_SESSION['lot_number']);
if (!isset ($_GET['page']) ) {
    $view->pageNumber  = 1;
} else {
    $view->pageNumber = $_GET['page'];
}
$view->nextPage = $view->pageNumber + 1;
$view->prevPage = $view->pageNumber - 1;
$view->adjacents = "2";
$view->second_last = $view->totalNumber_of_page - 1; // total page minus 1
$page_first_result = ($view->pageNumber - 1) * $_SESSION['lot_number'];

