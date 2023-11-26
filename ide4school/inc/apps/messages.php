<?php
if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }
if(!$db->isUserLoggedIn()) {
    header("Location: not_authorized");
}
if($db->getCurrentUserInformations()['role'] == "Schüler") {
    if($db->isListenModeOn()) {
        header("Location: focus_lobby&return_to=messages");
    }
}
if(!isset($_SESSION['login_state']) && $db->getMFAMethodsFromUser($_SESSION['user_id']) != NULL) {
    header("Location: 2fa");
}
if($db->isMessageFunktionEnabled() == false) {
    header("Location: feature_not_active");
}

$classes = $db->getAllClasses();
$getCurrentUserData = $db->getCurrentUserInformations();

// SESSION CLASS MANAGMENT - BEGIN
$teacher_classes = $db->getAllowedClassesForTeachers();


$db->checkAssignmentRights();
if(isset($_POST['updateSessionClass'])) {
    $new_class = $_POST['class_name'];
    $_SESSION['currentSessionClass'] = $new_class;
    header("Location: messages");
}
// SESSION CLASS MANAGMENT - END


// MESSAGE MANAGMENT

if($getCurrentUserData['role'] == "Schüler") {
    $messages = $db->getMessagesForStudents();
}
else {
    $messages = $db->getAllMessages($_SESSION['currentSessionClass']);
}


// Message Creation
$class_members = $db->getClassMembers($_SESSION['currentSessionClass']);


if(isset($_POST["createMessage"]))
        {
          $log_user = $db->getLogUser();
          // Get Formular Data
          $titel = $_POST['message-titel'];
          $sender = $getCurrentUserData['secondName'] . ', ' . $getCurrentUserData['firstName'];
          $zuteilung = $_POST['message-zuteilung'];
          $message = $_POST['message-text'];
          $class = $_SESSION['currentSessionClass'];

         
          // Insert in DB and create Todo
          if($db->createMessage($titel, $sender, $zuteilung, $message, $class, $log_user)) {
            header("Location: messages");
            }
            else {
                echo'
            <div class="alert alert-danger" role="alert">
                                                <h4 class="alert-heading">Datenbankfehler</h4>
                                                <div class="alert-body">
                                                Mitteilung konnte nicht erstellt werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                                </div>
                                            </div>';
            }
    };


        if(isset($_POST["deleteMessage"]))
        {

        $id = $_POST['message-id'];
        $titel = $_POST['message-titel'];
        // Insert in DB and create Todo
        if($db->deleteMessage($id, $titel, $log_user)) {
            header("Location: messages");
            }
            else {
                echo'
            <div class="alert alert-danger" role="alert">
                                                <h4 class="alert-heading">Datenbankfehler</h4>
                                                <div class="alert-body">
                                               Mitteilung konnte nicht gelöscht werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                                </div>
                                            </div>';
            }
    };

      


