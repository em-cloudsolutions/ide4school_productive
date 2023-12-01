<?php
if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }
if(!$db->isUserLoggedIn()) {
    header("Location: not_authorized");
}
if(!isset($_SESSION['login_state']) && $db->getMFAMethodsFromUser($_SESSION['user_id']) != NULL) {
    header("Location: 2fa");
}
if($db->getCurrentUserInformations()['role'] == "Schüler") {
    if($db->isListenModeOn()) {
        header("Location: focus_lobby&return_to=project&id=".$_GET['id']);
    }
}

    $classes = $db->getAllClasses();
    $getCurrentUserData = $db->getCurrentUserInformations();

// SESSION CLASS MANAGMENT - BEGIN
$teacher_classes = $db->getAllowedClassesForTeachers();


$db->checkAssignmentRights();
if(isset($_POST['updateSessionClass'])) {
    $new_class = $_POST['class_name'];
    $_SESSION['currentSessionClass'] = $new_class;
    header("Location: submission&id=$submission_id");
}
// SESSION CLASS MANAGMENT - END

if(isset($_GET['id'])) {
    $project_id = $_GET['id'];
}
else {
    header("Location: projects");
}

$project_data = $db->getProjectData($project_id);

//Nehme den Wert aus project_content und decodiere ihn
//dekodiere den json_string
//wenn owner != user_id setze den wert von in_review (aus dem json string) auf true

$decoded_project_content = json_decode(urldecode($project_data['project_content']), true);
if($project_data['owner'] != $_SESSION['user_id']) {
    $decoded_project_content['in_review'] = true;
    $project_data['project_content'] = urlencode(json_encode($decoded_project_content));
}

if($project_data == NULL) {
    header("Location: projects");
}

if($getCurrentUserData['role'] == "Schüler" && $project_data['shared'] != "1" && $project_data['owner'] != $_SESSION['user_id']) {
    header("Location: not_authorized");
}

if(isset($_POST['markascompleted'])) {
    $db->markProjectAsCompleted($project_id);
    header("Location: project&id=$project_id");
}
if(isset($_POST['markasuncompleted'])) {
    $db->markProjectAsUncompleted($project_id);
    header("Location: project&id=$project_id");
}

if(isset($_POST['publishProject'])) {
    $db->publishProject($_POST['project_publish_id']);
    header("Location: project&id=".$_POST['project_publish_id']);
}

if(isset($_POST['changeVisibility'])) {
    $db->changeProjectVisibility($_POST['project_visibility_id']);
    header("Location: project&id=".$_POST['project_visibility_id']);
}

if(isset($_POST['deleteProject'])) {
    $db->deleteProject($project_id);
    header("Location: projects");
}

if(isset($_POST['returnProject'])) {
    $db->returnProject($_POST['return_grade'], $_POST['return_note'], $project_id);
    header("Location: project&id=$project_id");
}

if(isset($_POST['editDesc'])) {
    $db->editProjectDescription($_POST['project_edit_desc_id'], $_POST['project_edit_desc_new']);
    header("Location: project&id=$project_id");
}

if(isset($_POST['editName'])) {
    $db->editProjectName($_POST['project_edit_name_id'], $_POST['project_edit_name_new']);
    header("Location: project&id=$project_id");
}

if(isset($_POST['markascompleted'])) {
    $db->markProjectAsCompleted($_POST['markascompleted_id']);
    header("Location: project&id=$project_id");
}

if(isset($_POST['markasuncompleted'])) {
    $db->markProjectAsUncompleted($_POST['markasuncompleted_id']);
    header("Location: project&id=$project_id");
}






?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="author" content="em CLOUDsolutions">
    <title><?=$project_data['name']?> - Projektdetails - ide4school</title>
    <link rel="apple-touch-icon" href="app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/forms/select/select2.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/animate/animate.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/extensions/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/tables/datatable/rowGroup.bootstrap5.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/themes/bordered-layout.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/themes/semi-dark-layout.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/plugins/forms/form-validation.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/plugins/extensions/ext-component-sweet-alerts.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <!-- END: Custom CSS-->

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="">

<script>
    function updateSessionClass(class_name){
        document.getElementById("class_name").value = class_name;
        document.getElementById("updateSessionClassForm").submit();
    }

    <?php
