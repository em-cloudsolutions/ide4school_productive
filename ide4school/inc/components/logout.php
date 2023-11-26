<?php if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }

if(!$db->isUserLoggedIn()) {
    header("Location: not_authorized");
}

$benutzername = $db->getLogUser();
$db->logout($benutzername);
$token = $_COOKIE['remember_me'];
$db->deleteUserToken($_SESSION['user_id'], $token);
setcookie("remember_me","",time() - 3600);
unset($_COOKIE['remember_me']);
session_destroy();

if(isset($_GET['agb_not_confirmed'])) {
 header("Location: login&agb_not_confirmed");
}
else {
header("Location: index.php");
}
?>