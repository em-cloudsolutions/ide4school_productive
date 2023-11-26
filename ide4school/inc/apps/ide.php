<?php
if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }
if(!$db->isUserLoggedIn()) {
    header("Location: not_authorized");
}
if(!isset($_SESSION['login_state']) && $db->getMFAMethodsFromUser($_SESSION['user_id']) != NULL) {
    header("Location: 2fa");
}

$getCurrentUserData = $db->getCurrentUserInformations();


if(isset($_GET['clicksave']))
{
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
</head>
<body>
    <form action="ide" method="POST">
        <input type="hidden" name="project_id">
        <input type="hidden" name="project_name">
        <input type="hidden" name="project_type">
        <input type="hidden" name="project_content">
        <input type="hidden" name="clicksave_action">
    </form>
    <script>
        //Lese Wert von key "project" aus LocalStorage aus
        var project = localStorage.getItem("project");
        var project_id = JSON.parse(project)['identifier'];
        var project_name = JSON.parse(project)['name'];
        var project_type = JSON.parse(project)['project_type'];
        //Setze die Werte in das Formular
        document.getElementsByName("project_id")[0].value = project_id;
        document.getElementsByName("project_name")[0].value = project_name;
        document.getElementsByName("project_type")[0].value = project_type;
        document.getElementsByName("project_content")[0].value = project;
        //Sende das Formular ab
        document.forms[0].submit();
    </script>
</body>
</html>
<?php
}

if(isset($_GET['autosave']))
{
    ?>
    <!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
</head>
<body>
    <script>
        //Lese Wert von key "project" aus LocalStorage aus
        var project = localStorage.getItem("project");
        var project_id = JSON.parse(project)['identifier'];
        var project_name = JSON.parse(project)['name'];
        var project_type = JSON.parse(project)['project_type'];
        
        //Sende am besten per fetch anstatt per form
        fetch('ide', {
            method: 'POST',
            body: JSON.stringify({
                project_id: project_id,
                project_name: project_name,
                project_type: project_type,
                project_content: project,
                autosave_action: true
            })
        });
    </script>
</body>
</html>
<?php
}

//Entnehme das Projekt aus dem POST-Request
if(isset($_POST['clicksave_action']))
{
    $project = json_decode($_POST['project_content'], true);
    $project_id = $project['identifier'];
    $project_content = $project['content'];

    $db->updateProject($project_id, $project_content);
    die();
}


?>


