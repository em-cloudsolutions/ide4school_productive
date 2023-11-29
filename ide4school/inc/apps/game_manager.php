<?php
if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }
if(!$db->isUserLoggedIn()) {
    header("Location: not_authorized");
}
if(!isset($_SESSION['login_state']) && $db->getMFAMethodsFromUser($_SESSION['user_id']) != NULL) {
    header("Location: 2fa");
}

$users = $db->getAllUsers();
$classes = $db->getAllClasses();
$getCurrentUserData = $db->getCurrentUserInformations();

// SESSION CLASS MANAGMENT - BEGIN
$teacher_classes = $db->getAllowedClassesForTeachers();


$db->checkAssignmentRights();
if(isset($_POST['updateSessionClass'])) {
    $new_class = $_POST['class_name'];
    $_SESSION['currentSessionClass'] = $new_class;
    header("Location: users");
}
// SESSION CLASS MANAGMENT - END


$game_sessions = $db->getAllGameSessions();

if(isset($_POST['stopSession'])) {
    if($db->stopSession($_POST['game_token_stop'])) {
        header("Location: game_manager");
    }
    else {
        echo'
        <div class="alert alert-danger" role="alert">
                                            <h4 class="alert-heading">Datenbankfehler</h4>
                                            <div class="alert-body">
                                            Spielsitzung konnte nicht beendet werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie Ihren Institutionsadministrator!
                                            </div>
                                        </div>';
    }
}

if(isset($_POST['pauseSession'])) {
    if($db->pauseSession($_POST['game_token_pause'])) {
        header("Location: game_manager");
    }
    else {
        echo'
        <div class="alert alert-danger" role="alert">
                                            <h4 class="alert-heading">Datenbankfehler</h4>
                                            <div class="alert-body">
                                            Spielsitzung konnte nicht pausiert werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie Ihren Institutionsadministrator!
                                            </div>
                                        </div>';
    }
}

if(isset($_POST['continueSession'])) {
    if($db->continueSession($_POST['game_token_continue'])) {
        header("Location: game_manager");
    }
    else {
        echo'
        <div class="alert alert-danger" role="alert">
                                            <h4 class="alert-heading">Datenbankfehler</h4>
                                            <div class="alert-body">
                                            Spielsitzung konnte nicht freigegeben werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie Ihren Institutionsadministrator!
                                            </div>
                                        </div>';
    }
}

