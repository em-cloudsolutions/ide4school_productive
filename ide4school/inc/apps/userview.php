<?php
    if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }

    if(!$db->isUserLoggedIn()) {
        header("Location: not_authorized");
    }
    if(!isset($_SESSION['login_state']) && $db->getMFAMethodsFromUser($_SESSION['user_id']) != NULL) {
        header("Location: 2fa");
    }

    $user_id = $_GET['id'];
    $user_data = $db->getProfileData($user_id);

    $classes = $db->getAllClasses();
    $getCurrentUserData = $db->getCurrentUserInformations();

// SESSION CLASS MANAGMENT - BEGIN
$teacher_classes = $db->getAllowedClassesForTeachers();


$db->checkAssignmentRights();
if(isset($_POST['updateSessionClass'])) {
    $new_class = $_POST['class_name'];
    $_SESSION['currentSessionClass'] = $new_class;
    header("Location: user&id=$user_data");
}
// SESSION CLASS MANAGMENT - END


    // Update user

if(isset($_POST["updateUser"]))
{
  $log_user = $db->getLogUser();
  // Get Formular Data
  $firstName = $_POST['user-firstName'];
  $secondName = $_POST['user-secondName'];
  $class = $_POST['user-class'];
  $role = $_POST['user-role'];


  $username = $_POST['user-username'];
  $password1 = $_POST['user-password'];
  $password2 = $_POST['user-password2'];

  if($password1 != NULL && $password1 == $password2) {
        $unencrypted_password = $password1;
        $password = password_hash($unencrypted_password, PASSWORD_DEFAULT);
    }
    else {
        $password = $db->getCurrentUserPasswort($user_id);
    }


  //Change alpha-num
  $username = str_replace("ö", "o", $username);
  $username = str_replace("ä", "a", $username);
  $username = str_replace("ü", "u", $username);
  $username = str_replace("ß", "ss", $username);
  $username = str_replace("Ö", "O", $username);
  $username = str_replace("Ä", "A", $username);
  $username = str_replace("Ü", "U", $username);
  $username = str_replace("", "", $username);
  $username = str_replace("_", "", $username);
  $username = str_replace("-", "", $username);

  $spec_chars = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
  $username = strtr( $username, $spec_chars);

  


  // Insert in DB and create Folder
  if($db->updateUser($firstName, $secondName, $class, $role, $username, $password, $log_user, $user_id)) {
    header("Location: user&id=$user_id");
        }
    
    else {
        echo'
                    <div class="alert alert-danger" role="alert">
                                                        <h4 class="alert-heading">Datenbankfehler</h4>
                                                        <div class="alert-body">
                                                        Benutzer konnte nicht aktualisiert werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                                        </div>
                                                    </div>';
        sleep(3);
        header("Location: user&id=$user_id");
    }
};

//reinit user
if(isset($_POST["reinitUser"]))
{
    // Get Formular Data
    $firstName = $_POST['user-firstName'];
    $secondName = $_POST['user-secondName'];
    $class = $_POST['user-class'];
    $role = $_POST['user-role'];

    $username = $_POST['user-username'];
    $password1 = $_POST['user-password'];
    $password2 = $_POST['user-password2'];

    if($password1 != NULL && $password1 == $password2) {
        $unencrypted_password = $password1;
        $password = password_hash($unencrypted_password, PASSWORD_DEFAULT);
    }
    else {
        $password = $db->getCurrentUserPasswort($user_id);
    }

      //Change alpha-num
    $username = str_replace("ö", "o", $username);
    $username = str_replace("ä", "a", $username);
    $username = str_replace("ü", "u", $username);
    $username = str_replace("ß", "ss", $username);
    $username = str_replace("Ö", "O", $username);
    $username = str_replace("Ä", "A", $username);
    $username = str_replace("Ü", "U", $username);
    $username = str_replace("", "", $username);
    $username = str_replace("_", "", $username);
    $username = str_replace("-", "", $username);

    $spec_chars = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
    $username = strtr( $username, $spec_chars);

    

    if($db->reinitUser($id, $firstName, $secondName, $class, $role, $username, $password)) {
        header("Location: users");
            }
}

   



