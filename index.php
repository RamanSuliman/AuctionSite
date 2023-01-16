<?php
require_once('Models/User_actions.php');
$view = new stdClass();
$user = new User_actions();
$view->pageTitle = 'Auction System';
require_once('Views/index.phtml');