if($project_data['owner'] == $_SESSION['user_id'] && $project_data['submitted'] == "0" || $project_data['owner'] == $_SESSION['user_id'] && $project_data['submitted'] == "1" && $project_data['reviewed'] == "1") { ?>

    function deleteProject(project_id){
        if(confirm("Projekt wirklich löschen? Alle Daten werden unwiderruflich gelöscht!")) {
        document.getElementById("project_delete_id").value = project_id;
        document.getElementById("deleteProjectForm").submit();
        }
    }

    function publishProject(project_id){
        if(confirm("Projekt wirklich zur Bewertung einreichen? Diese Aktion kann nicht rückgängig gemacht werden!")) {
            document.getElementById("project_publish_id").value = project_id;
            document.getElementById("publishProjectForm").submit();
        }
    }
<?php
}
?>

    function changeVisibility(project_id){
        document.getElementById("project_visibility_id").value = project_id;
        document.getElementById("changeVisibilityForm").submit();
    }

<?php
if($project_data['owner'] == $_SESSION['user_id'] && $project_data['submitted'] == "0" || $project_data['owner'] == $_SESSION['user_id'] && $project_data['submitted'] == "1" && $project_data['reviewed'] == "1") { ?>

    function editDescription(project_id) {
        if(confirm("Projektbeschreibung ändern?")) {
            new_description = prompt("Neue Projektbeschreibung eingeben:");
            document.getElementById("project_edit_desc_id").value = project_id;
            document.getElementById("project_edit_desc_new").value = new_description;
            document.getElementById("editDescForm").submit();
        }
    }

    function editName(project_id) {
        if(confirm("Projektnamen ändern?")) {
            new_name = prompt("Neuen Projektnamen eingeben:");
            document.getElementById("project_edit_name_id").value = project_id;
            document.getElementById("project_edit_name_new").value = new_name;
            document.getElementById("editNameForm").submit();
        }
    }

    function markProjectAsCompletetd(project_id) {
        if(confirm('Projekt als "Abgeschlossen" markieren?')) {
            document.getElementById("markascompleted").submit();
        }
    }

    function markProjectAsUncompletetd(project_id) {
        if(confirm('Projekt als "In Bearbeitung" markieren?')) {
            document.getElementById("markasuncompleted").submit();
        }
    }

<?php
}
?>

</script>

<?php
if($getCurrentUserData['role'] == "Schüler" && $project_data['submitted'] == "1" && $project_data['reviewed'] == "0") {
    //DO NOTHING
}
else {
?>
<script>
        function openEditor() {
            localStorage.setItem("project", decodeURIComponent(`<?=$project_data['project_content']?>`));
            window.location.href = "/ide4school-ce";
        }
    </script>

<?php
}
?>




<!-- Session class update hidden form -->
<form action="project&id=<?=$project_id?>" method="POST" id="updateSessionClassForm">
            <input name="class_name" type="text" hidden id="class_name">
            <input name="updateSessionClass" type="text" hidden id="updateSessionClass">
        </form>

        <form action="project&id=<?=$project_id?>" method="POST" id="deleteProjectForm">
            <input name="project_delete_id" type="text" hidden id="project_delete_id">
            <input name="deleteProject" type="text" hidden id="deleteProject">
        </form>

        <form action="project&id=<?=$project_id?>" method="POST" id="changeVisibilityForm">
            <input name="project_visibility_id" type="text" hidden id="project_visibility_id">
            <input name="changeVisibility" type="text" hidden id="changeVisibility">
        </form>

        <form action="project&id=<?=$project_id?>" method="POST" id="publishProjectForm">
            <input name="project_publish_id" type="text" hidden id="project_publish_id">
            <input name="publishProject" type="text" hidden id="publishProject">
        </form>

        <form action="project&id=<?=$project_id?>" method="POST" id="editDescForm">
            <input name="project_edit_desc_id" type="text" hidden id="project_edit_desc_id">
            <input name="project_edit_desc_new" type="text" hidden id="project_edit_desc_new">
            <input name="editDesc" type="text" hidden id="editDesc">
        </form>

        <form action="project&id=<?=$project_id?>" method="POST" id="editNameForm">
            <input name="project_edit_name_id" type="text" hidden id="project_edit_name_id">
            <input name="project_edit_name_new" type="text" hidden id="project_edit_name_new">
            <input name="editName" type="text" hidden id="editName">
        </form>

        <form action="project&id=<?=$project_id?>" method="POST" id="markascompleted">
            <input name="markascompleted" type="text" hidden id="markascompleted">
            <input name="markascompleted_id" type="text" hidden id="markascompleted_id">
        </form>

        <form action="project&id=<?=$project_id?>" method="POST" id="markasuncompleted">
            <input name="markasuncompleted" type="text" hidden id="markasuncompleted">
            <input name="markasuncompleted_id" type="text" hidden id="markasuncompleted_id">
        </form>