if(isset($_POST['renameSession'])) {
    if($db->renameSession($_POST['new_name_entry'], $_POST['game_token_rename'])) {
        header("Location: game_manager");
    }
    else {
        echo'
        <div class="alert alert-danger" role="alert">
                                            <h4 class="alert-heading">Datenbankfehler</h4>
                                            <div class="alert-body">
                                            Spielsitzung konnte nicht umbenannt werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie Ihren Institutionsadministrator!
                                            </div>
                                        </div>';
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
    <title>Spiel Sitzungen - <?php echo $db->getCurrentInstitution() ?> - ide4school</title>
    <link rel="apple-touch-icon" href="app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/forms/select/select2.min.css">
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

    function stopSession(game_token){
        if(confirm("Sitzung wirklich löschen?")) {
        document.getElementById("game_token_stop").value = game_token;
        document.getElementById("stopSessionForm").submit();
        }
    }

    function pauseSession(game_token){
        if(confirm("Sitzung wirklich pausieren?")) {
        document.getElementById("game_token_pause").value = game_token;
        document.getElementById("pauseSessionForm").submit();
        }
    }

    function continueSession(game_token){
        if(confirm("Sitzung wirklich freigeben?")) {
        document.getElementById("game_token_continue").value = game_token;
        document.getElementById("continueSessionForm").submit();
        }
    }

    function copyInvitationLink(id){
        //Kombieniere 'game_token_invite_' mit der id des Elements
        var element = 'game_token_invite_' + id;
        finalLink = document.getElementById(element).textContent;
        //Link in Zwischenablage des benutzers kopieren
        navigator.clipboard.writeText(finalLink);
    }

    function renameSession(game_token){
        if(confirm("Sitzung umbenennen?")) {
            if(newName = prompt("Neuer Name: ")) {
                document.getElementById("new_name_entry").value = newName;
                document.getElementById("game_token_rename").value = game_token;
                document.getElementById("renameSessionForm").submit();
            }
            
        }

    }


</script>
<!-- Session class update hidden form -->
<form action="game_manager" method="POST" id="updateSessionClassForm">
            <input name="class_name" type="text" hidden id="class_name">
            <input name="updateSessionClass" type="text" hidden id="updateSessionClass">
        </form>

 <!-- user logout hidden form -->

        <form action="game_manager" method="post" id="stopSessionForm">
            <input name="game_token_stop" type="text" hidden id="game_token_stop">
            <input name="stopSession" type="text" hidden id="stopSession">
        </form>

        <form action="game_manager" method="post" id="pauseSessionForm">
            <input name="game_token_pause" type="text" hidden id="game_token_pause">
            <input name="pauseSession" type="text" hidden id="pauseSession">
        </form>
        
        <form action="game_manager" method="post" id="continueSessionForm">
            <input name="game_token_continue" type="text" hidden id="game_token_continue">
            <input name="continueSession" type="text" hidden id="continueSession">
        </form>

        <form action="game_manager" method="post" id="renameSessionForm">
            <input name="new_name_entry" type="text" hidden id="new_name_entry">
            <input name="game_token_rename" type="text" hidden id="game_token_rename">
            <input name="renameSession" type="text" hidden id="renameSession">
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
                <li class="nav-item"><a class="d-flex align-items-center" href="projects"><i data-feather="layers"></i><span class="menu-title text-truncate" data-i18n="Projects">Projekte</span></a>
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
                    <li class=" nav-item open"><a class="d-flex align-items-center" href="#"><i data-feather="play-circle"></i><span class="menu-title text-truncate" data-i18n="Lernspiele">Lernspiele</span></a>
                    <ul class="menu-content ">
                        <li><a class="d-flex align-items-center" href="games"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Spieleübersicht">Spieleübersicht</span></a>
                        </li>
                        <li><a class="d-flex align-items-center active" href="game_manager"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Session Manager">Session Manager</span></a>
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

    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- users list start -->
                <section class="app-user-list">
                   
                    <!-- list and filter start -->
                    <div class="card">
                    <div class="card-body border-bottom">
    <h4 class="card-title">Von Ihnen gestartete Spielsitzungen</h4>
    <div class="row">

        
     </div>
</div>
                       
<br />
                            <table class="user-list-table table">
                                <thead class="table-light">
                                    <tr>
                                      
                                        <th>Spiel</th>
                                        <th>Status</th>
                                        <th>Aufrufe</th>
                                        <th>Erstellt am</th>
                                        <th>Erstellt von</th>
                                        <th>Aktionen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($game_sessions as $game_session) {
                                    echo '
                                <tr class="odd">
    <td class=" control" tabindex="0" style="display: none;"></td>
    <td class="sorting_1">
        <div class="d-flex justify-content-left align-items-center">
            <div class="avatar-wrapper">
                <div class="avatar  me-1"><a target="_blank" href="game&token=' . $game_session['token'] . '" ><img src="app-assets/images/games/'.$db->getGameNameFromID($game_session['game_id']).'.png" alt="' .$db->getGameNameFromID($game_session['game_id']). '" height="32" width="32"></a></div>
            </div>
            <div class="d-flex flex-column"><a target="_blank" href="game&token=' . $game_session['token'] . '" class="user_name text-truncate text-body"><span class="fw-bolder">' .$game_session['session_name']. '</span></a><small class="emp_post text-muted">Token: ' . $game_session['token'] . '</small></div>
        </div>
    </td>';
    if($game_session['status'] == "1") {
        echo '<td><span class="badge rounded-pill badge-light-success" text-capitalized="">Aktiv</span></td>';
        }
        else {
        echo '<td><span class="badge rounded-pill badge-light-warning" text-capitalized="">Inaktiv</span></td>';
        }
    echo'
    <td>' . $game_session['requests'] . '</td>
    <td>'.$game_session['created_at']. '</td>
    <td>'.$game_session['creator']. '</td>
    <td>
    <div class="btn-group">
        <a class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical font-small-4">
                <circle cx="12" cy="12" r="1"></circle>
                <circle cx="12" cy="5" r="1"></circle>
                <circle cx="12" cy="19" r="1"></circle>
            </svg>
        </a>
        <div class="dropdown-menu dropdown-menu-end">
        ';
                
            echo '
            <a href="javascript:;" OnClick="renameSession(`'.$game_session['token'].'`)" class="dropdown-item delete-record">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                Sitzung umbenennen
            </a>
            <a href="javascript:;" OnClick="copyInvitationLink(`'.$game_session['id'].'`)" class="dropdown-item delete-record">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-link"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                Link kopieren
            </a>
            <textarea id="game_token_invite_'.$game_session['id'].'" style="display: none;">'.$_SERVER['HTTP_HOST'].'/ide4school/game&token='.$game_session['token'].'</textarea>';
            
            if($game_session['status'] == '1') {
                echo'<a href="javascript:;" OnClick="pauseSession(`' . $game_session['token'] . '`)" class="dropdown-item delete-record">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-pause"><rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect></svg>
                   Sitzung pausieren
            </a>';
            }
            if($game_session['status'] == '0') {
                echo'<a href="javascript:;" OnClick="continueSession(`' . $game_session['token'] . '`)" class="dropdown-item delete-record">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-play"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                   Sitzung fortsetzen
            </a>';
            }
            echo '
            <a href="javascript:;" OnClick="stopSession(`' . $game_session['token'] . '`)" class="dropdown-item delete-record">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                  Sitzung beenden
            </a>';

            echo '
        </div>
    </div>
</td>
</tr>';
} 
if($game_sessions == NULL) {
    echo '<tr><td>Keine Spiele gestartet.</td></tr>';
}?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- list and filter end -->
                </section>
                <!-- users list ends -->

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
    <script src="app-assets/vendors/js/forms/validation/jquery.validate.min.js"></script>
    <script src="app-assets/vendors/js/forms/cleave/cleave.min.js"></script>
    <script src="app-assets/vendors/js/forms/cleave/addons/cleave-phone.us.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="app-assets/js/core/app-menu.js"></script>
    <script src="app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

            <?php include('inc/components/createEnviroment.php'); ?>

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