<?php
if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }
if(!$db->isUserLoggedIn()) {
    header("Location: not_authorized");
}
if(!isset($_SESSION['login_state']) && $db->getMFAMethodsFromUser($_SESSION['user_id']) != NULL) {
    header("Location: 2fa");
}

$project_ident = $_GET['ident'];
$project_data = $db->getProjectData($project_ident)

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
</head>
<body>
<script>
    localStorage.setItem("project", decodeURIComponent(`<?=$project_data['project_content']?>`));
    window.location.href = "/ide4school-ce";
</script>
</body>
</html>