// Logout User

if(isset($_POST["remoteLogout"]))
{
    $id = $_POST['user_id'];
    if($db->LogoutUser($id)) {
        header("Location: user&id=$user_id");
            }
    else {
        echo'
                    <div class="alert alert-danger" role="alert">
                                                        <h4 class="alert-heading">Datenbankfehler</h4>
                                                        <div class="alert-body">
                                                        Fokus Modus konnte nicht abgemeldet werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                                        </div>
                                                    </div>';
        sleep(3);
        header("Location: user&id=$user_id");
    }
};

// Get user activitys
$user_fullname = $db->getFullNameViaID($user_data['id']);
$user_activitys = $db->getUserActivity($user_fullname);

// Get user levys
$projects = $db->getSubmittedProjectsFromUser($user_data['id']);
?>

<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="author" content="em CLOUDsolutions">
    <title><?=$user_data['firstName']?> <?=$user_data['secondName']?> - Benutzeransicht - <?=$user_data['institution']?> - ide4school</title>
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

    function remoteLogout(user_id){
        if(confirm("Benutzer wirklich abmelden?")) {
        document.getElementById("user_id").value = user_id;
        document.getElementById("remoteLogoutForm").submit();
        }
    }

      // Manage User delete

      function deleteUser(id){
        if(confirm("Benutzer wirklich löschen?")){
        document.getElementById("user_delete_id_input").value = id;
        document.getElementById("user_delete_form").submit();
        }
    }
</script>

<!-- User delete hidden form -->
        <form action="users" method="post" id="user_delete_form">
            <input name="id" type="text" hidden id="user_delete_id_input">
            <input name="delete" type="text" hidden id="delete">
        </form>

</script>
<!-- Session class update hidden form -->
<form action="user&id=<?=$user_id?>" method="POST" id="updateSessionClassForm">
            <input name="class_name" type="text" hidden id="class_name">
            <input name="updateSessionClass" type="text" hidden id="updateSessionClass">
        </form>

        <!-- user logout hidden form -->

        <form action="user&id=<?=$user_id?>" method="post" id="remoteLogoutForm">
            <input name="user_id" type="text" hidden id="user_id">
            <input name="remoteLogout" type="text" hidden id="remoteLogout">
        </form>


