<?php
require_once('../Models/ItemDataSet.php');
$items= new ItemDataSet();

session_start();
//////// AJAX CALLS /////////
if(isset($_GET['liveSearch']) && $_SESSION['URLtoken'] == $_GET['key'])
{
    echo json_encode($items->search_to_get_ItemID($_GET['liveSearch'], 1 , 8));
    exit();
}
if(isset($_GET['changeKey']))
{
    $_SESSION['URLtoken'] = bin2hex(openssl_random_pseudo_bytes(12));
    echo json_encode($_SESSION['URLtoken']);
}
if(!isset($_GET['changeKey']))
{
    header("Location: ../index.php");
}