?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="author" content="em CLOUDsolutions">
    <title>Mitteilungen - <?php echo $db->getCurrentInstitution() ?> - ide4school</title>
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
        <form action="messages" method="POST" id="updateSessionClassForm">
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
                    echo'<li class=" nav-item active"><a class="d-flex align-items-center" href="messages"><i data-feather="message-square"></i><span class="menu-title text-truncate" data-i18n="Messages">Mitteilungen</span></a>
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
                            <div class="add-task">
                                <?php
                            if($db->noStudent()) {
                                                echo '<button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#new-task-modal">
                                                Nachricht erstellen
                                            </button>';
                                            }
                                ?>
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
                        <div class="body-content-overlay"></div>
                        <div class="todo-app-list">
                           

                            <!-- Message List starts -->
                            <div class="todo-task-list-wrapper list-group">
                                <ul class="todo-task-list media-list" id="todo-task-list">
                                    <?php
                                    if($messages == NULL) {
                                        echo '
                                        <li class="todo-item">
                                                <div class="todo-title-wrapper">
                                                    <div class="todo-title-area">
                                                        Keine Mitteilungen vorhanden.
                                                    </div>
                                                </div>
                                            </div>';
                                    }
                                    foreach($messages as $message) {
                                        echo'
                                            <li class="todo-item" data-bs-toggle="modal" data-bs-target="#view-message-modal-id' . $message['id'] . '">
                                                <div class="todo-title-wrapper">
                                                    <div class="todo-title-area">
                                                        
                                                        <div class="title-wrapper">
                                                            
                                                            <span  class="todo-title">' . $message['title'] . '</span>
                                                        </div>
                                                    </div>
                                                    <div class="todo-item-action">
                                                        <div class="badge-wrapper me-1">
                                                            <span class="badge rounded-pill badge-light-primary">' . $message['date'] . '</span>
                                                        </div>
                                                        <small class="text-nowrap text-muted me-1">Von ' . $message['sender'] . '</small>
                                                        <div class="avatar">
                                                            <img src="app-assets/images/portrait/small/avatar-s-4.jpg" alt="user-avatar" height="32" width="32" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>';
                                    }
                                    ?>
                                    
                                </ul>
                                
                            </div>
                            <!-- Todo List ends -->
                        </div>

                        <!-- Creation Sidebar starts -->
                        <div class="modal modal-slide-in sidebar-todo-modal fade" id="new-task-modal">
                            <div class="modal-dialog sidebar-lg">
                                <div class="modal-content p-0">
                                    <form id="form-modal-todo" class="todo-modal needs-validation" action="messages" method="POST">
                                        <div class="modal-header align-items-center mb-1">
                                            <h5 class="modal-title">Nachricht erstellen</h5>
                                            <div class="todo-item-action d-flex align-items-center justify-content-between ms-auto">
                                                <span class="todo-item-favorite cursor-pointer me-75"><i data-feather="star" class="font-medium-2"></i></span>
                                                <i data-feather="x" class="cursor-pointer" data-bs-dismiss="modal" stroke-width="3"></i>
                                            </div>
                                        </div>
                                        <div class="modal-body flex-grow-1 pb-sm-0 pb-3">
                                            <div class="action-tags">
                                                <div class="mb-1">
                                                    <label for="todoTitleAdd" class="form-label">Titel</label>
                                                    <input type="text" id="todoTitleAdd" name="message-titel" class="new-todo-item-title form-control" placeholder="Titel" />
                                                </div>
                                                <div class="mb-1 position-relative">
                                                    <label for="task-assigned" class="form-label d-block">An</label>
                                                    <select class="select2 form-select" id="todo-zuteilung" name="message-zuteilung">
                                                    <option data-img="app-assets/images/portrait/small/avatar-s-3.jpg" value="<?=$_SESSION['currentSessionClass']?>" selected>
                                                            Alle
                                                        </option>
                                                    <?php foreach($class_members as $class_member) {
                                                        echo '<option data-img="app-assets/images/portrait/small/avatar-s-3.jpg" value="' . $class_member['secondName'] . ', ' . $class_member['firstName'] . '">
                                                        ' . $class_member['secondName'] . ', ' . $class_member['firstName'] . '
                                                    </option>';
                                                    } ?>
                                                        
                                                       
                                                    </select>
                                                </div>
                                                
                                                <div class="mb-1">
                                                    <label class="form-label">Beschreibung</label>
                                                    <textarea rows="3" class="form-control task-description" id="task-due-date" placeholder="Nachricht eingeben..." name="message-text"></textarea>
                                                </div>
                                            </div>
                                            <div class="mb-1">
                                                <button type="submit" name="createMessage" value="createMessage" class="btn btn-primary  add-todo-item me-1">Hinzufügen</button>
                                                <button type="button" class="btn btn-outline-secondary add-todo-item " data-bs-dismiss="modal">
                                                    Abbrechen
                                                </button>
                                               
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Creation Sidebar ends -->

                    <?php

                    foreach($messages as $message) {
                        echo'<!-- View Sidebar starts -->
                        <div class="modal large fade" id="view-message-modal-id' . $message['id'] . '">
                            <div class="modal-dialog sidebar-lg">
                                <div class="modal-content p-0">
                                    <form id="form-modal-todo" class="todo-modal needs-validation" action="messages" method="POST">
                                        <div class="modal-header align-items-center mb-1">
                                            <h5 class="modal-title">Mitteilung ansehen</h5>
                                            <div class="todo-item-action d-flex align-items-center justify-content-between ms-auto">
                                                <i data-feather="x" class="cursor-pointer" data-bs-dismiss="modal" stroke-width="3"></i>
                                            </div>
                                        </div>
                                        <div class="modal-body flex-grow-1 pb-sm-0 pb-3">
                                            <div class="action-tags">
                                                <div class="mb-1">
                                                    <label for="todoTitleAdd" class="form-label">Titel:</label>
                                                    <input type="text" id="todoTitleAdd" name="message-titel" class="new-todo-item-title form-control" placeholder="Titel" value="' . $message['title'] . '" readonly/>
                                                </div>
                                                <div class="mb-1 position-relative">
                                                    <label for="task-assigned" class="form-label d-block">Von:</label>
                                                    <input type="text" id="todoTitleAdd" name="message-sender" class="new-todo-item-title form-control" placeholder="Nachricht von:" value="' . $message['sender'] . '" readonly/>
                                                </div>
                                                <div class="mb-1 position-relative">
                                                    <label for="task-assigned" class="form-label d-block">An:</label>
                                                    <input type="text" id="todoTitleAdd" name="message-zuteilung" class="new-todo-item-title form-control" placeholder="Nachricht an" value="' . $message['receiver'] . '" readonly/>
                                                </div>
                                               
                                                <div class="mb-1">
                                                    <label for="task-due-date" class="form-label">Gesendet:</label>
                                                    <input type="datetime-local" class="form-control task-due-date" id="task-due-date" placeholder="Gesendet:" value="' . $message['date'] . '" name="message-date" readonly/>
                                                </div>
                                                
                                                <div class="mb-1">
                                                    <label class="form-label">Nachricht:</label>
                                                    <textarea rows="3" class="form-control task-description" id="task-due-date" name="message-text" readonly>' . $message['text'] . '</textarea>
                                                </div>
                                            </div>
                                            <input type="hidden" value="' . $message['id'] . '" name="message-id" readonly hidden/> 
                                            <div class="mb-1">';
                                            if($getCurrentUserData['role'] != "Schüler") {
                                                echo '<button type="submit" name="deleteMessage" class="btn btn-outline-secondary add-todo-item ">Löschen</button>';
                                            }
                                            echo'
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- View Sidebar ends -->';
                    }
                    ?>
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