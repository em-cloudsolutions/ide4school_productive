<?php
if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }

$game_token = $_GET['token'];

if(!$db->isUserLoggedIn()) {
    header("Location: login&game=$game_token");
}
if(!isset($_SESSION['login_state']) && $db->getMFAMethodsFromUser($_SESSION['user_id']) != NULL) {
    header("Location: 2fa&game=$game_token");
}
$game = $db->checkGameIDViaToken($game_token);
$game_status = $db->checkGameStatusViaToken($game_token);
$db->addRequestNumber($game_token);

if ($game == "1") {
    if($game_status == "1") {
        include('core/games/default/css_dinner/index.html');
    }
    else {
        include("inc/components/game_inactive.php");
    }
}

elseif ($game == "2") {
    if($game_status == "1") {
        include('core/games/default/elevator_saga/index.html');
    }
    else {
        include("inc/components/game_inactive.php");
    } 
}

elseif ($game == "3") {
    if($game_status == "1") {
        include('core/games/default/flexbox-froggy/index.html');
    }
    else {
        include("inc/components/game_inactive.php");
    } 
}

elseif ($game == "4") {
    if($game_status == "1") {
        include('core/games/default/sql_mysteries/index.html');
    }
    else {
        include("inc/components/game_inactive.php");
    } 
}

elseif ($game == "5") {
    if($game_status == "1") {
        include('core/games/default/the_aviator/index.html');
    }
    else {
        include("inc/components/game_inactive.php");
    } 
}

elseif ($game == "6") {
    if($game_status == "1") {
        include('core/games/default/gridgarden/index.html');
    }
    else {
        include("inc/components/game_inactive.php");
    } 
}

elseif ($game == "7") {
    if($game_status == "1") {
        include('core/games/default/bitburner/index.html');
    }
    else {
        include("inc/components/game_inactive.php");
    } 
}





else {
    include("inc/components/game_not_found.php");
}


?>