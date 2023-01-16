<?php
require_once('../../Models/User_actions.php');
$user = new User_actions();
if(isset($_GET['lat']))
{
    $user->add_location($_GET['lat'], $_GET['lon']);
    echo "true";
}
//$user->add_location(1412412,343443);