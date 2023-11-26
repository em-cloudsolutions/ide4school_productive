<?php
if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }
if(!$db->isUserLoggedIn()) {
    header("Location: not_authorized");
}
if($db->getCurrentUserInformations()['role'] == "Schüler") {
    if($db->isListenModeOn()) {
        header("Location: focus_lobby&return_to=projects");
    }
}
if(!isset($_SESSION['login_state']) && $db->getMFAMethodsFromUser($_SESSION['user_id']) != NULL) {
    header("Location: 2fa");
}




$classes = $db->getAllClasses();
$getCurrentUserData = $db->getCurrentUserInformations();

// SESSION CLASS MANAGMENT - BEGIN
$teacher_classes = $db->getAllowedClassesForTeachers();

$db->checkAssignmentRights();
if(isset($_POST['updateSessionClass'])) {
    $new_class = $_POST['class_name'];
    $_SESSION['currentSessionClass'] = $new_class;
    header("Location: projects");
}
// SESSION CLASS MANAGMENT - END


if(!isset($_GET['state'])) {
    $current_state = "my"; 
}
else {
$current_state = $_GET['state'];
}

if($current_state == "all") {
   $projects = $db->getMyProjects();
}
elseif($current_state == "unreviewed") {
        $projects = $db->getMyUnreviewedProjects();
    }
elseif($current_state == "reviewed") {
        $projects = $db->getMyReviewedProjects();
}
elseif($current_state == "completed") {
   $projects = $db->getMyCompletedProjects();
}
elseif($current_state == "my") {
   $projects = $db->getMyInProgressProjects();
}
elseif($current_state == "shared") {
        $projects = $db->getMySharedProjects();
}
elseif($current_state == "to_review") {
        $projects = $db->getUnreviewedProjects();
}
elseif($current_state == "reviewed_projects") {
    $projects = $db->getReviewedProjects();
}
elseif($current_state == "sharedwithme") {
    $projects = $db->getSharedProjects();
}



