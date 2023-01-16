<?php
require_once('Models/User_actions.php');
require_once('Models/ItemDataSet.php');
$user = new User_actions();
if(!isset($_GET['id_auction']) && !isset($_SESSION['id_auction']))
{
    require_once('Views/Forms/error_page.phtml');
    exit();
}
$view = new stdClass();
$view->pageTitle = "Paintings LOTs";
$items = new ItemDataSet();
if(isset($_GET['id_auction']))
{
    $_SESSION['id_auction'] = $_GET['id_auction'];
    if(isset($_SESSION['lot_number']))
    {
        unset($_SESSION['lot_number']);
    }
    if(isset($_SESSION['price']))
    {
        unset($_SESSION['price']);
    }
}
$view->numberOfData = $items->getNumberOfItems($_SESSION['id_auction']);
$view->auction = $items->getAuction_BasedOnItem($_SESSION['id_auction']);
$view->auction_expiration = $items->expires_in_days($view->auction[1]);

require_once('Pagination.php');

if(isset($_SESSION['id_auction']))
{
    if(isset($_SESSION['price']) || isset($_SESSION['lot_number']))
    {
        if(isset($_SESSION['price']) && isset($_SESSION['lot_number']))
        {
            $view->result = min_max($items, $page_first_result,$_SESSION['lot_number']);
        }
        if(isset($_SESSION['price']) && !isset($_SESSION['lot_number']))
        {
            $view->result = min_max($items, $page_first_result,$_SESSION['lot_number']);
        }
    }
    if((isset($_SESSION['lot_number']) && !isset($_SESSION['price'])) || (isset($_SESSION['main']) && !isset($_SESSION['lot_number'])))
    {
        $view->result = $items->howManyToDisplay($page_first_result, $_SESSION['lot_number'], $_SESSION['id_auction'],0);
    }
}
require_once('Views/items.phtml');



function min_max($items, $offset, $total_records_per_page)
{
    if(strpos($_SESSION['price'],"ma") === false)
    {
        return $items->howManyToDisplay($offset, $total_records_per_page, $_SESSION['id_auction'],1);
    }
    else
    {
        return $items->howManyToDisplay($offset, $total_records_per_page, $_SESSION['id_auction'],2);
    }
}
