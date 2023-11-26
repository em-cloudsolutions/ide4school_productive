<?php
	session_start();

    error_reporting(E_ALL);

    define('IN_SITE', true);
	
    if(isset($_GET["page"]))
    {
        $page = $_GET["page"];
    }
    else
    {
        $page = "login";
    }
	
    require_once 'core/internal/db_actions.php';
	$db = new DB();
    $timezone = 'Europe/Berlin';
    date_default_timezone_set($timezone);

    define( 'ROOTPATH', dirname(__FILE__) . '/' );
    include("router.php");
    
    
?>
    
