<?php
if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }
if(!$db->isUserLoggedIn()) {
    header("Location: not_authorized");
}
if(!isset($_SESSION['login_state']) && $db->getMFAMethodsFromUser($_SESSION['user_id']) != NULL) {
    header("Location: 2fa");
}

if(!$db->noStudent()) {
    header("Location: not_authorized");
}

$getCurrentUserData = $db->getCurrentUserInformations();

// SESSION CLASS MANAGMENT - BEGIN
$teacher_classes = $db->getAllowedClassesForTeachers();

$db->checkAssignmentRights();
if(isset($_POST['updateSessionClass'])) {
    $new_class = $_POST['class_name'];
    $_SESSION['currentSessionClass'] = $new_class;
    header("Location: classes");
}
// SESSION CLASS MANAGMENT - END


$currentInstitution = $db->getCurrentInstitution();

if(isset($_GET['edit'])) {
    $template_to_edit = $_GET['exam_id'];
    $template_data = $db->getExamContent($template_to_edit);
    $editable = true;
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
    <title>Prüfungsvorlage erstellen - <?php echo $db->getCurrentInstitution() ?> - ide4school</title>
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

    <link rel="stylesheet" type="text/css" href="app-assets/css/plugins/forms/form-wizard.css">    
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/forms/wizard/bs-stepper.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/forms/select/select2.min.css">
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
        <form action="classlist" method="POST" id="updateSessionClassForm">
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
    <h4 class="card-title">Prüfungsvorlage erstellen</h4>
    <div class="row">

        <div class="col-md-4 user_status">
        <label class="form-label" for="FilterTransaction">Erstelle jetzt eine Vorlage für deine nächste Prüfung.</label>
            
        </div>
        
     </div>
</div>
                       
<br /></div>
<div class="row">
<!-- Horizontal Wizard -->
<section class="horizontal-wizard">
                    <div class="bs-stepper horizontal-wizard-example">
                        <div class="bs-stepper-header" role="tablist">
                            <div class="step" data-target="#account-details" role="tab" id="account-details-trigger">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-box">1</span>
                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">Aufgaben konfigurieren</span>
                                        <span class="bs-stepper-subtitle">Erstelle die Aufgaben der Prüfung.</span>
                                    </span>
                                </button>
                            </div>
                            <div class="line">
                                <i data-feather="chevron-right" class="font-medium-2"></i>
                            </div>
                            
                           
                            <div class="step" data-target="#address-step" role="tab" id="address-step-trigger">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-box">2</span>
                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">Weitere Einstellungen festlegen</span>
                                        <span class="bs-stepper-subtitle">Lege bspw. ein Zeitlimit fest.</span>
                                    </span>
                                </button>
                            </div>
                            <div class="line">
                                <i data-feather="chevron-right" class="font-medium-2"></i>
                            </div>
                            <div class="step" data-target="#social-links" role="tab" id="social-links-trigger">
                                <button type="button" class="step-trigger">
                                    <span class="bs-stepper-box">3</span>
                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">Prüfung fertigstellen</span>
                                        <span class="bs-stepper-subtitle">Add Social Links</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                        <div class="bs-stepper-content">
                            <div id="account-details" class="content" role="tabpanel" aria-labelledby="account-details-trigger">
                                <div class="content-header">
                                    <h5 class="mb-0">Aufgaben konfigurieren</h5>
                                    <small class="text-muted">Bitte gebe jetzt weitere Details an.</small>
                                </div>
                                <form id="taskForm">
                                    <div class="row" id="aufgabenerstellung">
                                    <?php
if ($editable) {
    $data = json_decode($template_data['json_content'], true);

    if (isset($data['tasks']) && is_array($data['tasks'])) {
        foreach ($data['tasks'] as $index => $task) {
            $counter = $index + 1; // Aufgabennummer basierend auf dem Index

            // Feldwerte aus dem JSON-Code abrufen
            $taskName = isset($task['name']) ? $task['name'] : '';
            $taskType = isset($task['type']) ? $task['type'] : '';
            $taskDescription = isset($task['description']) ? $task['description'] : '';
            $taskVof = isset($task['vof']) ? $task['vof'] : false;

            // Erstelle das neue Aufgabenelement
            echo '<div class="col-md-6 col-12 mb-1" id="aufgabe_' . $counter . '">';
            echo '<fieldset>';
            echo '<div class="input-group">';
            echo '<input type="text" class="form-control" readonly="" value="Aufgabe ' . $counter . '" aria-label="Amount" aria-invalid="false">';
            echo '<select class="form-select" id="basicSelect" name="typ_aufgabe_' . $counter . '">';
            echo '<option value="theorie" class="dropdown-item" ' . ($taskType === 'theorie' ? 'selected' : '') . '>Theorie (Text)</option>';
            echo '<option value="praxis" class="dropdown-item" ' . ($taskType === 'praxis' ? 'selected' : '') . '>Praxis (Code)</option>';
            echo '<option value="struktogramm" class="dropdown-item" ' . ($taskType === 'struktogramm' ? 'selected' : '') . '>Struktogramm</option>';
            echo '<option value="anderes" class="dropdown-item" ' . ($taskType === 'anderes' ? 'selected' : '') . '>Etwas anderes</option>';
            echo '</select>';
            echo '</div><br />';
            echo '<div class="input-group mb-2">';
            echo '<span class="input-group-text" id="basic-addon3" name="name_aufgabe_' . $counter . '">Aufgabe: </span>';
            echo '<input type="text" class="form-control" name="name_aufgabe_' . $counter . '" placeholder="Trage hier deine Aufgabenstellung ein..." id="basic-url3" aria-describedby="basic-addon3" value="' . $taskName . '">';
            echo '</div>';
            echo '<div class="form-check form-switch">';
            echo '<input type="checkbox" class="form-check-input" name="vof_aufgabe_' . $counter . '" id="customSwitch' . $counter . '" ' . ($taskVof ? 'checked' : '') . '>';
            echo '<label class="form-check-label" for="customSwitch' . $counter . '">Verwendung von alten Benutzerdateien erlauben</label>';
            echo '</div>';
            if ($counter != 1) {
                echo '<a href="#" onclick="removeTaskField(\'aufgabe_' . $counter . '\', \'beschreibung_' . $counter . '\')">Aufgabe entfernen</a>';
            }
            echo '</fieldset>';
            echo '</div>';
            echo '<div class="col-md-6 col-12 mb-1" id="beschreibung_' . $counter . '">';
            echo '<div class="input-group">';
            echo '<span class="input-group-text" name="description_aufgabe_' . $counter . '">Beschreibung der Aufgabe: </span>';
            echo '<textarea class="form-control" placeholder="Beschreibe deine Aufgabe näher, um den Schüler:innen ggf. auftretende Fragen direkt zu beantworten..." aria-label="With textarea">' . $taskDescription . '</textarea>';
            echo '</div>';
            echo '<br />';
            echo '</div>';
        }
    }
                                    } else {
                                        ?>
                                        <div class="col-md-6 col-12 mb-1">
                                            <fieldset>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="txt_aufgabe" readonly="" value="Aufgabe 1" aria-label="Amount" aria-invalid="false">
                                                    <select class="form-select" id="basicSelect" name="typ_aufgabe_1">
                                                        <option value="theorie" class="dropdown-item">Theorie (Text)</option>
                                                        <option value="praxis" class="dropdown-item">Praxis (Code)</option>
                                                        <option value="struktogramm" class="dropdown-item">Struktogramm</option>
                                                        <option value="anderes" class="dropdown-item">Etwas anderes</option>
                                                    </select>
                                                </div><br />
                                                <div class="input-group mb-2">
                                                    <span class="input-group-text" id="basic-addon3" >Aufgabe: </span>
                                                    <input type="text" class="form-control" name="name_aufgabe_1" placeholder="Trage hier deine Aufgabenstellung ein..." id="basic-url3" aria-describedby="basic-addon3">
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" class="form-check-input" name="vof_aufgabe_1" id="customSwitch1">
                                                    <label class="form-check-label" for="customSwitch1">Verwendung von alten Benutzerdateien erlauben</label>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-md-6 col-12 mb-1">
                                            <div class="input-group">
                                                <span class="input-group-text" >Beschreibung der Aufgabe: </span>
                                                <textarea class="form-control" name="description_aufgabe_1" placeholder="Beschreibe deine Aufgabe näher, um den Schüler:innen ggf. auftretende Fragen direkt zu beantworten..." aria-label="With textarea"></textarea>
                                            </div>
                                            <br />
                                        </div>
                                        <?php
                                    }
                                    ?>


                                    </div>
                                </form>
                                <button type="button" onclick="addTaskField()" class="btn btn-success waves-effect waves-float waves-light">Aufgabe hinzufügen</button>
                                <br /><br />
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-outline-secondary btn-prev" disabled>
                                        <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                                        <span class="align-middle d-sm-inline-block d-none">Zurück</span>
                                    </button>
                                    <button class="btn btn-primary btn-next">
                                        <span class="align-middle d-sm-inline-block d-none">Weiter</span>
                                        <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div id="address-step" class="content" role="tabpanel" aria-labelledby="address-step-trigger">
                                <div class="content-header">
                                    <h5 class="mb-0">Weitere Einstellungen</h5>
                                    <small>Lege ein paar weitere Einstellungen für deine Prüfung fest.</small>
                                </div>
                                <form>
                                <?php
                                if ($editable) {
                                    $data = json_decode($template_data['json_content'], true);

                                    // Feldwerte aus dem JSON-Code abrufen
                                    $examTime = isset($data['settings']['time']) ? $data['settings']['time'] : '';
                                    $examTitle = isset($data['settings']['title']) ? $data['settings']['title'] : '';
                                    $examExactOrder = isset($data['settings']['exact_order']) ? $data['settings']['exact_order'] : false;
                                    $examMessage = isset($data['settings']['message']) ? $data['settings']['message'] : '';
                                
                                ?>

                                <div id="exam_options">
                                    <div class="row">
                                        <div class="mb-1 col-md-6">
                                            <label class="form-label" for="exam_time">Zeit zum bearbeiten aller Aufgaben (in Minuten)</label>
                                            <input type="text" name="exam_time" id="exam_time" class="form-control" placeholder="45" value="<?php echo $examTime; ?>" />
                                        </div>
                                        <div class="mb-1 col-md-6">
                                            <label class="form-label" for="exam_title">Arbeitstitel (z.B. 1. Klassenarbeit im Fach Informatik)</label>
                                            <input type="text" id="exam_title" class="form-control" placeholder="1. Klassenarbeit im Fach Informatik" value="<?php echo $examTitle; ?>" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-1 col-md-6">
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="exam_exact_order" id="exam_exact_order" <?php echo $examExactOrder ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="exam_exact_order">Aufgaben nacheinander abarbeiten<br />(Sobald eine Aufgabe abgegeben wurde, ist sie für den Schüler nicht mehr einsehbar)</label>
                                            </div>
                                        </div>
                                        <div class="mb-1 col-md-6">
                                            <label class="form-label" for="exam_message">Arbeitsnachricht an Schüler (eine Art Willkommensnachricht mit weiteren Instruktionen)</label><br />
                                            <textarea class="form-control" id="exam_message" name="exam_message" id="exam_welcome_message" placeholder="Liebe Schüler, ihr habt nun 45 Minuten Zeit die folgenden Aufgaben zu erledigen. Bitte achtet auf die Zeit und geht eure Aufgaben nacheinander an. Maximale Erfolge!"><?php echo $examMessage; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                } else {
                                ?>

                                    <div id="exam_options">
                                        <div class="row">
                                        <div class="mb-1 col-md-6">
                                                <label class="form-label" for="exam_time">Zeit zum bearbeiten aller Aufgaben (in Minuten)</label>
                                                <input type="text" name="exam_time" id="exam_time" class="form-control" placeholder="45" />
                                            </div>
                                            <div class="mb-1 col-md-6">
                                                <label class="form-label" for="exam_title">Arbeitstitel (z.B. 1. Klassenarbeit im Fach Informatik)</label>
                                                <input type="text" id="exam_title" class="form-control" placeholder="1. Klassenarbeit im Fach Informatik" />
                                            </div>
                                            
                                        </div>
                                        <div class="row">
                                            <div class="mb-1 col-md-6">
                                                
                                                <div class="form-check form-switch">
                                                            <input type="checkbox" class="form-check-input" name="exam_exact_order" id="exam_exact_order">
                                                            <label class="form-check-label" for="exam_exact_order">Aufgaben nacheinander abarbeiten<br />(Sobald eine Aufgabe abgegeben wurde, ist sie für den Schüler nicht mehr einsehbar)</label>
                                                        </div>
                                                        
                                                </div>

                                                
                                                <div class="mb-1 col-md-6">
                                                <label class="form-label" for="exam_welcome_message">Arbeitsnachricht an Schüler (eine Art Willkommensnachricht mit weiteren Instruktionen)</label><br />
                                                            <textarea class="form-control" id="exam_message" name="exam_message" id="exam_welcome_message" placeholder="Liebe Schüler, ihr habt nun 45 Minuten Zeit die folgenden Aufgaben zu erledigen. Bitte achtet auf die Zeit und geht eure Aufgaben nacheinander an. Maximale Erfolge!" ></textarea>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <?php
                                }
                                    ?>
                                </form>
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-primary btn-prev">
                                        <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                                        <span class="align-middle d-sm-inline-block d-none">Zurück</span>
                                    </button>
                                    <button  class="btn btn-primary btn-next">
                                        <span class="align-middle d-sm-inline-block d-none">Weiter</span>
                                        <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="social-links" class="content" role="tabpanel" aria-labelledby="social-links-trigger">
                                <div class="content-header">
                                    <h5 class="mb-0">Prüfung fertigstellen</h5>
                                    <small>Stelle deine Prüfungsvorlage fertig.</small>
                                </div>
                                <?php
                                if($editable) {
                                    $exam_title = $template_data['title'];
                                    $exam_comment = $template_data['comment'];
                                ?>
                                <div class="row">
                                        <div class="mb-1 col-md-6">
                                            <label class="form-label" for="exam_library_title">Titel der Prüfung (zur Ansicht in der Prüfungsvorlagen Bibliothek)</label>
                                            <input type="text" id="exam_library_title" class="form-control" value="<?=$exam_title?>" placeholder="1. Klausur - Informatik Kurs 2024 Lehrer XY" />
                                        </div>
                                        
                                </div>
                                <div class="row">
                                            
                                        <div class="mb-1 col-md-6">
                                            <label class="form-label" for="exam_library_comment">Kurze Beschreibung der Prüfung (zur Ansicht in der Prüfungsvorlagen Bibliothek)</label><br />
                                            <textarea class="form-control" id="exam_library_comment" name="exam_library_comment" placeholder="Klausur beinhaltet folgende Themen: Verschlüsselung, Rekursive Funktionen, Palindrom Checker" ><?=$exam_comment?></textarea>
                                        </div>
                                        
                                </div>
                                <?php
                                } else {
                                    ?>
                                    <div class="row">
                                        <div class="mb-1 col-md-6">
                                            <label class="form-label" for="exam_library_title">Titel der Prüfung (zur Ansicht in der Prüfungsvorlagen Bibliothek)</label>
                                            <input type="text" id="exam_library_title" class="form-control" placeholder="1. Klausur - Informatik Kurs 2024 Lehrer XY" />
                                        </div>
                                        
                                </div>
                                <div class="row">
                                            
                                        <div class="mb-1 col-md-6">
                                            <label class="form-label" for="exam_library_comment">Kurze Beschreibung der Prüfung (zur Ansicht in der Prüfungsvorlagen Bibliothek)</label><br />
                                            <textarea class="form-control" id="exam_library_comment" name="exam_library_comment" placeholder="Klausur beinhaltet folgende Themen: Verschlüsselung, Rekursive Funktionen, Palindrom Checker" ></textarea>
                                        </div>
                                        
                                </div>
                                <?php
                                }
                                ?>
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-primary btn-prev">
                                        <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                                        <span class="align-middle d-sm-inline-block d-none">Zurück</span>
                                    </button>
                                    <button onClick="createExam()" class="btn btn-success btn-submit">Prüfung erstellen</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- /Horizontal Wizard -->
        
    </div>
                        
                       
                    
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
    <script src="app-assets/vendors/js/forms/wizard/bs-stepper.min.js"></script>

    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="app-assets/js/core/app-menu.js"></script>
    <script src="app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->
    <script src="app-assets/js/scripts/forms/form-wizard.js"></script>


        <script>
   // JavaScript-Code
   let counter = 1; // Zählervariable für Aufgabennummer

function addTaskField() {
  counter++; // Erhöhe den Zähler

  // Erstelle das neue Aufgabenelement
  let newField = document.createElement("div");
  newField.className = "col-md-6 col-12 mb-1";
  let aufgabeId = "aufgabe_" + counter;
  newField.id = aufgabeId;

  // HTML-Code für das neue Aufgabenelement
  newField.innerHTML = `
    <br /><hr><br />
    <fieldset>
      <div class="input-group">
        <input type="text" class="form-control" readonly="" value="Aufgabe ${counter}" aria-label="Amount" aria-invalid="false">
        <select class="form-select" id="basicSelect" name="typ_aufgabe_${counter}">
          <option value="theorie" class="dropdown-item">Theorie (Text)</option>
          <option value="praxis" class="dropdown-item">Praxis (Code)</option>
          <option value="struktogramm" class="dropdown-item">Struktogramm</option>
          <option value="anderes" class="dropdown-item">Etwas anderes</option>
        </select>
      </div><br />
      <div class="input-group mb-2">
        <span class="input-group-text" id="basic-addon3" >Aufgabe: </span>
        <input type="text" class="form-control" name="name_aufgabe_${counter}" placeholder="Trage hier deine Aufgabenstellung ein..." id="basic-url3" aria-describedby="basic-addon3">
      </div>
      <div class="form-check form-switch">
        <input type="checkbox" class="form-check-input" name="vof_aufgabe_${counter}" id="customSwitch${counter}">
        <label class="form-check-label" for="customSwitch${counter}">Verwendung von alten Benutzerdateien erlauben</label>
      </div><br />
      <a href="#" onclick="removeTaskField('${aufgabeId}', 'beschreibung_${counter}')">Aufgabe entfernen</a>
    </fieldset>
  `;

  let newField1 = document.createElement("div");
  newField1.className = "col-md-6 col-12 mb-1";
  let beschreibungId = "beschreibung_" + counter;
  newField1.id = beschreibungId;

  newField1.innerHTML = ` <br /><hr><br />
    <div class="input-group">
      <span class="input-group-text" name="description_aufgabe_${counter}">Beschreibung der Aufgabe: </span>
      <textarea class="form-control" placeholder="Beschreibe deine Aufgabe näher, um den Schüler:innen ggf. auftretende Fragen direkt zu beantworten..." aria-label="With textarea"></textarea>
    </div>
    <br /><br />
  `;

  // Füge das neue Aufgabenelement hinzu
  document.getElementById("aufgabenerstellung").appendChild(newField);
  document.getElementById("aufgabenerstellung").appendChild(newField1);

  // Aktualisiere die Nummerierung der vorhandenen Aufgaben
  updateTaskNumbering();
}

function removeTaskField(aufgabeId, beschreibungId) {
  // Entferne das Aufgabenelement und das dazugehörige Beschreibungselement
  let aufgabeElement = document.getElementById(aufgabeId);
  let beschreibungElement = document.getElementById(beschreibungId);

  aufgabeElement.parentNode.removeChild(aufgabeElement);
  beschreibungElement.parentNode.removeChild(beschreibungElement);

  // Aktualisiere die Nummerierung der verbleibenden Aufgaben
  updateTaskNumbering();
}

function updateTaskNumbering() {
  let aufgaben = document.querySelectorAll("#aufgabenerstellung fieldset");

  // Aktualisiere die Nummerierung für jede Aufgabe
  aufgaben.forEach((aufgabe, index) => {
    let aufgabenNummer = aufgabe.querySelector("input[type=text]");
    aufgabenNummer.value = `Aufgabe ${index + 1}`;
    aufgabenNummer.setAttribute("value", `Aufgabe ${index + 1}`);
  });
}





function getAllTaskValues() {
  // Hole alle Aufgabenfelder, den Typ, den Namen und die Beschreibung
  let taskFields = document.querySelectorAll("#aufgabenerstellung fieldset");
  let taskTypes = document.querySelectorAll("#aufgabenerstellung select");
  let taskNames = document.querySelectorAll("#aufgabenerstellung input[name^=name_aufgabe]");
  let taskDescriptions = document.querySelectorAll("#aufgabenerstellung textarea");
  let taskVof = document.querySelectorAll("#aufgabenerstellung input[name^=vof_aufgabe]");

  // Erstelle ein Objekt für die Einstellungen
  let settings = {
    title: "",
    message: "",
    exact_order: false,
    time: 0
  };

  // Hole die Werte der Einstellungen
  settings.title = document.getElementById("exam_title").value;
  settings.message = document.getElementById("exam_message").value;
  settings.exact_order = document.getElementById("exam_exact_order").checked;
  settings.time = parseInt(document.getElementById("exam_time").value);

  // Erstelle ein Objekt für das Exam
  let exam = {
    settings: settings,
    tasks: []
  };

  // Gehe alle Aufgabenfelder durch
  for (let i = 0; i < taskFields.length; i++) {
    // Erstelle ein Objekt für die Aufgabe
    let task = {};

    // Füge den Typ, den Namen, die Beschreibung und den vof-Schalter der Aufgabe hinzu
    task.name = taskNames[i].value;
    task.type = taskTypes[i].value;
    task.description = taskDescriptions[i].value;
    task.vof = taskVof[i].checked;

    // Füge die Aufgabe dem Array hinzu
    exam.tasks.push(task);
  }

  // Gebe das Objekt mit den Einstellungen und Aufgaben zurück
  return exam;
}


function createExam() {
    //Neme das Objekt exam und speichere es in der Datenbank

    var exam = getAllTaskValues();
    var examJson = JSON.stringify(exam);
    // Sende dieses Obejkt per AJAX an exam_library mit POST
    $.ajax({
        type: "POST",
        url: "exam_library",
        //data mit dem objekt exam und dann zwei variablen exam_titel und exam_comment
        data: {
            exam: examJson,
            exam_title: document.getElementById("exam_library_title").value,
            exam_comment: document.getElementById("exam_library_comment").value,
            <?php
            if($editable) {
                echo 'exam_id: ' . $template_to_edit . ',';
                echo 'editExam: true';
            }
            else {
                echo 'createExam: true';
            }
            ?>
        },
        success: function(data) {
            // Wenn die Antwort erfolgreich ist, dann gebe eine Erfolgsmeldung aus
            window.location.href = "exam_library";
        }
    });
}

    

  </script>

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