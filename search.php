<?php
require_once('Models/ItemDataSet.php');
require_once('Models/User_actions.php');


$view = new stdClass();
$user = new User_actions();
$view->pageTitle = "LOTs Search";
$items= new ItemDataSet();

if(!isset($_POST['search']) && !isset($_SESSION['pattern']))
{
    require_once('Views/Forms/error_page.phtml');
    exit();
}

//Store search value in session to ensure data aren't lost on refresh time.
if(isset($_POST['search']) && !empty($_POST['pattern']))
{
    if($_POST['key'] != $_SESSION['URLtoken']) { header("Location: index.php"); }
    $_SESSION['pattern'] = $_POST['pattern'];

}

//Get number of records that would be used for pagination.
$view->numberOfData = $items->getNumberOfSearchItems($_SESSION['pattern']);
require_once('Pagination.php');

//Fetch search results 8 results per page.
$view->result  = $items->search_items($_SESSION['pattern'], $page_first_result, 8);

require_once('Views/search.phtml');