<?php include('inc/components/header.php'); ?>


 <!-- BEGIN: Main Menu-->
 <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item me-auto"><a class="navbar-brand" href="dashboard"><span class="brand-logo">
                            <svg viewbox="0 0 139 95" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="24">
                                <defs>
                                    <lineargradient id="linearGradient-1" x1="100%" y1="10.5120544%" x2="50%" y2="89.4879456%">
                                        <stop stop-color="#000000" offset="0%"></stop>
                                        <stop stop-color="#FFFFFF" offset="100%"></stop>
                                    </lineargradient>
                                    <lineargradient id="linearGradient-2" x1="64.0437835%" y1="46.3276743%" x2="37.373316%" y2="100%">
                                        <stop stop-color="#EEEEEE" stop-opacity="0" offset="0%"></stop>
                                        <stop stop-color="#FFFFFF" offset="100%"></stop>
                                    </lineargradient>
                                </defs>
                                <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g id="Artboard" transform="translate(-400.000000, -178.000000)">
                                        <g id="Group" transform="translate(400.000000, 178.000000)">
                                            <path class="text-primary" id="Path" d="M-5.68434189e-14,2.84217094e-14 L39.1816085,2.84217094e-14 L69.3453773,32.2519224 L101.428699,2.84217094e-14 L138.784583,2.84217094e-14 L138.784199,29.8015838 C137.958931,37.3510206 135.784352,42.5567762 132.260463,45.4188507 C128.736573,48.2809251 112.33867,64.5239941 83.0667527,94.1480575 L56.2750821,94.1480575 L6.71554594,44.4188507 C2.46876683,39.9813776 0.345377275,35.1089553 0.345377275,29.8015838 C0.345377275,24.4942122 0.230251516,14.560351 -5.68434189e-14,2.84217094e-14 Z" style="fill:currentColor"></path>
                                            <path id="Path1" d="M69.3453773,32.2519224 L101.428699,1.42108547e-14 L138.784583,1.42108547e-14 L138.784199,29.8015838 C137.958931,37.3510206 135.784352,42.5567762 132.260463,45.4188507 C128.736573,48.2809251 112.33867,64.5239941 83.0667527,94.1480575 L56.2750821,94.1480575 L32.8435758,70.5039241 L69.3453773,32.2519224 Z" fill="url(#linearGradient-1)" opacity="0.2"></path>
                                            <polygon id="Path-2" fill="#000000" opacity="0.049999997" points="69.3922914 32.4202615 32.8435758 70.5039241 54.0490008 16.1851325"></polygon>
                                            <polygon id="Path-21" fill="#000000" opacity="0.099999994" points="69.3922914 32.4202615 32.8435758 70.5039241 58.3683556 20.7402338"></polygon>
                                            <polygon id="Path-3" fill="url(#linearGradient-2)" opacity="0.099999994" points="101.428699 0 83.0667527 94.1480575 130.378721 47.0740288"></polygon>
                                        </g>
                                    </g>
                                </g>
                            </svg></span>
                        <h2 class="brand-text">ide4school</h2>
                    </a></li>
                
            </ul>
        </div>
        <?php
        if($getCurrentUserData['role'] != "Schüler") {
            ?>
        <br />
        <div class="class_selector" style="margin-left: 5%; margin-right: 5%;">
                                        <label class="form-label" for="basicSelect">Ausgewählte Klasse</label>
                                    <form>
                                        <select name="session_class_selector" class="form-select" id="basicSelect" onChange="updateSessionClass(this.form.session_class_selector.options[this.form.session_class_selector.selectedIndex].value)"> 
                                        <?php
                                        echo '<option>' . $_SESSION['currentSessionClass'] . '</option>';
                                        echo '<option>---</option>';
                                        foreach ($teacher_classes as $class) {
                                            echo '<option value="' . $class['class'] . '">' . $class['class'] . '</option>';
                                        }
                                        ?>
                                        </select>
                                    </form>
                                    </div>
        <?php
        }
        ?>
        <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
               
                <li class="nav-item"><a class="d-flex align-items-center" href="dashboard"><i data-feather="home"></i><span class="menu-title text-truncate" data-i18n="Dashboard">Dashboard</span></a>
                </li>
                <li class=" navigation-header"><span data-i18n="Apps">Apps</span><i data-feather="more-horizontal"></i>
                </li>
                <li class="nav-item"><a data-bs-toggle="modal" data-bs-target="#createEnviromentModal" class="d-flex align-items-center"><i data-feather="edit-3"></i><span class="menu-title text-truncate" data-i18n="Development Enviroment">Programmieren</span></a>
                </li>
                <li class="nav-item active"><a class="d-flex align-items-center" href="projects"><i data-feather="layers"></i><span class="menu-title text-truncate" data-i18n="Projects">Projekte</span></a>
                </li>
                <li class="nav-item"><a class="d-flex align-items-center" target="_blank" href="struktogrammeditor"><i data-feather="layout"></i><span class="menu-title text-truncate" data-i18n="Struktogrammeditor">Struktogrammeditor</span></a>
                    </li>
                <?php
                
                if($db->isEmailFunktionEnabled()) {
                    echo '<li class=" nav-item"><a class="d-flex align-items-center" href="email"><i data-feather="mail"></i><span class="menu-title text-truncate" data-i18n="Direktnachrichten">Direktnachrichten</span></a>
                    </li>';
                }

                if($db->isMessageFunktionEnabled()) {
                    echo'<li class=" nav-item"><a class="d-flex align-items-center" href="messages"><i data-feather="message-square"></i><span class="menu-title text-truncate" data-i18n="Messages">Mitteilungen</span></a>
                    </li>';
                }

                if($db->isTodoFunktionEnabled()) {
                    echo '<li class=" nav-item"><a class="d-flex align-items-center" href="todo"><i data-feather="check-square"></i><span class="menu-title text-truncate" data-i18n="Todo">Todo</span></a>
                    </li>';
                }

                //if($db->isSubmissionFunktionEnabled()) {
                 //   echo '<li class=" nav-item"><a class="d-flex align-items-center" href="submissions"><i data-feather="inbox"></i><span class="menu-title text-truncate" data-i18n="Submissions">Abgaben</span></a>
                  //  </li>';
                //}
                
                if($db->isGameFunktionEnabled()) {
                    ?>
                    <li class=" nav-item"><a class="d-flex align-items-center" href="#"><i data-feather="play-circle"></i><span class="menu-title text-truncate" data-i18n="Lernspiele">Lernspiele</span></a>
                    <ul class="menu-content ">
                        <li><a class="d-flex align-items-center" href="games"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Spieleübersicht">Spieleübersicht</span></a>
                        </li>
                        <li><a class="d-flex align-items-center" href="game_manager"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Session Manager">Session Manager</span></a>
                        </li>
                    </ul>
                </li>
                
<?php  
                }
                
