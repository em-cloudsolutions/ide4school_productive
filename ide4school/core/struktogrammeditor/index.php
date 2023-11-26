<?php
if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }
if(!$db->isUserLoggedIn()) {
    header("Location: not_authorized");
}
if($db->getCurrentUserInformations()['role'] == "Schüler") {
    if($db->isListenModeOn()) {
        header("Location: focus_lobby&return_to=dashboard");
    }
}
?>
<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <title>ide4school Struktogrammeditor</title>
    <link rel="apple-touch-icon" sizes="180x180" href="core/struktogrammeditor/apple-touch-icon.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="core/struktogrammeditor/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="core/struktogrammeditor/favicon-16x16.png" />
    <link rel="manifest" href="core/struktogrammeditor/site.webmanifest" />
    <link rel="mask-icon" href="core/struktogrammeditor/safari-pinned-tab.svg" color="#264040" />
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
    <meta name="msapplication-TileColor" content="#2d89ef">
    <meta name="theme-color" content="#ffffff">
    <script defer="defer" src="core/struktogrammeditor/struktogramm.js"></script>
    <link href="core/struktogrammeditor/struktogramm.css" rel="stylesheet">
</head>

<body></body>

</html>