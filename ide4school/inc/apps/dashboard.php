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
if(!isset($_SESSION['login_state']) && $db->getMFAMethodsFromUser($_SESSION['user_id']) != NULL) {
    header("Location: 2fa");
}

$classes = $db->getAllClasses();
$getCurrentUserData = $db->getCurrentUserInformations();

if($getCurrentUserData['agb_confirmed'] != "1") {
    header("Location: first-login");
}

// SESSION CLASS MANAGMENT - BEGIN
$teacher_classes = $db->getAllowedClassesForTeachers();

$db->checkAssignmentRights();
if(isset($_POST['updateSessionClass'])) {
    $new_class = $_POST['class_name'];
    $_SESSION['currentSessionClass'] = $new_class;
    header("Location: dashboard");
}
// SESSION CLASS MANAGMENT - END

$logs = $db->getAllLogs4Dashboard();
$notifications = $db->getUserNotifications4Dashboard();

if($db->shouldBeStudentInExam()) {
    $exam_id = $getCurrentUserData['exam_id'];
    $token = $db->getExamTokenViaID($exam_id);
    header("Location: exam&token=".$token);
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
    <title>Dashboard - <?=$getCurrentUserData['secondName']?>, <?=$getCurrentUserData['firstName']?> - <?=$getCurrentUserData['institution']?> - ide4school</title>
    <link rel="apple-touch-icon" href="app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/charts/apexcharts.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/extensions/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css">
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
    <link rel="stylesheet" type="text/css" href="app-assets/css/plugins/charts/chart-apex.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/plugins/extensions/ext-component-toastr.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/pages/app-invoice-list.css">
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
</script>
<!-- Session class update hidden form -->
        <form action="dashboard" method="post" id="updateSessionClassForm">
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
               
                <li class="nav-item active"><a class="d-flex align-items-center" href="dashboard"><i data-feather="home"></i><span class="menu-title text-truncate" data-i18n="Dashboard">Dashboard</span></a>
                </li>
                <li class=" navigation-header"><span data-i18n="Apps">Apps</span><i data-feather="more-horizontal"></i>
                </li>
                <li class="nav-item"><a data-bs-toggle="modal" data-bs-target="#createEnviromentModal" class="d-flex align-items-center"><i data-feather="edit-3"></i><span class="menu-title text-truncate" data-i18n="Development Enviroment">Programmieren</span></a>
                </li>
                <li class="nav-item"><a class="d-flex align-items-center" href="projects"><i data-feather="layers"></i><span class="menu-title text-truncate" data-i18n="Projects">Projekte</span></a>
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
                <li class=" nav-item"><a class="d-flex align-items-center" href="#"><i data-feather='check-circle'></i></i><span class="menu-title text-truncate" data-i18n="Exam">Prüfungen</span></a>
                    <ul class="menu-content ">
                        <li><a class="d-flex align-items-center" <?php if($getCurrentUserData['role'] == "Schüler") { echo ' href="exams"'; } else { echo 'href="exams"'; } ?>><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Exam timeline"><?php if($getCurrentUserData['role'] == "Schüler") { echo 'Geschriebene Prüfungen'; } else { echo 'Prüfungsübersicht'; }?></span></a>
                        </li>
                        <?php if($getCurrentUserData['role'] != "Schüler") { echo '<li><a class="d-flex align-items-center" href="exam&state=createNewExam" ><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Class folder">Klassenordner</span></a>
                        </li>'; }
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
if($getCurrentUserData['role'] == "Schüler") {
    $submissions = $db->getUserSubmissions4Dashboard();
    ?>

    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- Dashboard Analytics Start -->
                <section id="dashboard-analytics">
                    <div class="row match-height">
                        <!-- Greetings Card starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card card-congratulations">
                                <div class="card-body text-center">
                                    <img src="app-assets/images/elements/decore-left.png" class="congratulations-img-left" alt="card-img-left" />
                                    <img src="app-assets/images/elements/decore-right.png" class="congratulations-img-right" alt="card-img-right" />
                                    <div class="avatar avatar-xl bg-primary shadow">
                                        <div class="avatar-content">
                                            <i data-feather="award" class="font-large-1"></i>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <h1 class="mb-1 text-white">Hallo <?=$getCurrentUserData['firstName']?>,</h1>
                                        <p class="card-text m-auto w-75">
                                            willkommen im Benutzerportal von ide4school. <br />
                                            Erledige Aufgaben, schreibe Emails oder Programmiere einfach drauf los. Du hast die Wahl!
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Greetings Card ends -->

                    </div>

                    <div class="row match-height">
                       

                    <div class="row match-height">
                        <!-- Timeline Card -->
                        <div class="col-lg-6 col-12">
                            <div class="card card-user-timeline">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <i data-feather="list" class="user-timeline-title-icon"></i>
                                        <h4 class="card-title">Neue Benachrichtigungen</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <ul class="timeline ms-50">
                                        <?php
                                    
                                        if($notifications == NULL) {
                                            echo '<li class="timeline-item">
                                                    <span class="timeline-point timeline-point-indicator"></span>
                                                    <div class="timeline-event">
                                                        <h6>Keine Benachrichtigungen vorhanden.</h6>
                                                    </div>
                                                </li>';
                                        }
                                                foreach($notifications as $notification) {
                                                    echo '<li class="timeline-item">
                                                    <span class="timeline-point timeline-point-indicator"></span>
                                                    <div class="timeline-event">
                                                        <h6>' . $notification['heading'] . '</h6>
                                                        <p>' . $notification['text'] . '</p>
                                                    </div>
                                                </li>';
                                                }
                                            
                                            
                                            ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!--/ Timeline Card -->

                        <!-- Timeline Card -->
                        <div class="col-lg-6 col-12">
                            <div class="card card-user-timeline">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <i data-feather="list" class="user-timeline-title-icon"></i>
                                        <h4 class="card-title">Noch nicht korrigierte Arbeiten</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <ul class="timeline ms-50">
                                        <?php
                                            if($submissions == NULL) {
                                            echo '<li class="timeline-item">
                                                    <span class="timeline-point timeline-point-indicator"></span>
                                                    <div class="timeline-event">
                                                        <h6>Keine offenen Arbeiten vorhanden.</h6>
                                                    </div>
                                                </li>';
                                        }
                                         foreach($submissions as $submission) {
                                            echo '<li class="timeline-item">
                                            <span class="timeline-point timeline-point-indicator"></span>
                                            <div class="timeline-event">
                                                <h6>' . $submission['name'] . '</h6>
                                                <p> Abgegeben am: <b>' . $submission['date'] . '</b></p>
                                            </div>
                                        </li>';
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!--/ Timeline Card -->
                    </div>
                </section>
                <!-- Dashboard Analytics end -->

            </div>
        </div>
    </div>
    <!-- END: Content-->

    <?php
    }
    ?>

<?php

if($getCurrentUserData['role'] != "Schüler") {
    $submissions = $db->getAllInboxSubmissions4Dashboard();
    ?>

    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- Dashboard Analytics Start -->
                <section id="dashboard-analytics">
                    <div class="row match-height">
                        <!-- Greetings Card starts -->
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <div class="card card-congratulations">
                                <div class="card-body text-center">
                                    <img src="app-assets/images/elements/decore-left.png" class="congratulations-img-left" alt="card-img-left" />
                                    <img src="app-assets/images/elements/decore-right.png" class="congratulations-img-right" alt="card-img-right" />
                                    <div class="avatar avatar-xl bg-primary shadow">
                                        <div class="avatar-content">
                                            <i data-feather="award" class="font-large-1"></i>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <h1 class="mb-1 text-white">Hallo <?=$getCurrentUserData['firstName']?>,</h1>
                                        <p class="card-text m-auto w-75">
                                            willkommen im Benutzerportal von ide4school. <br />
                                            <?php
                                            if($getCurrentUserData['role'] == "Lehrer") {
                                                echo 'Verwalte deine Klassen, deine Schüler & die Arbeiten deiner Schüler bequem über unsere Benutzeroberfläche.';
                                            }
                                            if($getCurrentUserData['role'] == "Administrator") {
                                                echo 'Verwalte deine Institution und alles was Schüler und Lehrer auf ide4school tun bequem über unsere Benutzeroberfläche.';
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Greetings Card ends -->

                        <!-- Subscribers Chart Card starts -->
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header flex-column align-items-start pb-0">
                                    <div class="avatar bg-light-primary p-50 m-0">
                                        <div class="avatar-content">
                                            <i data-feather="users" class="font-medium-5"></i>
                                        </div>
                                    </div><br />
                                    <p class="card-text">Aktuell sind</p>
                                    <h2 class="fw-bolder mt-1"><?=$db->countAllActiveUsers()?></h2>
                                    <p class="card-text">Benutzer angemeldet</p>
                                </div>
                            </div>
                        </div>
                        <!-- Subscribers Chart Card ends -->

                        <!-- Orders Chart Card starts -->
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header flex-column align-items-start pb-0">
                                    <div class="avatar bg-light-warning p-50 m-0">
                                        <div class="avatar-content">
                                            <i data-feather="alert-triangle" class="font-medium-5"></i>
                                        </div>
                                    </div><br />
                                    <p class="card-text">Aktuell gibt es</p>
                                    <h2 class="fw-bolder mt-1">0</h2>
                                    <p class="card-text">Probleme & Fehler</p>
                                </div>
                                
                            </div>
                        </div>
                        <!-- Orders Chart Card ends -->
                    </div>

                    <div class="row match-height">
                       

                    <div class="row match-height">
                        <!-- Timeline Card -->
                        <div class="col-lg-6 col-12">
                            <div class="card card-user-timeline">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <i data-feather="list" class="user-timeline-title-icon"></i>
                                        <h4 class="card-title"><?php
                                            if($getCurrentUserData['role'] == "Lehrer") {
                                                echo 'Neue Benachrichtigungen';
                                            }
                                            if($getCurrentUserData['role'] == "Administrator") {
                                                echo 'Letzte Logs';
                                            }
                                            ?></h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <ul class="timeline ms-50">
                                        <?php
                                    if($getCurrentUserData['role'] == "Lehrer") {
                                        if($notifications == NULL) {
                                            echo '<li class="timeline-item">
                                                    <span class="timeline-point timeline-point-indicator"></span>
                                                    <div class="timeline-event">
                                                        <h6>Keine Benachrichtigungen vorhanden.</h6>
                                                    </div>
                                                </li>';
                                        }
                                                foreach($notifications as $notification) {
                                                    echo '<li class="timeline-item">
                                                    <span class="timeline-point timeline-point-indicator"></span>
                                                    <div class="timeline-event">
                                                        <h6>' . $notification['heading'] . '</h6>
                                                        <p>' . $notification['text'] . '</p>
                                                    </div>
                                                </li>';
                                                }
                                            }
                                            if($getCurrentUserData['role'] == "Administrator") {
                                                if($logs == NULL) {
                                                    echo '<li class="timeline-item">
                                                            <span class="timeline-point timeline-point-indicator"></span>
                                                            <div class="timeline-event">
                                                                <h6>Keine Logeinträge vorhanden.</h6>
                                                            </div>
                                                        </li>';
                                                }
                                                foreach($logs as $log) {
                                                    echo '<li class="timeline-item">
                                                    <span class="timeline-point timeline-point-indicator"></span>
                                                    <div class="timeline-event">
                                                    <h6>' . $log['date'] . '</h6>
                                                    <p>' . $log['text'] . '</p>
                                                    </div>
                                                </li>';
                                                }
                                            }
                                            ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!--/ Timeline Card -->

                        <!-- Timeline Card -->
                        <div class="col-lg-6 col-12">
                            <div class="card card-user-timeline">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <i data-feather="list" class="user-timeline-title-icon"></i>
                                        <h4 class="card-title">Noch nicht korrigierte Arbeiten</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <ul class="timeline ms-50">
                                        <?php
                                            if($submissions == NULL) {
                                            echo '<li class="timeline-item">
                                                    <span class="timeline-point timeline-point-indicator"></span>
                                                    <div class="timeline-event">
                                                        <h6>Keine offenen Arbeiten vorhanden.</h6>
                                                    </div>
                                                </li>';
                                        }
                                         foreach($submissions as $submission) {
                                            echo '<li class="timeline-item">
                                            <span class="timeline-point timeline-point-indicator"></span>
                                            <div class="timeline-event">
                                                <h6><b>' . $submission['name'] . '</b></h6>
                                                <p>Abgegeben von: <b>'; $id = $submission['owner'];
                                                $owner = $db->getFullNameViaID($id);
                                                echo $owner; echo '</b></p>
                                                <p> Abgegeben am: <b>' . $submission['date'] . '</b></p>
                                            </div>
                                        </li>';
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!--/ Timeline Card -->
                    </div>
                </section>
                <!-- Dashboard Analytics end -->

            </div>
        </div>
    </div>
    <!-- END: Content-->

    <?php
    }
    ?>

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <?php include('inc/components/footer.php'); ?>


    <!-- BEGIN: Vendor JS-->
    <script src="app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="app-assets/vendors/js/charts/apexcharts.min.js"></script>
    <script src="app-assets/vendors/js/extensions/toastr.min.js"></script>
    <script src="app-assets/vendors/js/extensions/moment.min.js"></script>
    <script src="app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
    <script src="app-assets/vendors/js/tables/datatable/datatables.buttons.min.js"></script>
    <script src="app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js"></script>
    <script src="app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
    <script src="app-assets/vendors/js/tables/datatable/responsive.bootstrap5.js"></script>

    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="app-assets/js/core/app-menu.js"></script>
    <script src="app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->
    <?php include('inc/components/createEnviroment.php'); ?>
    <?php include('inc/components/createProject.php'); ?>


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