<script>
// Check PW
 function PWcheck(){
  if (document.getElementById('user-password').value ==
    document.getElementById('user-password2').value) {
    document.getElementById('pw-check-message').style.color = 'green';
    document.getElementById('pw-check-message').innerHTML = 'Passwort akzeptiert!';
    document.getElementById('updatebutton').disabled = false;
  } else {
    document.getElementById('pw-check-message').style.color = 'red';
    document.getElementById('pw-check-message').innerHTML = 'Passwörter stimmen nicht überein!';
    document.getElementById('updatebutton').disabled = true;
  }
}
</script>


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
                
                <li class=" nav-item active"><a class="d-flex align-items-center" href="users"><i data-feather="user"></i><span class="menu-title text-truncate" data-i18n="Users">Benutzer</span></a>
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
                                            <img class="img-fluid rounded mt-3 mb-2" src="app-assets/images/avatars/<?php echo($db->getUserAvatar($user_data['id']))?>.png" height="110" width="110" alt="User avatar" />
                                            <div class="user-info text-center">
                                                <h4><?=$user_data['secondName']?>, <?=$user_data['firstName']?></h4>
                                                <span class="badge bg-light-secondary">@<?=$user_data['username']?></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <h4 class="fw-bolder border-bottom pb-50 mb-1">Details</h4>
                                    <div class="info-container">
                                        <ul class="list-unstyled">
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Vorname:</span>
                                                <span><?=$user_data['firstName']?></span>
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Nachname:</span>
                                                <span><?=$user_data['secondName']?></span>
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Benutzername:</span>
                                                <span><?=$user_data['username']?></span>
                                            </li>


                                    
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Rolle:</span>
                                                <span><?=$user_data['role']?></span>
                                            </li>
                                 
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Klasse:</span>
                                                <span><?=$user_data['class']?></span>
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Institution:</span>
                                                <span><?=$user_data['institution']?></span>
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Status:</span>
                                                <?php if($user_data['status'] == '1'){
                                                        echo '<span class="badge bg-light-success">Online</span>';}
                                                        else {echo '<span class="badge bg-light-danger">Offline</span>';} ?>
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Zuletzt online:</span>
                                                <?php if($user_data['status'] == '1'){
                                                echo '<span class="badge bg-light-secondary">Gerade angemeldet</span>';}
                                                else {
                                                    if($user_data['last_logout'] == NULL) {
                                                            echo '<span class="badge bg-light-secondary">Noch nie eingeloggt</span>';
                                                        }
                                                        else {
                                                            echo '<span class="badge bg-light-secondary">' . $user_data['last_logout'] . '</span>';
                                                        }
                                                } 
                                                    ?>
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Erstellt am:</span>
                                                <span class="badge bg-light-secondary"><?=$user_data['created_at']?></span>
                                            </li>
                                        </ul>
                                        <div class="d-flex justify-content-center pt-2">
                                        <?php
                        if($db->isAdmin()) {
                           ?>
                                            <a href="javascript:;" class="btn btn-primary me-1" data-bs-target="#editUser" data-bs-toggle="modal">
                                                Bearbeiten
                                            </a>
                                            <?php
                        }
                        ?>
                                            <a href="javascript:;" class="btn btn-secondary me-1" OnClick="remoteLogout(<?=$user_id?>)">
                                                Abmelden
                                            </a>
                                            <?php
                        if($db->isAdmin()) {
                           ?>
                                            <a href="javascript:;" OnClick="deleteUser(<?=$user_id?>)" class="btn btn-outline-danger suspend-user">Löschen</a>
                        <?php
                                        }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /User Card -->
                            
                        </div>
                        <!--/ User Sidebar -->

                        <!-- User Content -->
                        <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
                            <!-- Levies table -->
                            <div class="card">
                                <h4 class="card-header">Noch nicht zurückgegebene Projekte dieses Benutzers:</h4>
                                <div class="table-responsive">
                                    <table class="table datatable-project">
                                        <thead>
                                            <tr>
                                                <th class="text-nowrap">Name:</th>
                                                <th>Abgegeben am:</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                            <?php
                                            if(!$projects) {
                                                echo '<td>Keine abgegebenen Projekte</td>';
                                            }
                                            else {
                                            foreach($projects as $project) {
                                                $project_name = json_decode(urldecode($project['project_content']), true)['name'];
                                                echo '<td><a href="project&id=' . $project['id'] . '">' . $project_name . '</a></td>';
                                                echo '<td>' . $project['submitted_at'] . '</td>';
                                            }
                                        }
                                            ?>
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /Levies table -->

                            <!-- Activity Timeline -->
                            <div class="card">
                                <h4 class="card-header">Benutzeraktivitäten</h4>
                                <div class="card-body pt-1">
                                    <ul class="timeline ms-50">
                                        <?php
                                        foreach($user_activitys as $user_activity) {
                                            echo '<li class="timeline-item">
                                                <span class="timeline-point timeline-point-indicator"></span>
                                                <div class="timeline-event">
                                                    <div class="d-flex justify-content-between flex-sm-row flex-column mb-sm-0 mb-1">
                                                        <h6>' . $user_activity['text'] . '</h6>
                                                        <span class="timeline-event-time me-1">' . $user_activity['date'] . '</span>
                                                    </div>
                                                    
                                                </div>
                                            </li>';
                                        }
                                        ?>
                                        
                                    </ul>
                                </div>
                            </div>
                            <!-- /Activity Timeline -->

                            
                        </div>
                        <!--/ User Content -->
                    </div>
                </section>
                <?php
                        if($db->isAdmin()) {
                           ?>
                <!-- Edit User Modal -->
                <div class="modal fade" id="editUser" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-edit-user">
                        <div class="modal-content">
                            <div class="modal-header bg-transparent">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body pb-5 px-sm-5 pt-50">
                                <div class="text-center mb-2">
                                    <h1 class="mb-1">Benutzerinformationen bearbeiten</h1>
                                    
                                </div>
                                <?php echo '<form action="user&id=' . $user_data['id'] . '" method="POST" id="editUserForm" class="row gy-1 pt-75">'; ?>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="modalEditUserFirstName">Vorname</label>
                                        <input type="text" id="modalEditUserFirstName" name="user-firstName" class="form-control" placeholder="Max" value="<?=$user_data['firstName']?>" data-msg="Vornamen des Benutzers eingeben" />
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="modalEditUserLastName">Nachname</label>
                                        <input type="text" id="modalEditUserLastName" name="user-secondName" class="form-control" placeholder="Mustermann" value="<?=$user_data['secondName']?>" data-msg="Nachnamen des Benutzers eingeben" />
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="modalEditUserEmail">Benutzername</label>
                                        <input type="text" id="user-username" name="user-username" class="form-control" placeholder="MMustermann" value="<?=$user_data['username']?>"/>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="modalEditUserStatus">Klasse</label>
                                        <select id="modalEditUserStatus" name="user-class" class="form-select" aria-label="Klasse des Benutzers angeben">
                                            <option selected><?=$user_data['class']?></option>
                                            <option >---</option>
                                            <?php
                                            foreach($classes as $class) {
                                            echo '<option value="'.$class['name'].'" class="text-capitalize">' . $class['name'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="modalEditUserName">Neues Passwort</label>
                                        <input type="password" id="user-password" name="user-password" class="form-control" placeholder="**********"  onkeyup="PWcheck()"/>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="modalEditUserEmail">Neues Passwort wiederholen</label>
                                        <input type="password" id="user-password2" name="user-password2" class="form-control" placeholder="**********" onkeyup="PWcheck()"/>
                                        <span name="pw-check-message" id="pw-check-message" style="float: right;"></span>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label" for="modalEditUserEmail">Institution</label>
                                        <input type="text" id="user-institution" name="user-institution" class="form-control" placeholder="Musterschule" value="<?=$user_data['institution']?>" readonly />
                                        <span name="pw-check-message" id="pw-check-message" style="float: right;"></span>
                                    </div>                                    
                                    <div class="col-12 col-md-4">
                          <label for="place" class="form-label">Nutzer Rolle</label>
                          <div class="form-check">
                            <input
                              name="user-role"
                              class="form-check-input"
                              type="radio"
                              value="Schüler"
                              id="defaultRadio1"
                              <?php if($user_data['role'] == 'Schüler'){
                              echo 'checked'; }
                              else {
                              }?>
                            />
                            <label class="form-check-label" for="defaultRadio1"> Schüler </label>
                          </div>
                          <div class="form-check">
                            <input
                              name="user-role"
                              class="form-check-input"
                              type="radio"
                              value="Lehrer"
                              id="defaultRadio2"
                            <?php if($user_data['role'] == 'Lehrer') { 
                              echo 'checked'; }
                            else {}?>
                            />
                            <label class="form-check-label" for="defaultRadio2"> Lehrer </label>
                          </div>
                          <div class="form-check">
                            <input
                              name="user-role"
                              class="form-check-input"
                              type="radio"
                              value="Administrator"
                              id="defaultRadio3"
                              <?php if($user_data['role'] == 'Administrator'){
                                echo 'checked';}
                                else {} ?>
                            />
                            <label class="form-check-label" for="defaultRadio3"> Administrator </label>
                          </div>
                        </div>
                                   
                                    <div class="col-12 text-center mt-2 pt-50">
                                        <button type="submit" name="updateUser" value="updateUser" id="updatebutton" class="btn btn-primary me-1">Ändern</button>
                                        <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal" aria-label="Schließen">
                                            Abbrechen
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ Edit User Modal -->
                                <?php
                        }
                        ?>
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