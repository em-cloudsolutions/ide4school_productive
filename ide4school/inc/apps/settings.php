<?php
if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }
if(!$db->isUserLoggedIn()) {
    header("Location: not_authorized");
}
if(!isset($_SESSION['login_state']) && $db->getMFAMethodsFromUser($_SESSION['user_id']) != NULL) {
    header("Location: 2fa");
}

if(!$db->isAdmin()) {
    header("Location: not_authorized");
}


$classes = $db->getAllClasses();
$getCurrentUserData = $db->getCurrentUserInformations();

// SESSION CLASS MANAGMENT - BEGIN
$teacher_classes = $db->getAllowedClassesForTeachers();

$db->checkAssignmentRights();
if(isset($_POST['updateSessionClass'])) {
    $new_class = $_POST['class_name'];
    $_SESSION['currentSessionClass'] = $new_class;
    header("Location: settings");
}
// SESSION CLASS MANAGMENT - END

$settings = $db->getAllSettings();
$features = $db->getAllFeatures();


if(isset($_POST['enableFeature'])) {
    $feature_id = $_POST['feature_id_enable'];
        if($db->enableFeature($feature_id)) {
            header("Location: settings");
        }
        else {
            echo'
            <div class="alert alert-danger" role="alert">
                                                <h4 class="alert-heading">Datenbankfehler</h4>
                                                <div class="alert-body">
                                                Funktionsstatus konnte nicht geändert werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                                </div>
                                            </div>';
        }
}

if(isset($_POST['disableFeature'])) {
    $feature_id = $_POST['feature_id_disable'];
        if($db->disableFeature($feature_id)) {
            header("Location: settings");
        }
        else {
            echo'
            <div class="alert alert-danger" role="alert">
                                                <h4 class="alert-heading">Datenbankfehler</h4>
                                                <div class="alert-body">
                                                Funktionsstatus konnte nicht geändert werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                                </div>
                                            </div>';
        }
}

if(isset($_POST['allowLogin'])) {
        if($db->allowLogin()) {
            header("Location: settings");
        }
        else {
            echo'
            <div class="alert alert-danger" role="alert">
                                                <h4 class="alert-heading">Datenbankfehler</h4>
                                                <div class="alert-body">
                                                Loginstatus konnte nicht geändert werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.de
                                                </div>
                                            </div>';
        }
}

if(isset($_POST['denyLogin'])) {
        if($db->denyLogin()) {
            header("Location: settings");
        }
        else {
            echo'
            <div class="alert alert-danger" role="alert">
                                                <h4 class="alert-heading">Datenbankfehler</h4>
                                                <div class="alert-body">
                                                Loginstatus konnte nicht geändert werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.de
                                                </div>
                                            </div>';
        }
}