?>
                <!-- <li class=" nav-item"><a class="d-flex align-items-center" href="#"><i data-feather='check-circle'></i></i><span class="menu-title text-truncate" data-i18n="Exam">Prüfungen</span></a>
                    <ul class="menu-content ">
                        <li><a class="d-flex align-items-center" <?php //if($getCurrentUserData['role'] == "Schüler") { echo ' href="exams"'; } else { echo 'href="exams"'; } ?>><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Exam timeline"><?php if($getCurrentUserData['role'] == "Schüler") { echo 'Geschriebene Prüfungen'; } else { echo 'Prüfungsübersicht'; }?></span></a>
                        </li>
                        <?php //if($getCurrentUserData['role'] != "Schüler") { echo '<li><a class="d-flex align-items-center" href="exam&state=createNewExam" ><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Class folder">Klassenordner</span></a></li>'; }
                        ?>
                        <?php
                        if($getCurrentUserData['role'] != "Schüler") {
                            ?>
                            <li><a class="d-flex align-items-center" href="fileshare"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Dateifreigabe">Dateifreigabe</span></a>
                            </li>
                        <?php
                        }
                        if($db->isSubmissionFunktionEnabled() && $getCurrentUserData['role'] != "Schüler") {
                            ?>
                            <li><a class="d-flex align-items-center" href="disk&drive=ad-submissions"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Submissions">Abgabeordner</span></a>
                            </li>
                        <?php
                        }
                        ?>
                    </ul>
                </li>
                !-->
                <?php
                        if($db->noStudent()) {
                           ?>
                <li class=" navigation-header"><span data-i18n="Management">Verwaltung</span><i data-feather="more-horizontal"></i>
                
                <li class=" nav-item"><a class="d-flex align-items-center" href="users"><i data-feather="user"></i><span class="menu-title text-truncate" data-i18n="Users">Benutzer</span></a>
                </li>
                <li class=" nav-item"><a class="d-flex align-items-center" href="classes"><i data-feather="users"></i><span class="menu-title text-truncate" data-i18n="Classes">Klassen</span></a>
                </li>
                <?php
               if($db->isAssignmentFunktionEnabled() && $db->isAdmin()) {
                    echo '<li class=" nav-item"><a class="d-flex align-items-center" href="assignments"><i data-feather="user-plus"></i><span class="menu-title text-truncate" data-i18n="Assignments">Zuordnungen</span></a>
                    </li>';
                }
                ?>
                <?php
                        }

                if($db->isAdmin()) {
                           ?>
         
         <li class=" nav-item"><a class="d-flex align-items-center" href="housekeeping"><i data-feather="wind"></i><span class="menu-title text-truncate" data-i18n="Housekeeping">Housekeeping</span></a>
         </li>
         <?php
                        }
                        ?>

                
                <?php
                        if($db->isAdmin()) {
                           ?>
                <li class=" navigation-header"><span data-i18n="Settings">Einstellungen</span><i data-feather="more-horizontal"></i>

                <li class=" nav-item"><a class="d-flex align-items-center" href="logs"><i data-feather="server"></i><span class="menu-title text-truncate" data-i18n="Logs">Logs</span></a>
         </li>
                <li class=" nav-item"><a class="d-flex align-items-center" href="settings"><i data-feather="settings"></i><span class="menu-title text-truncate" data-i18n="Settings">Einstellungen</span></a>
                </li>
<?php
                        }
                        ?>
                
               

            </ul>
        </div>
    </div>
    <!-- END: Main Menu-->
    <?php
                                        $project_id = json_decode(urldecode($project_data['project_content']), true)['identifier'];
                                        $project_name = json_decode(urldecode($project_data['project_content']), true)['name'];
                                        ?>
    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <section class="app-user-view-account">
                    <div class="row">
                        <!-- User Sidebar -->
                        <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                            <!-- User Card -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="user-avatar-section">
                                        <div class="d-flex align-items-center flex-column">
                                            <img class="img-fluid rounded mt-3 mb-2" src="app-assets/images/illustration/api.svg" height="450px" width="450px" alt="User avatar" />
                                            <div class="user-info text-center">
                                                <h4><?=$project_name?></h4>
                                                <br />
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <h4 class="fw-bolder border-bottom pb-50 mb-1">Projektdetails</h4>
                                    <div class="info-container">
                                        <ul class="list-unstyled">
                                        

                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Name:</span>
                                                <span><?=$project_name?></span>
                                                <span style="cursor: pointer;" OnClick="editName('<?=$project_id?>')"><?php if($project_data['owner'] == $_SESSION['user_id'] && $project_data['submitted'] == "0" || $project_data['owner'] == $_SESSION['user_id'] && $project_data['submitted'] == "1" && $project_data['reviewed'] == "1") { ?><i data-feather="edit"></i><?php } ?></span>
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Beschreibung:</span>
                                                <span><?=$project_data['description']?></span>
                                                <span style="cursor: pointer;" OnClick="editDescription('<?=$project_id?>')"><?php if($project_data['owner'] == $_SESSION['user_id'] && $project_data['submitted'] == "0" || $project_data['owner'] == $_SESSION['user_id'] && $project_data['submitted'] == "1" && $project_data['reviewed'] == "1") { ?><i data-feather="edit"></i><?php } ?></span>
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Erstellt am:</span>
                                                <span><?=$project_data['created_at']?></span>
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Besitzer:</span>
                                                <?php
                                                    $owner = $project_data['owner'];
                                                    $owner_name = $db->getFullnameViaID($owner);
                                                ?>
                                                <span><?=$owner_name?></span>
                                            </li>
                                           
                                            
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Sichtbarkeit:</span>
                                                <?php
                                                if($project_data['shared'] == "1") {
                                                    echo '<span class="badge bg-light-warning">Geteilt</span>';
                                                } else {
                                                    echo '<span class="badge bg-light-danger">Privat</span>';
                                                }
                                                ?>
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Status:</span>
                                                <?php
                                                if($project_data['completed'] == "1") {
                                                    echo '<span class="badge bg-light-success">Abgeschlossen</span>';
                                                } else {
                                                    echo '<span class="badge bg-light-warning">In Bearbeitung</span>';
                                                }
                                                ?>
                                            </li>
                                            <?php
                                            if($getCurrentUserData['role'] == "Schüler" && $project_data['owner'] == $_SESSION['user_id'] && $project_data['submitted'] == "0") {
                                                ?>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Zur Bewertung abgegeben:</span>
                                                <?php
                                                if($project_data['submitted'] == "1") {
                                                    echo '<span class="badge bg-light-success">Ja</span>';
                                                } else {
                                                    echo '<span class="badge bg-light-danger">Nein</span>';
                                                }
                                                ?>
                                            </li>
                                            <?php
                                            }
                                        ?>
                                            
                                            
    
                                        </ul>
                                        <?php
                                            if($project_data['completed'] == "0" && $project_data['owner'] == $_SESSION['user_id']) {
                                                ?>
                                                <button type="button" OnClick="markProjectAsCompletetd(<?=$project_id?>)" class="btn btn-primary me-1">
                                                Projekt als "Abgeschlossen" markieren
                                        </button><br /><br />
                                                <?php
                                            } elseif($project_data['completed'] == "1" && $project_data['owner'] == $_SESSION['user_id']) {
                                                ?>
                                                <button type="button" OnClick="markProjectAsUncompletetd(<?=$project_id?>)" class="btn btn-primary me-1">
                                                Projekt als "In Bearbeitung" markieren
                                        </button><br /><br />
                                                <?php
                                            }
                                            ?>
                                        <?php
                                            if($project_data['owner'] == $_SESSION['user_id']  && $project_data['submitted'] == "0") {
                                                ?>
                                                
                                            
                                                <button type="button" OnClick="changeVisibility(<?=$project_id?>)" class="btn btn-primary me-1">
                                                Sichtbarkeit ändern
                                        </button>
                                        <?php
                                        if($getCurrentUserData['role'] == "Schüler") {
                                            ?>
                                        <button type="button" OnClick="publishProject(<?=$project_id?>)"  class="btn btn-primary me-1">
                                                Projekt abgeben
                                        </button>
                                        <?php
                                            }
                                        ?><br /><br />
                                        
                                            <a OnClick="deleteProject(`<?=$project_data['id']?>`)" class="btn btn-outline-danger suspend-user">Löschen</a>
                                        
                                                <?php
                                            }
                                            ?>
                                        <?php
                                            if($project_data['submitted'] == "1") {
                                                ?>
                                        <br /><?php
                                        if($getCurrentUserData['role'] == "Lehrer" OR $getCurrentUserData['role'] == "Administrator") {
                                        ?>
                                        <form action="project&id=<?=$project_id?>" method="POST">
                                        <?php
                                        if($project_data['submitted'] == "1" && $project_data['reviewed'] == "0") {
                                            ?>
                                        <h4 class="fw-bolder border-bottom pb-50 mb-1">Kommentare & Benotung</h4>
                                    <div class="info-container">
                                    <p class="card-text">Fügen Sie Kommentare & Verbesserungsvorschläge ein, die dem Schüler / der Schülerin helfen könnten, in Zukunft bessere Ergebnisse zu erzielen.</p>
                                    <div class="row">
                                        <div class="col-12">
                                        <div class="mb-1">
                                                <label class="form-label" for="basicInput">Note / Punkte</label>
                                                <input type="text" class="form-control" name="return_grade" id="basicInput" placeholder="Note / Punkte hier eingeben...">
                                            </div>
                                            <div class="mb-1">
                                                <label class="form-label" for="exampleFormControlTextarea1">Kommentare & Verbesserungen</label>
                                                <textarea class="form-control" name="return_note" id="exampleFormControlTextarea1" rows="3" placeholder="Schreiben Sie etwas..."></textarea>
                                            </div>
                                        </div>

                           
                                    </div>
                                    
                                        <div class="d-flex justify-content-center pt-2">
                                            <button type="submit" name="returnProject" value="returnProject" class="btn btn-primary me-1">
                                                Zurückgeben
                                        </button>
                                        
                                       
                                        
                                        </form>
                                        </div>
                                    </div>
                                    <?php
                                        
                                        }
                                        if($project_data['reviewed'] == "1") {
                                            ?>
                                         <h4 class="fw-bolder border-bottom pb-50 mb-1">Kommentare & Benotung</h4>
                                    <div class="info-container">
                                                                    <div class="row">
                                        <div class="col-12">
                                        <ul class="list-unstyled">
                                        <li class="mb-75">
                                                <span class="fw-bolder me-25">Zurückgegeben am:</span>
                                                <?php if($project_data['reviewed'] == "1") {
                                                echo' <span>' . $project_data['return_at'] . '</span>';
                                                }
                                                else {
                                                    echo' <span>Nicht bewertet</span>';
                                                }
                                                ?>
                                                
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Zurückgegeben von:</span>
                                                <?php if($project_data['reviewed'] == "1") {
                                                echo' <span>'; $id = $project_data['return_from'];
                                                $teacher = $db->getFullNameViaID($id);
                                                echo $teacher; echo '</span>';
                                                }
                                                else {
                                                    echo' <span>Nicht bewertet</span>';
                                                }
                                                ?>
                                                
                                            </li>
                                        <li class="mb-75">
                                                <span class="fw-bolder me-25">Note / Punkte:</span>
                                                <?php if($project_data['reviewed'] == "1") {
                                                echo' <span>' . $project_data['return_grade'] . '</span>';
                                                }
                                                else {
                                                    echo' <span>Noch nicht bewertet</span>';
                                                }
                                                ?>
                                                
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Kommentare / Verbesserungen:</span>
                                                <?php if($project_data['reviewed'] == "1") {
                                                echo' <span>' . $project_data['return_note'] . '</span>';
                                                }
                                                else {
                                                    echo' <span>Noch nicht bewertet</span>';
                                                }
                                                ?>
                                            </li>
                                        </ul>
                                        </div> </div> </div> </div>
                                        <div class="d-flex justify-content-center pt-2">
                                            
                                            <?php
                                            if($project_data['owner'] == $_SESSION['user_id'] && $getCurrentUserData['role'] == "Schüler" && $project_data['submitted'] == "1") {
                                                ?>
                                                <button type="button" OnClick="changeVisibility(<?=$project_id?>)" class="btn btn-primary me-1">
                                                Sichtbarkeit ändern
                                        </button>
                                        <?php if($project_data['submitted'] == "0") {
                                            ?>
                                        <button type="button" OnClick="publishProject(<?=$project_id?>)"  class="btn btn-primary me-1">
                                                Projekt abgeben
                                        </button>
                                        <?php
                                            }
                                            ?>
                                            <a OnClick="deleteProject(`<?=$project_data['project_path']?>`)" class="btn btn-outline-danger suspend-user">Löschen</a>
                                                <?php
                                            }
                                            ?>
                                        
                                        </form>
                                            
                                        <?php
                                        }
                                    }

                                        if($getCurrentUserData['role'] == "Schüler") 
                                        {
                                        ?>
                                        
                                        <h4 class="fw-bolder border-bottom pb-50 mb-1">Kommentare & Benotung</h4>
                                    <div class="info-container">
                                                                        <div class="row">
                                        <div class="col-12">
                                        <ul class="list-unstyled">
                                        <li class="mb-75">
                                                <span class="fw-bolder me-25">Zurückgegeben am:</span>
                                                <?php if($project_data['reviewed'] == "1") {
                                                echo' <span>' . $project_data['return_at'] . '</span>';
                                                }
                                                else {
                                                    echo' <span>Nicht bewertet</span>';
                                                }
                                                ?>
                                                
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Zurückgegeben von:</span>
                                                <?php if($project_data['reviewed'] == "1") {
                                                echo' <span>'; $id = $project_data['return_from'];
                                                $teacher = $db->getFullNameViaID($id);
                                                echo $teacher; echo '</span>';
                                                }
                                                else {
                                                    echo' <span>Nicht bewertet</span>';
                                                }
                                                ?>
                                                
                                            </li>
                                        <li class="mb-75">
                                                <span class="fw-bolder me-25">Note / Punkte:</span>
                                                <?php if($project_data['reviewed'] == "1") {
                                                echo' <span>' . $project_data['return_grade'] . '</span>';
                                                }
                                                else {
                                                    echo' <span>Nicht bewertet</span>';
                                                }
                                                ?>
                                                
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Kommentare / Verbesserungen:</span>
                                                <?php if($project_data['reviewed'] == "1") {
                                                echo' <span>' . $project_data['return_note'] . '</span>';
                                                }
                                                else {
                                                    echo' <span>Nicht bewertet</span>';
                                                }
                                                ?>
                                            </li>
                                        </ul>
                                        <?php
                                            if($project_data['owner'] == $_SESSION['user_id'] && $getCurrentUserData['role'] == "Schüler" && $project_data['submitted'] == "1") {
                                                ?>
                                                <button type="button" OnClick="changeVisibility(<?=$project_id?>)" class="btn btn-primary me-1">
                                                Sichtbarkeit ändern
                                        </button>
                                        <?php if($project_data['submitted'] == "0") {
                                            ?>
                                        <button type="button" OnClick="publishProject(<?=$project_id?>)"  class="btn btn-primary me-1">
                                                Projekt abgeben
                                        </button>
                                        <?php
                                            }
                                        }
                                        if($project_data['owner'] == $_SESSION['user_id'] && $getCurrentUserData['role'] == "Schüler" && $project_data['submitted'] == "0" || $project_data['owner'] == $_SESSION['user_id'] && $getCurrentUserData['role'] == "Schüler" && $project_data['submitted'] == "1" && $project_data['reviewed'] == "1") {
                                            ?>
                                            <a OnClick="deleteProject(`<?=$project_data['id']?>`)" class="btn btn-outline-danger suspend-user">Löschen</a>
                                                <?php
                                            }
                                            ?>


                                        </div>

                            </div>
                                    </div>
                                    
                                    <?php
                                }
                                
                                        }
                                        ?>
                                        
                                    </div>
                                    
                                </div>
                            </div>
                            <!-- /User Card -->
                            
                        </div>
                        <!--/ User Sidebar -->

                          <!-- User Content -->
                          <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-5">
                           
                           
                           <div>
                               
                                       <section class="full-editor">
                                       <div class="row">
                                           <div class="col-12">
                                               <div class="card">
                                                   <div class="card-header">
                                                       <h4 class="card-title">Im Projekt enthaltene Dateien:</h4><button class="btn btn-primary" OnClick="openEditor()" type="button"<?php if($getCurrentUserData['role'] == "Schüler" && $project_data['submitted'] == "1" && $project_data['reviewed'] == "0") { echo 'disabled'; } ?>><i data-feather="edit"></i> Entwicklungsumgebung öffnen</button>
                                                   </div>
                                                   <div class="card-body">
                                                    <?php
                                                    if($getCurrentUserData['role'] == "Schüler" && $project_data['submitted'] == "1" && $project_data['reviewed'] == "0") {
                                                        echo '<div class="alert alert-danger" role="alert">
                                                        <h4 class="alert-heading">Achtung!</h4>
                                                        <p>Das Projekt wurde bereits zur Bewertung abgegeben und kann nicht mehr bearbeitet werden.</p>
                                                        <hr>
                                                        <p class="mb-0">Bitte wenden Sie sich an Ihren Lehrer, wenn Sie das Projekt noch einmal bearbeiten möchten.<br />
                                                        Nach der Bewertung durch den Lehrer wird das Bearbeiten wieder freigegeben.</p>
                                                        </div>';
                                                    }
                                                    if($getCurrentUserData['role'] != "Schüler" && $project_data['submitted'] == "1" && $project_data['reviewed'] == "0") {
                                                        echo '<div class="alert alert-warning" role="alert">
                                                        <h4 class="alert-heading">Achtung!</h4>
                                                        <p>Das Projekt wurde zur Bewertung abgegeben.</p>
                                                        <hr>
                                                        <p class="mb-0">Das nachträgliche Ändern von Projekten durch den Lehrer ist aus Sicherheitsgründen nicht erlaubt. <br />(Speicherfunktion ist deaktiviert)</p>
                                                        </div>';
                                                    }
                                                    ?>
                                                       <hr><br />
                                                       <div class="row">
                                                       
                                                           <pre><code class="language-python"><?php
                                                    $project_content = $project_data['project_content'];
                                                    
                                                    $project_content = json_decode(urldecode($project_content), true);
                                                    $components = $project_content['components'];
                                                    $image_list = $project_content['image_list'];

                                                    foreach($components as $component) {
                                                        echo '<li>' . $component['name'] . '.' . $component['extension'] . '</li>';
                                                    }
                                                ?><br /><br /></code></pre>
                                                       </div>
                                                   </div>
                                               </div>
                                           </div>
                                       </div>
                                   </section>               </div>
                              

                           
                       </div>
                       <!--/ User Content -->
                    </div>
                </section>
                


            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <?php include('inc/components/footer.php'); ?>


    <!-- BEGIN: Vendor JS-->
    <script src="app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="app-assets/vendors/js/forms/select/select2.full.min.js"></script>
    <script src="app-assets/vendors/js/forms/cleave/cleave.min.js"></script>
    <script src="app-assets/vendors/js/forms/cleave/addons/cleave-phone.us.js"></script>
    <script src="app-assets/vendors/js/forms/validation/jquery.validate.min.js"></script>
    <script src="app-assets/vendors/js/extensions/moment.min.js"></script>
    <script src="app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
    <script src="app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js"></script>
    <script src="app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
    <script src="app-assets/vendors/js/tables/datatable/responsive.bootstrap5.js"></script>
    <script src="app-assets/vendors/js/tables/datatable/datatables.buttons.min.js"></script>
    <script src="app-assets/vendors/js/tables/datatable/jszip.min.js"></script>
    <script src="app-assets/vendors/js/tables/datatable/pdfmake.min.js"></script>
    <script src="app-assets/vendors/js/tables/datatable/vfs_fonts.js"></script>
    <script src="app-assets/vendors/js/tables/datatable/buttons.html5.min.js"></script>
    <script src="app-assets/vendors/js/tables/datatable/buttons.print.min.js"></script>
    <script src="app-assets/vendors/js/tables/datatable/dataTables.rowGroup.min.js"></script>
    <script src="app-assets/vendors/js/extensions/sweetalert2.all.min.js"></script>
    <script src="app-assets/vendors/js/extensions/polyfill.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="app-assets/js/core/app-menu.js"></script>
    <script src="app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

   


    <!-- END: Page JS-->
    <?php include('inc/components/createEnviroment.php'); ?>
    <?php
if($project_data['submitted'] == "1" && $project_data['reviewed'] == "0" && $getCurrentUserData['role'] == "Schüler") {
    echo "
    <style>
    #iframe-container.disabled {
  opacity: 0.5; /* Verringere die Deckkraft, um es als deaktiviert anzuzeigen */
  pointer-events: none; /* Verhindere, dass Ereignisse an das Element weitergeleitet werden */
}

    </style>

    <script>
    var iframeContainer = document.getElementById('iframe-container');
    iframeContainer.classList.add('disabled');
    </script>";
}
?>
    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        })
    </script>
</body>
<!-- END: Body-->

</html>