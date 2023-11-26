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
    echo' <noscript><h2 style="color: red; font-weight: 400; text-align: center;">Warnung! <br />Diese Seite funktioniert nur mit Javascript!<br />Bitte stelle sicher, das Javascript in deinem Browser aktiviert ist, <br />um die gewünschte Seite anzeigen zu können!</h1></noscript>';
    echo "<!-- Programmierung & Entwicklung: em CLOUDsolutions - www.em-cloud-solutions.de-->";
    
?>
    