if(isset($_POST['renameInstitution'])) {
    $new_name = $_POST['new_name_input'];
    if($db->renameInstitution($new_name)) {
        header("Location: settings");
    }
    else {
        echo'
        <div class="alert alert-danger" role="alert">
                                            <h4 class="alert-heading">Datenbankfehler</h4>
                                            <div class="alert-body">
                                            Institutionsname konnte nicht geändert werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.de
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
    <title>Einstellungen - <?=$db->getCurrentInstitution();?> - ide4school</title>
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
    <link rel="stylesheet" type="text/css" href="app-assets/css/plugins/extensions/ext-component-sweet-alerts.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/plugins/forms/form-validation.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <!-- END: Custom CSS-->

    <script src="assets/js/jquery-3.6.0.min.js"></script>

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="">

<script>

    function enableFeature(feature_id){
        document.getElementById("feature_id_enable").value = feature_id;
        document.getElementById("enableFeatureForm").submit();
    }

    function disableFeature(feature_id){
        document.getElementById("feature_id_disable").value = feature_id;
        document.getElementById("disableFeatureForm").submit();
    }

    function updateSessionClass(class_name){
        document.getElementById("class_name").value = class_name;
        document.getElementById("updateSessionClassForm").submit();
    }

    function uninstall(){
        window.location.href = "uninstall.php";
    }

    function allowLogin(){
        document.getElementById("allowLoginForm").submit();
    }

    function denyLogin(){
        document.getElementById("denyLoginForm").submit();
    } 

    function renameInstitution(){
        if(confirm("Institution wirklich umbennen?")) {
            new_name = prompt("Wie soll die neue Institution heißen?");
            if(new_name != "") {
                document.getElementById("new_name_input").value = new_name;
            }
        }
        document.getElementById("renameInstitutionForm").submit();
    }
    
</script>
<!-- Session class update hidden form -->
        <form action="settings" method="POST" id="updateSessionClassForm">
            <input name="class_name" type="text" hidden id="class_name">
            <input name="updateSessionClass" type="text" hidden id="updateSessionClass">
        </form>

        <form action="settings" method="POST" id="allowLoginForm">
            <input type="hidden" name="allowLogin" hidden>
        </form>

        <form action="settings" method="POST" id="denyLoginForm">
            <input type="hidden" name="denyLogin" hidden>
        </form>

        <form action="settings" method="POST" id="enableFeatureForm">
            <input name="feature_id_enable" type="text" hidden id="feature_id_enable">
            <input type="hidden" name="enableFeature" hidden>
        </form>

        
        <form action="settings" method="POST" id="renameInstitutionForm">
            <input name="new_name_input" type="text" hidden id="new_name_input">
            <input type="hidden" name="renameInstitution" hidden>
        </form>

        <form action="settings" method="POST" id="disableFeatureForm">
            <input name="feature_id_disable" type="text" hidden id="feature_id_disable">
            <input type="text" name="disableFeature" hidden>
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
                <li class=" nav-item active"><a class="d-flex align-items-center" href="settings"><i data-feather="settings"></i><span class="menu-title text-truncate" data-i18n="Settings">Einstellungen</span></a>
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
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header float-start mb-0">Einstellungen - <?=$db->getCurrentInstitution();?></h2>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="content-body">
                <div class="row">
                    <div class="col-12">
                

                        

                        <!-- payment methods -->
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Funktionen verwalten</h4><br />
                            </div>
                            <div class="card-body my-1 py-25">
                            <p class="text-truncate"><b>Kernfunktionen (z.B. Datei-/Benutzer-/Klassenverwaltung & die Entwicklungsumgebung) können nicht deaktiviert werden!<br /> Dafür können Sie aber folgende Zusatzfunktionen für Ihre Institution aktivieren:</b></p>
                                <div class="row gx-4">
                                    <div class="col-lg-12">
                                    <div class="table-responsive">
                            
                            <table class="table text-nowrap text-center border-bottom">
                                <thead>
                                    <tr>
                                        <th class="text-start">Funktion</th>
                                        <th>Aktiviert</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php

                                        foreach ($features as $feature) {

                                        echo '<tr>
                                        <td class="text-start"><b>' . $feature['feature_name'] . '</b><br /><br /><p class="text-truncate"><i>Beschreibung: ' . $feature['feature_description'] . '.</i></p></td>
                                        <td>
                                        <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="customSwitch1" '; if($feature['feature_status'] == "1"){echo 'checked';} if($feature['feature_status'] == "0"){echo ' OnClick="enableFeature(' . $feature['id'] . ')"';} if($feature['feature_status'] == "1"){echo ' OnClick="disableFeature(' . $feature['id'] . ')"';} echo '>
                                        </div>
                                        </td>

                                    </tr>';
                                }
                                    
                                    ?>
                                    
                                </tbody>
                            </table>
                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <!-- / payment methods -->

                       


                <!-- current plan -->
                <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Institutionsverwaltung <span class="badge rounded-pill badge-light-danger">Gefahrenzone</span></h4>
                            </div>
                            <div class="card-body my-2 py-25">
                                <div class="row">
                                    <div class="col-md-12">
                                        
                                    
                                    
                                    <?php
                                            foreach($settings as $setting) {
                                                if($setting['allow_login'] == 1) {
                                        echo '
                                        <div class="mb-1">
                                            <h5>Anmeldung sperren?</h5>
                                            <span>Schüler und Lehrer werden anschließend nicht mehr in der Lage sein sich anzumelden! <br />Bereits angemeldete Benutzer bleiben aber angemeldet! <br />Der Administrator kann sich immer anmelden.</span>
                                            </div>
                                                                            
                                        <button OnClick="denyLogin()" class="btn btn-primary me-1 mt-1" data-bs-toggle="modal" data-bs-target="#pricingModal">
                                            Anmeldung sperren
                                        </button>';
                                    }

                                    if($setting['allow_login'] == 0) {
                                        echo '
                                        <div class="mb-1">
                                            <h5>Anmeldung freigeben?</h5>
                                            <span>Alle Mitglieder Ihrer Institution werden sich wieder anmelden können.</span>
                                        </div>
                                        <button OnClick="allowLogin()" class="btn btn-primary me-1 mt-1" data-bs-toggle="modal" data-bs-target="#pricingModal">
                                            Anmeldung freigeben
                                        </button>';
                                    }
                                            }
                                        ?><br /><br /><br />
                                        <div class="mb-1">
                                            <h5>Institution umbennenen</h5>
                                            <span>Mit einem Klick auf diesen Button können Sie Ihrer Institution einen neuen Namen geben.</span>
                                            </div>
                                                                            
                                        <button OnClick="renameInstitution()" class="btn btn-primary me-1 mt-1" data-bs-toggle="modal" data-bs-target="#pricingModal">
                                            Institution umbenennen
                                        </button>';
                                        <div class="col-12">
                                        <br /><br />
                                            <h5>ide4school deinstallieren</h5>
                                            <span>Wenn du möchtest, kannst du ide4school ganz einfach über den Deinstallationsassistenten deinstallieren. Die Dateien aller Benutzer werden vorher auf ihr Gerät heruntergeladen.</span>
                                            <br /><button OnClick="uninstall()" class="btn btn-outline-danger cancel-subscription mt-1">Deinstallationsassistent öffnen</button>
                                            
                                        </div>
                                    
                                </div>
                            </div>
                        </div>
                        <!-- / current plan -->

                        </div>
                                        </div>

                

            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>
                            </div>
    <?php include('inc/components/footer.php'); ?>


    <!-- BEGIN: Vendor JS-->
    <script src="app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="app-assets/vendors/js/forms/select/select2.full.min.js"></script>
    <script src="app-assets/vendors/js/extensions/sweetalert2.all.min.js"></script>
    <script src="app-assets/vendors/js/forms/validation/jquery.validate.min.js"></script>
    <script src="app-assets/vendors/js/forms/cleave/cleave.min.js"></script>
    <script src="app-assets/vendors/js/forms/cleave/addons/cleave-phone.us.js"></script>
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