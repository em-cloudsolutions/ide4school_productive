<?php
if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }
if(!$db->isUserLoggedIn()) {
    header("Location: not_authorized");
}
if($db->getCurrentUserInformations()['role'] == "Schüler") {
    if($db->isListenModeOn()) {
        header("Location: focus_lobby&return_to=email");
    }
}
if(!isset($_SESSION['login_state']) && $db->getMFAMethodsFromUser($_SESSION['user_id']) != NULL) {
    header("Location: 2fa");
}


if($db->isEmailFunktionEnabled() == false) {
    header("Location: feature_not_active");
}

$classes = $db->getAllClasses();
$getCurrentUserData = $db->getCurrentUserInformations();
$log_user = $db->getLogUser();


// SESSION CLASS MANAGMENT - BEGIN
$teacher_classes = $db->getAllowedClassesForTeachers();

$db->checkAssignmentRights();
if(isset($_POST['updateSessionClass'])) {
    $new_class = $_POST['class_name'];
    $_SESSION['currentSessionClass'] = $new_class;
    header("Location: email");
}
// SESSION CLASS MANAGMENT - END


// EMAIL MANAGEMENT
if(!isset($_GET['folder'])) {
    $current_folder = "inbox"; 
}
else {
$current_folder = $_GET['folder'];
}

$emails = $db->getEmailsFromInbox();

if($current_folder == "sent") {
 $emails = $db->getSentEmail();
}

if($_SESSION['currentSessionClass'] == "Alle Benutzer und Klassen") {
    $class_members = $db->getAllUsers();
}
$class_members = $db->getClassMembers($_SESSION['currentSessionClass']);
if($getCurrentUserData['role'] == "Schüler") {
    $class_members = $db->getAllUsersFromClass($getCurrentUserData['class']);
}


if(isset($_POST["sendEmail"]))
        {
          $log_user = $db->getLogUser();
          // Get Formular Data
          $subject = $_POST['email-subject'];
          $sender = $getCurrentUserData['id'];
          $to = $_POST['email-to'];
          $message = $_POST['email-message'];
          $class = $_SESSION['currentSessionClass'];

          // Insert in DB and create Email
          if($db->createEmail($subject, $sender, $to, $message, $class, $log_user)) {
            header("Location: email");
            }
            else {
                echo'
            <div class="alert alert-danger" role="alert">
                                                <h4 class="alert-heading">Datenbankfehler</h4>
                                                <div class="alert-body">
                                                Nachricht konnte nicht erstellt werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                                </div>
                                            </div>';
            }
    };


if(isset($_POST["deleteEmail"]))
        {

        $id = $_POST['email_id'];
        // Insert in DB and create Todo
        if($db->deleteEmail($id)) {
            header("Location: email");
            }
            else {
                echo'
            <div class="alert alert-danger" role="alert">
                                                <h4 class="alert-heading">Datenbankfehler</h4>
                                                <div class="alert-body">
                                                Nachricht konnte nicht gelöscht werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                                </div>
                                            </div>';
            }
    };

