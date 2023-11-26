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
        header("Location: focus_lobby&return_to=submissions");
    }
}


    
    if($db->isSubmissionFunktionEnabled() == false) {
        header("Location: feature_not_active");
    }


    $submission_id = $_GET['id'];

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


$submission_data = $db->getSubmission($submission_id);
$submission_file_path = $submission_data['path'];
if(is_file($submission_file_path)) {
    $submission_code = file_get_contents($submission_file_path);
}
else {
    $submission_code = "Datei konnte nicht geladen werden! Eventuell wurde sie gelöscht oder verschoben!<br /><br />";
}

$avatar_id = $db->getUserAvatar($submission_data['owner']);

if(isset($_POST["returnSubmission"]))
{
    $id = $submission_id;
    $return_comment = $_POST['return_comment'];
    $return_grade = $_POST['return_grade'];
    if($db->returnSubmission($id, $return_comment, $return_grade)) {
        header("Location: submission&id=$submission_id");
            }
    else {
        echo'
        <div class="alert alert-danger" role="alert">
                                            <h4 class="alert-heading">Datenbankfehler</h4>
                                            <div class="alert-body">
                                            Abgabe konnte nicht zurückgegeben werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                            </div>
                                        </div>';
        sleep(3);
        header("Location: submission&id=$submission_id");
    }
};

if(isset($_POST["deleteSubmission"]))
{
    $id = $_POST['delete_submission_id'];
    if($db->deleteSubmission($id)) {
        // -- Delete command 
        $unlink_path = $submission_file_path;
        unlink($unlink_path);
        header("Location: submissions");
    }
    else {
        echo'
        <div class="alert alert-danger" role="alert">
                                            <h4 class="alert-heading">Datenbankfehler</h4>
                                            <div class="alert-body">
                                            Abgabe konnte nicht gelöscht werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                            </div>
                                        </div>';
        sleep(3);
        header("Location: submission&id=$submission_id");
    }
};
 