$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (isset($data['createProject'])) {
    $project_name = trim($data['project_name']);
    $project_description = trim($data['project_description']);
    $project_visibility = trim($data['project_visibility']);
    $project_category = trim($data['project_category']);


    if($project_category == "website") {
        $project_content = '{"identifier":"blank-html-starter","project_type":"html","locale":"en","name":"Neues Projekt","user_id":null,"components":[{"id":"e732f181-933f-4324-844a-c05cedd9c56c","name":"index","extension":"html","content":""},{"id":"b06d109f-71e4-4227-8bce-fb67a9599381","name":"styles","extension":"css","content":""}],"image_list":[]}';
    }
    else {
        $project_content = '{"project_type":"python","name":"Neues Projekt","locale":null,"components":[{"extension":"py","name":"main","content":"","default":true}],"image_list":[]}';
    }
    

    if($db->createProject($project_name, $project_description, $project_visibility, $project_category, $project_content)) {
                $response = array(
                    'success' => 'true',
                    'redirect' => 'projects'
                );
                echo json_encode($response);
                exit;
    }
    else {
        $response = array(
            'status' => 'error',
            'message' => 'Datenbankfehler!'
        );
        echo json_encode($response);
        exit;
    }
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
    <title>Projekte - <?=$getCurrentUserData['secondName']?>, <?=$getCurrentUserData['firstName']?> - ide4school</title>
    <link rel="apple-touch-icon" href="app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/editors/quill/katex.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/editors/quill/monokai-sublime.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/editors/quill/quill.snow.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/forms/select/select2.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/extensions/dragula.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/extensions/toastr.min.css">
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
    <link rel="stylesheet" type="text/css" href="app-assets/css/plugins/forms/form-quill-editor.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/plugins/forms/pickers/form-flat-pickr.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/plugins/extensions/ext-component-toastr.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/plugins/forms/form-validation.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/pages/app-todo.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <!-- END: Custom CSS-->

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern content-left-sidebar navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="content-left-sidebar">

<script>
    function updateSessionClass(class_name){
        document.getElementById("class_name").value = class_name;
        document.getElementById("updateSessionClassForm").submit();
    }
</script>
<!-- Session class update hidden form -->
        <form action="projects" method="POST" id="updateSessionClassForm">
            <input name="class_name" type="text" hidden id="class_name">
            <input name="updateSessionClass" type="text" hidden id="updateSessionClass">
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
                                        echo '<option value="Alle Benutzer und Klassen">Alle Benutzer und Klassen</option>';
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
                <li class="nav-item active"><a class="d-flex align-items-center" href="projects"><i data-feather="layers"></i><span class="menu-title text-truncate" data-i18n="Projects">Projekte</span></a></li>
                <li class="nav-item"><a class="d-flex align-items-center" href="struktogrammeditor"><i data-feather="layout"></i><span class="menu-title text-truncate" data-i18n="Struktogrammeditor">Struktogrammeditor</span></a>
                    </li>
                <?php
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

                if($db->isSubmissionFunktionEnabled()) {
                    echo '<li class=" nav-item"><a class="d-flex align-items-center" href="submissions"><i data-feather="inbox"></i><span class="menu-title text-truncate" data-i18n="Submissions">Abgaben</span></a>
                    </li>';
                }
                ?>
                
                
                
                <li class=" nav-item"><a class="d-flex align-items-center" href="#"><i data-feather="hard-drive"></i><span class="menu-title text-truncate" data-i18n="Files">Dateien</span></a>
                    <ul class="menu-content ">
                        <li><a class="d-flex align-items-center" <?php if($getCurrentUserData['role'] == "Schüler") { echo ' href="disk&drive=my"'; } else { echo 'href="disk&drive=ad-users"'; } ?>><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="My folder"><?php if($getCurrentUserData['role'] == "Schüler") { echo 'Mein Ordner'; } else { echo 'Benutzerordner'; }?></span></a>
                        </li>
                        <li><a class="d-flex align-items-center" <?php if($getCurrentUserData['role'] == "Schüler") { echo ' href="disk&drive=class"'; } else { echo 'href="disk&drive=ad-classes"'; } ?>><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Class folder">Klassenordner</span></a>
                        </li>
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

    <!-- BEGIN: Content-->
    <div class="app-content content todo-application">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-area-wrapper container-xxl p-0">
            <div class="sidebar-left">
                <div class="sidebar">
                    <div class="sidebar-content todo-sidebar">
                        <div class="todo-app-menu">
                            


                            <br />
                            <div class="sidebar-menu-list">
                                <div class="list-group list-group-filters">
                                <span style="text-align: center;"><b>Projektstatus:</b></span>
                                <hr>
                                    <a href="projects&state=my" class="list-group-item list-group-item-action <?php if($current_state == "my") { echo 'active'; } ?>">
                                        <i data-feather="user" class="font-medium-3 me-50"></i>
                                        <span class="align-middle">In Bearbeitung</span>
                                    </a>
                                    <a href="projects&state=completed" class="list-group-item list-group-item-action <?php if($current_state == "completed") { echo 'active'; } ?>">
                                        <i data-feather="user" class="font-medium-3 me-50"></i>
                                        <span class="align-middle">Abgeschlossen</span>
                                    </a>
                                    <a href="projects&state=all" class="list-group-item list-group-item-action <?php if($current_state == "all") { echo 'active'; } ?>">
                                        <i data-feather="user" class="font-medium-3 me-50"></i>
                                        <span class="align-middle">Alle</span>
                                    </a>
                                    <?php if($getCurrentUserData['role'] == "Schüler") { ?>
                                    <a href="projects&state=shared" class="list-group-item list-group-item-action <?php if($current_state == "shared") { echo 'active'; } ?>">
                                        <i data-feather="user" class="font-medium-3 me-50"></i>
                                        <span class="align-middle">Geteilte Projekte</span>
                                    </a>
                                    <a href="projects&state=unreviewed" class="list-group-item list-group-item-action <?php if($current_state == "unreviewed") { echo 'active'; } ?>">
                                        <i data-feather="user" class="font-medium-3 me-50"></i>
                                        <span class="align-middle">Abgegebenen</span>
                                    </a>
                                    <a href="projects&state=reviewed" class="list-group-item list-group-item-action <?php if($current_state == "reviewed") { echo 'active'; } ?>">
                                        <i data-feather="user" class="font-medium-3 me-50"></i>
                                        <span class="align-middle">Bewertetet</span>
                                    </a>
                                    <?php
                                    }
                                    if($getCurrentUserData['role'] != "Schüler") {
                                        ?>
                                        <hr>
                                        <a href="projects&state=shared" class="list-group-item list-group-item-action <?php if($current_state == "shared") { echo 'active'; } ?>">
                                        <i data-feather="user" class="font-medium-3 me-50"></i>
                                        <span class="align-middle">Geteilte Projekte</span>
                                    </a>
                                    <a href="projects&state=to_review" class="list-group-item list-group-item-action <?php if($current_state == "to_review") { echo 'active'; } ?>">
                                        <i data-feather="user" class="font-medium-3 me-50"></i>
                                        <span class="align-middle">Noch zu bewerten</span>
                                    </a>
                                    <a href="projects&state=reviewed_projects" class="list-group-item list-group-item-action <?php if($current_state == "reviewed_projects") { echo 'active'; } ?>">
                                        <i data-feather="user" class="font-medium-3 me-50"></i>
                                        <span class="align-middle">Bereits bewertet</span>
                                    </a>
                                    <?php
                                    }
                                    ?>
                                    <hr>
                                    <span style='text-align: center;'>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#createAppModal" class="btn btn-primary me-1">
                                                Projekt erstellen
                                        </button>
                                    </span>
                                    
                                    
                                </div>
                                
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="content-right">
                <div class="content-wrapper container-xxl p-0">
                    <div class="content-header row">
                    </div>
                    <div class="content-body">
                

                <!-- Permission Table -->
                <div class="card">
                    
                    <div class="card-datatable table-responsive">
                        <table class="datatables-permissions table">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Beschreibung</th>
                                    <th>Typ</th>
                                    <th>Status</th>
                                    <th>Abgegeben</th>
                                    
                                        <th>Besitzer</th>
                                    
                                    <th>Erstellt</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if($projects == NULL) {
                                    echo'<tr class="odd">
                                   <td><p>Keine Projekte vorhanden.</p></td></tr>';
                                }
                                else {
                                foreach($projects as $project) {
                                    echo    '<tr class="odd">
                                   <td><a href="project&id=' . $project['id'] . '">' . $project['name'] . '</a></td>
                                   <td>';  
                                    echo $project['description'];
                                   echo '</td>';
                                   if($project['category'] == 'website') {
                                    echo '<td>Website</td>';
                                }
                                if($project['category'] == 'python') {
                                    echo '<td>Python</td>';
                                }
                                                if($project['completed'] == '0') {
                                                    echo '<td><span class="badge rounded-pill badge-light-warning">In Arbeit</span></a></td>';
                                                }
                                                if($project['completed'] == '1') {
                                                    echo '<td><span class="badge rounded-pill badge-light-success">Abgeschlossen</span></a></td>';
                                                }
                                                if($project['submitted'] == '0') {
                                                    echo '<td><span class="badge rounded-pill badge-light-danger">Nein</span></a></td>';
                                                }
                                                if($project['submitted'] == '1') {
                                                    echo '<td><span class="badge rounded-pill badge-light-success">Ja</span></a></td>';
                                                }
                                              
                                                if($getCurrentUserData['role'] != "Schüler") {
                                                   $owner = $project['owner'];
                                                   $owner_name = $db->getFullnameViaID($owner);
                                                    echo '<td><a href="user&id=' . $owner . '">' . $owner_name . '</a></td>';
                                                }
                                                if($getCurrentUserData['role'] == "Schüler") {
                                                    $owner = $project['owner'];
                                                    $owner_name = $db->getFullnameViaID($owner);
                                                    echo '<td>' . $owner_name . '</td>';
                                                }
                                                
                                                echo'<td>' . $project['created_at'] . '</td>
                                                </tr>
                                            
                                            
                </div>';
                                }
                            }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--/ Permission Table -->
                    </div>
                </div>
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
    <script src="app-assets/vendors/js/editors/quill/katex.min.js"></script>
    <script src="app-assets/vendors/js/editors/quill/highlight.min.js"></script>
    <script src="app-assets/vendors/js/editors/quill/quill.min.js"></script>
    <script src="app-assets/vendors/js/forms/select/select2.full.min.js"></script>
    <script src="app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js"></script>
    <script src="app-assets/vendors/js/extensions/dragula.min.js"></script>
    <script src="app-assets/vendors/js/forms/validation/jquery.validate.min.js"></script>
    <script src="app-assets/vendors/js/extensions/toastr.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="app-assets/js/core/app-menu.js"></script>
    <script src="app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <?php include('inc/components/createEnviroment.php'); ?>
    <?php include('inc/components/createProject.php'); ?>


    <script>






    </script>

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