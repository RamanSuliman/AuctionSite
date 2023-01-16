<?php
require_once("Models/User_actions.php");
$view = new stdClass();
$view->pageTitle = "Access Service";
$user = new User_actions();

if(isset($_GET["log_in"]))
{
    if(!$user->validatePassword($_GET['password']))
    {
        echo "false";
    }
    else
    {
        if($user->isEmailExist($_GET['email']))
        {
            if($user->check_login($_GET['email'], $_GET['password']) === true)
            {
                echo "true";
            }
            else
            {
                $user->invalid_login_report($_GET['email']);
                echo "false";
            }
        }
        else
        {
            echo "false";
        }
    }
}

if(isset($_GET['sign_up']))
{
    if($user->isEmailExist($_GET['email']))
    {
        echo "false";
    }
    else
    {
        if($user->validatePassword($_GET['password']))
        {
            if ($user->addUser($_GET['email'],$_GET['first_name'],$_GET['last_name'],$_GET['password']))
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
}

if(isset($_GET["logout"]))
{
    $user->logout();
    header('Location: login.php?log_btn=1');
}
if(isset($_GET['signup']))
{
    require_once("Views/Forms/sign_up.phtml");
}
if(isset($_GET['log_btn']))
{
    require_once("Views/Forms/login.phtml");
}


