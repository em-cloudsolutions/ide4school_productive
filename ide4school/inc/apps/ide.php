<?php
if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }
if(!$db->isUserLoggedIn()) {
    header("Location: not_authorized");
}
if(!isset($_SESSION['login_state']) && $db->getMFAMethodsFromUser($_SESSION['user_id']) != NULL) {
    header("Location: 2fa");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json' && isset($_GET['clicksave'])) {
    $project = file_get_contents('php://input');
    $project_decoded = json_decode($project, true);
        $project_id = $project_decoded['identifier'];
        if($project_id == "new") {
            $new_project_identifier = $db->createNewProjectThroughClicksave($project);
            //Gebe sowohl den neuen Identifier als auch ein 200 OK an den xhr request zurück, damit ich diesen dort auswerten kann
            if($new_project_identifier != NULL) {
                header('HTTP/1.1 200 OK');
                //gebe ein json obkjekt mit dem identifier zurück
                $reponse = array("identifier" => $new_project_identifier);
                echo json_encode($reponse);
            }
            else {
                header('HTTP/1.1 400 Bad Request');
            }
        }
        else {
            if($db->updateProjectCode($project_id, $project)) {
                header('HTTP/1.1 200 OK');
            }
            else {
                header('HTTP/1.1 400 Bad Request');
            }
        }
} 
elseif
    ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['CONTENT_TYPE'] == 'application/json' && isset($_GET['autosave'])) {
        $project = file_get_contents('php://input');
        $project_decoded = json_decode($project, true);
    $project_id = $project_decoded['identifier'];
    if($project_id != "new") {
        if($db->updateProjectCode($project_id, $project)) {
            header('HTTP/1.1 200 OK');
        }
        else {
            header('HTTP/1.1 400 Bad Request');
        }
    }          
    }
else {
  // Gib einen Fehler zurück
  header('HTTP/1.1 400 Bad Request');
}

?>