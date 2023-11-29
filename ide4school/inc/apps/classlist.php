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

$classes = $db->getAllClasses();
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


// Create new Class

if(isset($_POST["createClass"]))
        {
          $log_user = $db->getLogUser();
          // Get Formular Data
          $name = $_POST['class-name'];
          $description = $_POST['class-description'];
          
          $predefined_dir_name = $name;



          //Change alpha-num
          $predefined_dir_name = str_replace("ö", "oe", $predefined_dir_name);
          $predefined_dir_name = str_replace("ä", "ae", $predefined_dir_name);
          $predefined_dir_name = str_replace("ü", "ue", $predefined_dir_name);
          $predefined_dir_name = str_replace("ß", "ss", $predefined_dir_name);
          $predefined_dir_name = str_replace("Ö", "Oe", $predefined_dir_name);
          $predefined_dir_name = str_replace("Ä", "Äe", $predefined_dir_name);
          $predefined_dir_name = str_replace("Ü", "Ue", $predefined_dir_name);
          $predefined_dir_name = str_replace("#", "_", $predefined_dir_name);
          $predefined_dir_name = str_replace(" ", "_", $predefined_dir_name);
          $predefined_dir_name = str_replace("-", "_", $predefined_dir_name);
          $predefined_dir_name = str_replace("?", "_", $predefined_dir_name);
          $predefined_dir_name = str_replace(".", "_", $predefined_dir_name);
          $predefined_dir_name = str_replace(",", "_", $predefined_dir_name);
          $predefined_dir_name = str_replace(":", "_", $predefined_dir_name);

          $spec_chars = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
          $predefined_dir_name = strtr( $predefined_dir_name, $spec_chars);

          // Create Class Dir
          $class_dir = "files/classes/" .  $predefined_dir_name;

          // Insert in DB and create Folder
          if($db->createClass($name, $description, $class_dir, $log_user)) {
                    header("Location: classes");
            }
                else {
                    $db->delete_class_emergency($name, $description, $class_dir);
                    echo'
        <div class="alert alert-danger" role="alert">
                                            <h4 class="alert-heading">Dateifehler</h4>
                                            <div class="alert-body">
                                            Klassenordner konnte nicht erstellt werden. Klasse wurde nicht angelegt. Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                            </div>
                                        </div>';
                    sleep(3);
                    header("Location: classes");
                }
                
    };

    // Set Focus Mode offline
        if(isset($_GET["action"])) {
            {
            $action = $_GET["action"];
            $class_id = $_GET["class_id"];

            if($action == "setGroupListenModeOffline") {
                if($db->setGroupListenModeToOffline($class_id)) {
                  
                  header("Location: classes");
                  }
                  else {
                    echo'
        <div class="alert alert-danger" role="alert">
                                            <h4 class="alert-heading">Datenbankfehler</h4>
                                            <div class="alert-body">
                                            Fokus Modus konnte nicht geändert werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                            </div>
                                        </div>';
                  }
              }

     // Set Focus Mode online
            if($action == "setGroupListenModeOnline") {
              if($db->setGroupListenModeToOnline($class_id)) {
               
                header("Location: classes");
                }
                else {
                    echo'
        <div class="alert alert-danger" role="alert">
                                            <h4 class="alert-heading">Datenbankfehler</h4>
                                            <div class="alert-body">
                                            Fokus Modus konnte nicht geändert werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                            </div>
                                        </div>';
                }
            }
        
            
            }
          }

    // Delete Class
    if(isset($_POST["delete"])) {
        
        $id = $_POST["id"];

        $class_dir_for_delete = $db->getClassDir($id);
        if($db->deleteClass($id)) {
            header("Location: classes"); 
              
           
                          
                    }
          
                  else {
                    echo'
                    <div class="alert alert-danger" role="alert">
                                                        <h4 class="alert-heading">Fehler</h4>
                                                        <div class="alert-body">
                                                        Klasse konnte nicht gelöscht werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                                        </div>
                                                    </div>';
                        sleep(3);
                       
                          header("Location: classes");
                  }
    
                
        
      }

        // Rename Class
        if(isset($_POST['renameClass'])) {
            $db->renameClass($_POST['class_rename_id_input'], $_POST['class_rename_name_input']);
            header("Location: classes");
        }

        if(isset($_POST['editClassDescription'])) {
            $db->editClassDescription($_POST['class_desc_edit_id_input'], $_POST['class_desc_edit_name_input']);
            header("Location: classes");
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
    <title>Klassen - <?php echo $db->getCurrentInstitution() ?> - ide4school</title>
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
</script>
<!-- Session class update hidden form -->
        <form action="classlist" method="POST" id="updateSessionClassForm">
            <input name="class_name" type="text" hidden id="class_name">
            <input name="updateSessionClass" type="text" hidden id="updateSessionClass">
        </form>


        <!-- //Manage Focus Modus -->
        <script>
  function setListenModeOffline(class_id){
    window.location.href="classes&action=setGroupListenModeOffline&class_id=" + class_id;
  }

  function setListenModeOnline(class_id){
    window.location.href="classes&action=setGroupListenModeOnline&class_id=" + class_id;
  }

  // Manage class delete

    function deleteClass(id){
        document.getElementById("class_delete_id_input").value = id;
        document.getElementById("class_delete_form").submit();
    }

    function renameClass(id){
        if(confirm("Klasse umbenennen?")){
            var new_name = prompt("Neuer Name:");
            if(new_name != null){
                if(new_name != ""){
                    document.getElementById("class_rename_id_input").value = id;
                    document.getElementById("class_rename_name_input").value = new_name;
                    document.getElementById("class_rename_form").submit();
                }
                else {
                    alert("Bitte geben Sie einen Namen ein!");
                }
            }
        }
    }

    function editClassDescription(id){
        if(confirm("Klassenbeschreibung ändern?")){
            var new_desc = prompt("Neue Beschreibung:");
            if(new_desc != null){
                if(new_desc != ""){
                    document.getElementById("class_desc_edit_id_input").value = id;
                    document.getElementById("class_desc_edit_name_input").value = new_desc;
                    document.getElementById("class_desc_edit_form").submit();
                }
                else {
                    alert("Bitte geben Sie einen Namen ein!");
                }
            }
        }
    }
</script>

<!-- Class delete hidden form -->
        <form action="classes" method="post" id="class_delete_form">
            <input name="id" type="text" hidden id="class_delete_id_input">
            <input name="delete" type="text" hidden id="delete">
        </form>

        <!-- Class rename hidden form -->
        <form action="classes" method="post" id="class_rename_form">
            <input name="class_rename_id_input" type="hidden" hidden id="class_rename_id_input">
            <input name="class_rename_name_input" type="hidden" hidden id="class_rename_name_input">
            <input name="renameClass" type="hidden" hidden id="renameClass">
        </form>

        <!-- Class description edit hidden form -->
        <form action="classes" method="post" id="class_desc_edit_form">
            <input name="class_desc_edit_id_input" type="hidden" hidden id="class_desc_edit_id_input">
            <input name="class_desc_edit_name_input" type="hidden" hidden id="class_desc_edit_name_input">
            <input name="editClassDescription" type="hidden" hidden id="editClassDescription">

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
                <li class=" nav-item active"><a class="d-flex align-items-center" href="classes"><i data-feather="users"></i><span class="menu-title text-truncate" data-i18n="Classes">Klassen</span></a>
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
    <h4 class="card-title">Klassen - <?=$currentInstitution?></h4>
    <div class="row">

        <div class="col-md-4 user_status">
        <label class="form-label" for="FilterTransaction">Angezeigt werden alle registrierten Klassen deiner Institution.</label>
            
        </div>
        <div class="col-sm-12 col-lg-8 ps-xl-75 ps-0">

                <button style="float: right;" class="dt-button add-new btn btn-primary" tabindex="0" aria-controls="DataTables_Table_0" type="button" data-bs-toggle="modal" data-bs-target="#modals-slide-in"><span>Klasse hinzufügen</span></button> 

        </div>
     </div>
</div>
                       
<br />
                            <table class="user-list-table table">
                                <thead class="table-light">
                                    <tr>
                                      
                                        <th>Name</th>
                                        <th>Beschreibung</th>
                                        <th>Fokus Modus</th>
                                        <th>Aktionen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($classes as $class) {
                                    echo '
                                <tr class="odd">
    <td class=" control" tabindex="0" style="display: none;"></td>
    <td class="sorting_1">
        <div class="d-flex justify-content-left align-items-center">
            <div class="avatar-wrapper">
                <div class="avatar  me-1"><img src="app-assets/images/avatars/9.png" alt="Avatar" height="32" width="32"></div>
            </div>
            <div class="d-flex flex-column"><a href="users&class=' . $class['name'] . '" class="user_name text-truncate text-body"><span class="fw-bolder">' . $class['name'] . '</span></a></div>
        </div>
    </td>';

    if($class['description'] != NULL) {
        echo '<td>' . $class['description'] . '</td>';
        }
        else {
        echo '<td>Keine Beschreibung vorhanden.</td>';
        }
    
    if($class['focus_mode'] == "1") {
    echo '<td><span class="badge rounded-pill badge-light-warning" text-capitalized="">Aktiv</span></td>';
    }
    else {
    echo '<td><span class="badge rounded-pill badge-light-danger" text-capitalized="">Inaktiv</span></td>';
    }

    echo '
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
                if($class['focus_mode'] == "1") {
                    echo '<a onclick=setListenModeOffline(' . $class['id'] . ') class="dropdown-item delete-record">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
             </svg>
             Fokus Modus deaktivieren
             </a>
                ';
                    }
                    else {
                        echo '<a onclick=setListenModeOnline(' . $class['id'] . ') class="dropdown-item delete-record">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                         </svg>
                         Fokus Modus aktivieren
                         </a>
                            ';
                    }

            echo '
            <a onclick=renameClass(' . $class['id'] . ') class="dropdown-item delete-record">
            <i data-feather="edit"></i>
                Klasse umbenennen
            </a>
            <a onclick=editClassDescription(' . $class['id'] . ') class="dropdown-item delete-record">
            <i data-feather="edit"></i>
                Klassenbeschreibung ändern
            </a>
            <a onclick=deleteClass(' . $class['id'] . ') class="dropdown-item delete-record">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
                Klasse löschen
            </a>
        </div>
    </div>
</td>
</tr>';
} ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Modal to add new user starts-->
                        <div class="modal modal-slide-in new-user-modal fade" id="modals-slide-in">
                            <div class="modal-dialog">
                                <form class="add-new-user modal-content pt-0" action="classes" method="POST">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                                    <div class="modal-header mb-1">
                                        <h5 class="modal-title" id="exampleModalLabel">Klasse hinzufügen</h5>
                                    </div>
                                    <div class="modal-body flex-grow-1">
                                        <div class="mb-1">
                                            <label class="form-label" for="basic-icon-default-fullname">Name</label>
                                            <input type="text" class="form-control dt-full-name" id="basic-icon-default-fullname" placeholder="Klasse 05-1" name="class-name" />
                                        </div>
                                        <div class="mb-1">
                                            <label class="form-label" for="basic-icon-default-fullname">Beschreibung</label>
                                            <input type="text" class="form-control dt-full-name" id="basic-icon-default-fullname" placeholder="Das ist die Klasse 05-1 der Musterschule in Musterstadt." name="class-description" />
                                        </div>
                                        
                                       
                                        <br />
                                        
                                        <button type="submit" class="btn btn-primary me-1 data-submit" name="createClass">Klasse hinzufügen</button>
                                        <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">Abbrechen</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Modal to add new user Ends-->
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