if($submission_data == NULL) {
    header("Location: submissions");
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
    <title><?=$submission_data['name']?> - Abgabenansicht - <?=$getCurrentUserData['institution']?> - ide4school</title>
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

    function showSubmission(submission_path){
        document.getElementById("show_submission_path").value = submission_path;
        document.getElementById("showSubmissionForm").submit();
    }

    function deleteSubmission(submission_id){
        if(confirm("Abgabe wirklich löschen? Auch der Schüler / die Schülerin wird die Abgabe nicht mehr einsehen können!")) {
        document.getElementById("delete_submission_id").value = submission_id;
        document.getElementById("deleteSubmissionForm").submit();
        }
    }

</script>



<!-- Session class update hidden form -->
<form action="submission&id=<?=$submission_id?>" method="POST" id="updateSessionClassForm">
            <input name="class_name" type="text" hidden id="class_name">
            <input name="updateSessionClass" type="text" hidden id="updateSessionClass">
        </form>

        <form action="ide" target="_blank" method="POST" id="showSubmissionForm">
            <input name="show_submission_path" type="text" hidden id="show_submission_path">
            <input name="showSubmission" type="text" hidden id="showSubmission">
        </form>

        <form action="submission&id=<?=$submission_id?>" method="POST" id="deleteSubmissionForm">
            <input name="delete_submission_id" type="text" hidden id="delete_submission_id">
            <input name="deleteSubmission" type="text" hidden id="deleteSubmission">
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
                    echo '<li class=" nav-item active"><a class="d-flex align-items-center" href="submissions"><i data-feather="inbox"></i><span class="menu-title text-truncate" data-i18n="Submissions">Abgaben</span></a>
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
                                            <img class="img-fluid rounded mt-3 mb-2" src="app-assets/images/avatars/<?=$avatar_id?>.png" height="110" width="110" alt="User avatar" />
                                            <div class="user-info text-center">
                                                <h4><?=$submission_data['name']?></h4>
                                                <?php
                                                if($submission_data['status'] == "1") {
                                                 echo '<span class="badge bg-light-success">Zurückgegeben</span>';
                                                }

                                                if($submission_data['status'] == "0") {
                                                    echo '<span class="badge bg-light-danger">Nicht zurückgegeben</span>';
                                                   }

                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <h4 class="fw-bolder border-bottom pb-50 mb-1">Details</h4>
                                    <div class="info-container">
                                        <ul class="list-unstyled">


                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Name:</span>
                                                <span><?=$submission_data['name']?></span>
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Abgegeben von:</span>
                                                <span>
                                                    <?php
                                                $id = $submission_data['owner'];
                                                $owner = $db->getFullNameViaID($id);
                                                echo $owner;
                                                ?>
                                                </span>
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Abgegeben am:</span>
                                                <span><?=$submission_data['date']?></span>
                                            </li>
                                    
                                            
                                        </ul>
                                        <br /><?php
                                        if($getCurrentUserData['role'] == "Lehrer" OR $getCurrentUserData['role'] == "Administrator") {
                                        ?>
                                        <form action="submission&id=<?=$submission_id?>" method="POST">
                                        <?php
                                        if($submission_data['status'] == "0") {
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
                                                <textarea class="form-control" name="return_comment" id="exampleFormControlTextarea1" rows="3" placeholder="Schreiben Sie etwas..."></textarea>
                                            </div>
                                        </div>

                            </div>
                                    </div>
                                    
                                        <div class="d-flex justify-content-center pt-2">
                                            <button type="submit" name="returnSubmission" value="returnSubmission" class="btn btn-primary me-1">
                                                Zurückgeben
                                        </button>
                                        
                                        <a OnClick="deleteSubmission(<?=$submission_data['id']?>)" class="btn btn-outline-danger suspend-user">Löschen</a>
                                        
                                        </form>
                                        </div>
                                    </div>
                                    <?php
                                        }
                                        if($submission_data['status'] == "1") {
                                            ?>
                                         <h4 class="fw-bolder border-bottom pb-50 mb-1">Kommentare & Benotung</h4>
                                    <div class="info-container">
                                                                    <div class="row">
                                        <div class="col-12">
                                        <ul class="list-unstyled">
                                        <li class="mb-75">
                                                <span class="fw-bolder me-25">Zurückgegeben am:</span>
                                                <?php if($submission_data['status'] == "1") {
                                                echo' <span>' . $submission_data['return_date'] . '</span>';
                                                }
                                                else {
                                                    echo' <span>Nicht bewertet</span>';
                                                }
                                                ?>
                                                
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Zurückgegeben von:</span>
                                                <?php if($submission_data['status'] == "1") {
                                                echo' <span>'; $id = $submission_data['return_from'];
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
                                                <?php if($submission_data['status'] == "1") {
                                                echo' <span>' . $submission_data['return_grade'] . '</span>';
                                                }
                                                else {
                                                    echo' <span>Noch nicht bewertet</span>';
                                                }
                                                ?>
                                                
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Kommentare / Verbesserungen:</span>
                                                <?php if($submission_data['status'] == "1") {
                                                echo' <span>' . $submission_data['return_comment'] . '</span>';
                                                }
                                                else {
                                                    echo' <span>Noch nicht bewertet</span>';
                                                }
                                                ?>
                                            </li>
                                        </ul>
                                        </div> </div> </div> </div>
                                        <div class="d-flex justify-content-center pt-2">
                                        <a OnClick="deleteSubmission(<?=$submission_data['id']?>)" class="btn btn-outline-danger suspend-user">Löschen</a>
                                        </div>

                            
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
                                                <?php if($submission_data['status'] == "1") {
                                                echo' <span>' . $submission_data['return_date'] . '</span>';
                                                }
                                                else {
                                                    echo' <span>Nicht bewertet</span>';
                                                }
                                                ?>
                                                
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Zurückgegeben von:</span>
                                                <?php if($submission_data['status'] == "1") {
                                                echo' <span>'; $id = $submission_data['return_from'];
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
                                                <?php if($submission_data['status'] == "1") {
                                                echo' <span>' . $submission_data['return_grade'] . '</span>';
                                                }
                                                else {
                                                    echo' <span>Nicht bewertet</span>';
                                                }
                                                ?>
                                                
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Kommentare / Verbesserungen:</span>
                                                <?php if($submission_data['status'] == "1") {
                                                echo' <span>' . $submission_data['return_comment'] . '</span>';
                                                }
                                                else {
                                                    echo' <span>Nicht bewertet</span>';
                                                }
                                                ?>
                                            </li>
                                        </ul>
                                        </div>

                            </div>
                                    </div>
                                    </form>
                                   
                                    </div>
                                    <?php
                                        }
                                        ?>
                                </div>
                            </div>
                            <!-- /User Card -->
                            
                        </div>
                        <!--/ User Sidebar -->

                        <!-- User Content -->
                        <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
                            <!-- Code box -->
                            <div class="card">
                                <h4 class="card-header">Inhalt:</h4>

                                <div class="table-responsive">
                                <code>
                                    <pre style="margin-left: 1%; margin-right: 1%;"><br/><div style="margin-left: 2%; margin-right: 2%;"><?php echo $submission_code; ?></div></pre>
                                                </code>
                                                
                                </div>
                                <button OnClick="showSubmission('<?=$submission_data['path']?>')" style="float: right;" class="dt-button add-new btn btn-primary" tabindex="0" aria-controls="DataTables_Table_0" type="button"><span>In der Entwicklungsumgebung ansehen</span></button> 
                            </div>
                            <!-- /Code box -->
                            
                            <?php
                                if ($getCurrentUserData['role'] != "Schüler") {

                                    echo '
                                    <div class="card">
                                        <div id="DataTables_Table_1_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                                            <div class="card-header pt-1 pb-25">
                                                <div class="head-label">
                                                    <h4 class="card-title">Mögliche Plagiate</h4>
                                                </div>
                                                <div class="dt-action-buttons text-end"></div>
                                            </div>
                                            <table class="invoice-table table text-nowrap dataTable no-footer dtr-column" id="DataTables_Table_1" role="grid">
                                                <thead>
                                                    <tr role="row">
                                                        <th tabindex="0" rowspan="1" colspan="1" style="width: 46px;">Abgabe:</th>
                                                        <th tabindex="0" aria-controls="DataTables_Table_1" rowspan="1" colspan="1" style="width: 73px;">Abgegeben von:</th>
                                                        <th  tabindex="0" aria-controls="DataTables_Table_1" rowspan="1" colspan="1" style="width: 130px;">Abgegeben am:</th>
                                                        <th  rowspan="1" colspan="1" style="width: 80px;">Übereinstimmung:</th>
                                                    </tr>
                                                </thead>
                                                <tbody>';

                                    // PLAGIAT CHECK
                                    if (glob('files/submissions/*/*')) {
                                        foreach (glob('files/submissions/*/*') as $extra_file) {
                                            if ($extra_file == $submission_file_path) {
                                                continue; // Springe zur nächsten Iteration der Schleife
                                            }

                                            // Dateien einlesen
                                            $files = array();
                                            $files[] = file_get_contents($extra_file);
                                            $files[] = file_get_contents($submission_file_path);

                                            $file1 = $extra_file;
                                            $file2 = $submission_file_path;

                                            // Dateien vergleichen (ohne Diff())
                                            $similarity = similar_text($files[0], $files[1], $percent);

                                            $plagiat_id = $db->getSubmissionIDViaPath($extra_file);
                                            $plagiat_name = $db->getSubmissionNameViaPath($extra_file);
                                            $plagiat_owner = $db->getSubmissionOwnerViaPath($extra_file);
                                            $plagiat_owner_name = $db->getFullNameViaID($plagiat_owner);
                                            $plagiat_date = $db->getSubmissionDateViaPath($extra_file);
                                            $plagiat_matchrate = round($percent, 2) . "%";
                                            $plagiat_matchrate_calc = round($percent, 2);
                                            // Ausgabe
                                            if ($percent > 75 && $submission_id != $plagiat_id) {
                                                echo '<tr role="row" class="odd">
                                                            <td class="sorting_1">
                                                                <a data-bs-toggle="modal" data-bs-target="#plagiat_' . $plagiat_id . '" href="javascript:void(0);">' . $plagiat_name . '</a>
                                                            </td>
                                                            <td>' . $plagiat_owner_name . '</td>
                                                            <td>' . $plagiat_date . '</td>
                                                            <td class="cell-fit"><span ';
                                                if ($percent <= 80) echo 'class="badge badge-light-primary"';
                                                if ($percent <= 90 && $percent > 80) echo 'class="badge badge-light-warning"';
                                                if ($percent <= 100 && $percent > 90) echo 'class="badge badge-light-danger"';
                                                echo '>' . $plagiat_matchrate . '</span></td>
                                                        </tr>
                                                        ';
                                            }
                                            else {
                                                //Keine Plagiate gefunden ausgeben
                                                echo '<tr role="row" class="odd">
                                                <td>Keine großen Übereinstimmungen gefunden.</td>
                                                </tr>';
                                            }

                                            

                                            $file1Content = file_get_contents($file1);
                                            $file2Content = file_get_contents($file2);

                                            // Dateien in einzelne Zeilen aufteilen
                                            $file1Lines = explode(PHP_EOL, $file1Content);
                                            $file2Lines = explode(PHP_EOL, $file2Content);


                                            // Array für die Übereinstimmungen
                                            $matchingLines = array();

                                            // Durchlaufe alle Zeilen der ersten Datei
                                            foreach ($file1Lines as $lineIndex => $line) {
                                                $matchingPhrases = array();

                                                // Durchlaufe alle Zeilen der zweiten Datei
                                                foreach ($file2Lines as $compareLineIndex => $compareLine) {
                                                    // Vergleiche die beiden Zeilen
                                                    similar_text($line, $compareLine, $percent);
                                                    // Falls die Übereinstimmung größer als 80% ist
                                                    if ($percent > 80) {
                                                        // Füge die Übereinstimmung dem Array hinzu
                                                        $matchingLines[] = array(
                                                            'file1LineIndex' => $lineIndex,
                                                            'file2LineIndex' => $compareLineIndex,
                                                            'percent' => $percent
                                                        );

                                                        // Füge die übereinstimmenden Phrasen dem Array hinzu
                                                        $matchingPhrases[] = $line;
                                                    }
                                                }

                                                // Markiere die übereinstimmenden Phrasen in den Zeilen
                                                foreach ($matchingPhrases as $matchingPhrase) {
                                                    $line = str_replace($matchingPhrase, '<span style="background-color: yellow;">' . $matchingPhrase . '</span>', $line);
                                                }

                                                // Speichere die markierte Zeile
                                                $file1Lines[$lineIndex] = $line;
                                            }

                                            // Zeilen der zweiten Datei markieren
                                            foreach ($matchingLines as $matchingLine) {
                                                $file2Lines[$matchingLine['file2LineIndex']] = str_replace($file2Lines[$matchingLine['file2LineIndex']], '<span style="background-color: yellow;">' . $file2Lines[$matchingLine['file2LineIndex']] . '</span>', $file2Lines[$matchingLine['file2LineIndex']]);
                                            }

                                            // Konvertiere die Zeilen zurück in den Text
                                            $markedFile1Content = implode(PHP_EOL, $file1Lines);
                                            $markedFile2Content = implode(PHP_EOL, $file2Lines);

                                            echo '<!-- plagiat modal  -->
                                            <div class="modal fade" id="plagiat_' . $plagiat_id . '" tabindex="-1" aria-labelledby="pricingModalTitle" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-xl">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-transparent">
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body px-sm-5 mx-50 pb-5">
                                                            <div id="pricing-plan">
                                                                <!-- title text and switch button -->
                                                                <div class="text-center">
                                                                    <h1 id="pricingModalTitle">Plagiat Check für Abgabe: ' . $plagiat_name . '</h1>
                                                                    <p class="mb-3">
                                                                        Hinweis: Alle möglicherweise übereinstimmenden Stellen wurden farblich markiert.<br /> <span style="color: red;">Diese automatische Analyse ist keine Garantie für ein Plagiat. Bitte überprüfen Sie die Abgabe nochmals manuell.</span><br />
                                                                        Wahrscheinlichkeit: <span ';
                                            if ($plagiat_matchrate_calc <= 80) echo 'class="badge badge-light-primary"';
                                            if ($plagiat_matchrate_calc <= 90 && $plagiat_matchrate_calc > 80) echo 'class="badge badge-light-warning"';
                                            if ($plagiat_matchrate_calc <= 100 && $plagiat_matchrate_calc > 90) echo 'class="badge badge-light-danger"';
                                            echo '>' . $plagiat_matchrate . '</span>

                                                                    </p>
                                                                </div>
                                                                <!--/ title text and switch button -->

                                                                <div class="row pricing-card">
                                                                    <!-- file1 -->
                                                                    <div class="col-12 col-lg-6">
                                                                        <div class="card basic-pricing border text-center shadow-none">
                                                                            <div class="card-body">
                                                                                <h3>' . $submission_data['name'] . '</h3>
                                                                                <p class="card-text">Abgegeben von: ' . $db->getFullNameViaID($submission_data['owner']) . '</p>
                                                                                <br /><br />';
                                            echo '<div style="float: left; width: 100%; background-color: lightgrey;">' . PHP_EOL;
                                            echo '<pre style="text-align: left;">' . PHP_EOL;
                                            echo $markedFile1Content . PHP_EOL;
                                            echo '</pre>' . PHP_EOL;
                                            echo '</div>' . PHP_EOL;
                                            echo '</div>
                                                                        </div>
                                                                    </div>
                                                                    <!--/ file1 -->

                                                                    <!-- file2 -->
                                                                    <div class="col-12 col-lg-6">
                                                                        <div class="card enterprise-pricing border text-center shadow-none">
                                                                            <div class="card-body">
                                                                                <h3>' . $plagiat_name . '</h3>
                                                                                <p class="card-text">Abgegeben von: ' . $plagiat_owner_name . '</p>
                                                                                <br /><br />';
                                            echo '<div style="float: left; width: 100%; background-color: lightgrey;">' . PHP_EOL;
                                            echo '<pre style="text-align: left;">' . PHP_EOL;
                                            echo $markedFile2Content . PHP_EOL;
                                            echo '</pre>' . PHP_EOL;
                                            echo '</div>' . PHP_EOL;
                                            echo '<div style="clear: both;"></div>' . PHP_EOL;
                                            echo '</div>
                                                                        </div>
                                                                    </div>
                                                                    <!--/ file2 -->
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- / plagiat modal  -->';
                                        }
                                        
                                    }
                                    else {
                                        //Keine Plagiate gefunden ausgeben
                                        echo '<tr role="row" class="odd">
                                        <td>Keine großen Übereinstimmungen gefunden.</td>
                                        </tr>';
                                    }
                                    echo '</tbody>
                                            </table>';
                                }
                               ?>
                                </div>
                            </div>

                            
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

    <!-- BEGIN: Page JS-->
    <script src="app-assets/js/scripts/pages/modal-edit-user.js"></script>

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