if(isset($_POST["openEmail"]))
        {

        $id = $_POST['email_id'];
        if($db->openEmail($id)) {
            header("Location: email");
            }
            else {
                echo'
            <div class="alert alert-danger" role="alert">
                                                <h4 class="alert-heading">Datenbankfehler</h4>
                                                <div class="alert-body">
                                                Nachricht konnte nicht als gelesen markiert werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
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
    <title>E-Mail - <?=$getCurrentUserData['secondName']?>, <?=$getCurrentUserData['firstName']?> - ide4school</title>
    <link rel="apple-touch-icon" href="app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/editors/quill/katex.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/editors/quill/monokai-sublime.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/editors/quill/quill.snow.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/extensions/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/forms/select/select2.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css2?family=Inconsolata&amp;family=Roboto+Slab&amp;family=Slabo+27px&amp;family=Sofia&amp;family=Ubuntu+Mono&amp;display=swap">
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
    <link rel="stylesheet" type="text/css" href="app-assets/css/plugins/extensions/ext-component-toastr.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/pages/app-email.css">
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
        <form action="email" method="post" id="updateSessionClassForm">
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
                <li class="nav-item"><a class="d-flex align-items-center" href="projects"><i data-feather="layers"></i><span class="menu-title text-truncate" data-i18n="Projects">Projekte</span></a>
                </li>
                <li class="nav-item"><a class="d-flex align-items-center" target="_blank" href="struktogrammeditor"><i data-feather="layout"></i><span class="menu-title text-truncate" data-i18n="Struktogrammeditor">Struktogrammeditor</span></a>
                    </li>
                <?php
                
                if($db->isEmailFunktionEnabled()) {
                    echo '<li class=" nav-item active"><a class="d-flex align-items-center" href="email"><i data-feather="mail"></i><span class="menu-title text-truncate" data-i18n="Direktnachrichten">Direktnachrichten</span></a>
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

    <!-- BEGIN: Content-->
    <div class="app-content content email-application">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-area-wrapper container-xxl p-0">
            <div class="sidebar-left">
                <div class="sidebar">
                    <div class="sidebar-content email-app-sidebar">
                        <div class="email-app-menu">
                            <div class="form-group-compose text-center compose-btn">
                                <button type="button" class="compose-email btn btn-primary w-100" data-bs-backdrop="false" data-bs-toggle="modal" data-bs-target="#compose-mail">
                                    Verfassen
                                </button>
                            </div>
                            <div class="sidebar-menu-list">
                                <div class="list-group list-group-messages">
                                    <a href="email&folder=inbox" class="list-group-item list-group-item-action <?php if($current_folder == "inbox") { echo 'active'; } ?>">
                                        <i data-feather="mail" class="font-medium-3 me-50"></i>
                                        <span class="align-middle">Posteingang</span>
                                    </a>
                                    <a href="email&folder=sent" class="list-group-item list-group-item-action <?php if($current_folder == "sent") { echo 'active'; } ?>">
                                        <i data-feather="send" class="font-medium-3 me-50"></i>
                                        <span class="align-middle">Gesendet</span>
                                    </a>
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
                        <div class="body-content-overlay"></div>
                        <!-- Email list Area -->
                        <div class="email-app-list">
                            

                            <!-- Email list starts -->
                            <div class="email-user-list">
                                <ul class="email-media-list">
                                    <?php
                                    if($emails == NULL) {
                                        echo '<div class="d-flex user-mail">
                                        <h5>Keine Emails vorhanden.</h5>
                                    </div>';
                                    }
                                    foreach($emails as $email) {
                                        echo'
                                    <li class="d-flex user-mail '; if($email['opened'] == "1") { echo 'mail-read'; } echo '" data-bs-toggle="modal" data-bs-target="#view-email-modal-id' . $email['id'] . '">
                                        <div class="mail-left pe-50">
                                            <div class="avatar">
                                                <img src="app-assets/images/portrait/small/avatar-s-20.jpg" alt="avatar img holder" />
                                            </div>
                                            
                                        </div>
                                        <div class="mail-body">
                                            <div class="mail-details">
                                                <div class="mail-items">
                                                ';
                                                if($email['opened'] == 1) {
                                                    echo'<h5 class="mb-25">' . $email['subject'] . '</h5>';
                                                } else {
                                                    echo'<h5 class="mb-25"><b>' . $email['subject'] . '</h5></b>';
                                                }
                                                echo'
                                                    <span class="text-truncate">Von: '; 
                                                    $id = $email['sender'];
                                                    $sender = $db->getFullNameViaID($id);
                                                    echo $sender; echo '</span>
                                                    <span class="text-truncate">    --->    An: '; $id = $email['receiver'];
                                                    $receiver = $db->getFullNameViaID($id);
                                                    echo $receiver; echo '</span>
                                                </div>
                                                <div class="mail-meta-item">

                                                    <span class="mail-date">' . $email['date'] . '</span>
                                                </div>
                                            </div>
                                            <div class="mail-message">
                                                <p class="text-truncate mb-0">
                                                ' . $email['text'] . '
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                    ';
                                }
                                echo '
                                </ul>
                            </div>
                            <!-- Email list ends -->
                        </div>
                        <!--/ Email list Area -->';


foreach($emails as $email) {
    echo'<!-- View Sidebar starts -->
    <div class="modal large fade" id="view-email-modal-id' . $email['id'] . '">
        <div class="modal-dialog sidebar-lg">
            <div class="modal-content p-0">
                <form id="form-modal-todo" class="todo-modal needs-validation" action="email" method="POST">
                    <div class="modal-header align-items-center mb-1">
                        <h5 class="modal-title">Direktnachricht ansehen</h5>
                        <div class="todo-item-action d-flex align-items-center justify-content-between ms-auto">
                            <i data-feather="x" class="cursor-pointer" data-bs-dismiss="modal" stroke-width="3"></i>
                        </div>
                    </div>
                    <div class="modal-body flex-grow-1 pb-sm-0 pb-3">
                        <div class="action-tags">
                            <div class="mb-1">
                                <label for="todoTitleAdd" class="form-label">Betreff:</label>
                                <input type="text" id="todoTitleAdd" name="email-titel" class="new-todo-item-title form-control" placeholder="Titel" value="' . $email['subject'] . '" readonly/>
                            </div>
                            <div class="mb-1 position-relative">
                                <label for="task-assigned" class="form-label d-block">Von:</label>
                                <input type="text" id="todoTitleAdd" name="message-sender" class="new-todo-item-title form-control" placeholder="Nachricht von:" value="'; $id = $email['sender'];
                                $sender = $db->getFullNameViaID($id);
                                echo $sender; echo '" readonly/>
                            </div>
                            <div class="mb-1 position-relative">
                                <label for="task-assigned" class="form-label d-block">An:</label>
                                <input type="text" id="todoTitleAdd" name="message-zuteilung" class="new-todo-item-title form-control" placeholder="Nachricht an" value="'; $id = $email['receiver'];
                                $receiver = $db->getFullNameViaID($id);
                                echo $receiver; echo '" readonly/>
                            </div>
                           
                            <div class="mb-1">
                                <label for="task-due-date" class="form-label">Gesendet:</label>
                                <input type="datetime-local" class="form-control task-due-date" id="task-due-date" placeholder="Gesendet:" value="' . $email['date'] . '" name="message-date" readonly/>
                            </div>
                            
                            <div class="mb-1">
                                <label class="form-label">Nachricht:</label>
                                <textarea rows="15" class="form-control task-description" id="task-due-date" name="message-text" readonly>' . $email['text'] . '</textarea>
                            </div>
                        </div>
                        <input type="hidden" value="' . $email['id'] . '" name="email_id" readonly hidden/> 
                        <div class="mb-1">';
                      if($email['opened'] == "0" && $email['receiver'] == $_SESSION['user_id']) {
                            echo '<button type="submit" name="openEmail" class="btn btn-outline-secondary add-todo-item">Als gelesen markieren</button> ';
                      } 
                        echo ' <button style="text-align: right;" type="submit" name="deleteEmail" class="btn btn-outline-secondary add-todo-item">Direktnachricht löschen</button>';
                    
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

                        <!-- compose email -->
                        <div class="modal modal-sticky" id="compose-mail" data-bs-keyboard="false">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                <div class="modal-content p-0">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Direktnachricht verfassen</h5>
                                        <div class="modal-actions">
                                            <a href="#" class="text-body me-75 compose-maximize"><i data-feather="maximize-2"></i></a>
                                            <a class="text-body" href="#" data-bs-dismiss="modal" aria-label="Close"><i data-feather="x"></i></a>
                                        </div>
                                    </div>
                                    <div class="modal-body flex-grow-1 p-0">
                                        <form class="compose-form" action="email" method="POST">
                                            <div class="compose-mail-form-field select2-primary">
                                                <label for="email-to" class="form-label">An: </label>
                                                <div class="flex-grow-1">
                                                    <select class="select2 form-select w-100" id="email-to" name="email-to" multiple>
                                                   
                                                        <?php foreach($class_members as $class_member) {
                                                        echo '<option data-avatar="'.$class_member['avatar'].'.png" value="' . $class_member['id'] . '">
                                                        ' . $class_member['secondName'] . ', ' . $class_member['firstName'] . '
                                                    </option>';
                                                    } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="compose-mail-form-field">
                                                <label for="emailSubject" class="form-label">Betreff: </label>
                                                <input type="text" id="emailSubject" class="form-control" name="email-subject" />
                                            </div>
                                            <div id="message-editor">
                                                

                                                <textarea class="editor" style="width: 96%; margin-left: 15px; margin-right: 15px;" placeholder="Nachricht hier eingeben..." name="email-message" id="email-message" cols="30" rows="10"></textarea>
                                                
                                                
                                                <div class="compose-editor-toolbar">
                                                    <span class="ql-formats me-0">
                                                        <select class="ql-font">
                                                            <option selected>Sailec Light</option>
                                                            <option value="sofia">Sofia Pro</option>
                                                            <option value="slabo">Slabo 27px</option>
                                                            <option value="roboto">Roboto Slab</option>
                                                            <option value="inconsolata">Inconsolata</option>
                                                            <option value="ubuntu">Ubuntu Mono</option>
                                                        </select>
                                                    </span>
                                                    <span class="ql-formats me-0">
                                                        <button class="ql-bold"></button>
                                                        <button class="ql-italic"></button>
                                                        <button class="ql-underline"></button>
                                                        <button class="ql-link"></button>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="compose-footer-wrapper">
                                                <div class="btn-wrapper d-flex align-items-center">
                                                    <div class="btn-group dropup me-1">
                                                        <button type="submit" name="sendEmail" value="sendEmail" class="btn btn-primary">Senden</button>
                                                        
                                                    </div>
                                                   
                                                </div>
                                                <div class="footer-action d-flex align-items-center">
                                                
                                                    <i data-feather="trash" class="font-medium-2 cursor-pointer" data-bs-dismiss="modal"></i>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/ compose email -->

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
    <script src="app-assets/vendors/js/extensions/toastr.min.js"></script>
    <script src="app-assets/vendors/js/forms/select/select2.full.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="app-assets/js/core/app-menu.js"></script>
    <script src="app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="app-assets/js/scripts/pages/app-email.js"></script>
    <!-- END: Page JS-->
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