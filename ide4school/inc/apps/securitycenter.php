<?php
if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }
if(!$db->isUserLoggedIn()) {
    header("Location: not_authorized");
}
if(!isset($_SESSION['login_state']) && $db->getMFAMethodsFromUser($_SESSION['user_id']) != NULL) {
    header("Location: 2fa");
}

$getCurrentUserData = $db->getCurrentUserInformations();
$mfa_methods = $db->getMFAMethodsFromUser($_SESSION['user_id']);
$login_activitys = $db->getLoginActivity($_SESSION['user_id']);

// PASSWORT ÄNDERN
if(isset($_POST["updatePassword"]))
{
  $log_user = $db->getLogUser();
  // Get Formular Data
  $password1 = $_POST['password1'];
  $password2 = $_POST['password2'];
  $current_password = $_POST['current_password'];

  $current_saved_passwort = $db->getCurrentUserPasswort($_SESSION['user_id']);
  if(password_verify($current_password, $current_saved_passwort)) {
    if($password1 != NULL && $password1 == $password2) {
            $unencrypted_password = $password1;
            $password = password_hash($unencrypted_password, PASSWORD_DEFAULT);

            // Insert in DB and create Folder
        if($db->updatePasswort($password)) {
            $token = "*";
            $db->deleteUserToken($_SESSION['user_id'], $token);
            header("Location: logout");
                }
            
            else {
                echo'
            <div class="alert alert-danger" role="alert">
                                                <h4 class="alert-heading">Datenbankfehler</h4>
                                                <div class="alert-body">
                                                Passwort konnte nicht aktualisiert werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                                </div>
                                            </div>';
                sleep(3);
                header("Location: securitycenter");
            }

        }
        else {
            echo'
            <div class="alert alert-danger" role="alert">
                                                <h4 class="alert-heading">Fehler</h4>
                                                <div class="alert-body">
                                                Passwörter müssen übereinstimmen!
                                                </div>
                                            </div>';
        }
    }
    else {
        echo'
            <div class="alert alert-danger" role="alert">
                                                <h4 class="alert-heading">Fehler</h4>
                                                <div class="alert-body">
                                                Aktuelles Passwort nicht korrekt!
                                                </div>
                                            </div>';
    }
}
// ENDE PASSWORT ÄNDERN

//CODE AUTHENTIFIZIERUNG
//use OTPHP\TOTP;
//require_once 'src/2fa/otphp/vendor/autoload.php';

//$otp = TOTP::generate();
//$otp_code_secret = $otp->getSecret();
//echo "The OTP secret is: {$otp_code_secret}\n";


//$otp = TOTP::createFromSecret($otp_code_secret);
//echo "The current OTP is: {$otp->now()}\n";


// Note: You must set label before generating the QR code
//$otp->setLabel('ide4school Anmeldung - '.$getCurrentUserData['secondName'].', '.$getCurrentUserData['firstName']);
//$grCodeUri = $otp->getQrCodeUri(
//    'https://api.qrserver.com/v1/create-qr-code/?data=[DATA]&size=300x300&ecc=M',
//    '[DATA]'
//);



if(isset($_POST['deleteMFAMethod'])) {
    $mfa_method_id = $_POST['mfa_method_id'];
    if($db->removeMFA($mfa_method_id)) {
        header("Location: securitycenter");
    }
    else {
        echo'
        <div class="alert alert-danger" role="alert">
                                            <h4 class="alert-heading">Fehler</h4>
                                            <div class="alert-body">
                                            2FA konnte nicht entfernt werden. Datenbankfehler!
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
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>Sicherheitscenter - Dashboard - <?=$getCurrentUserData['secondName']?>, <?=$getCurrentUserData['firstName']?> - <?=$getCurrentUserData['institution']?> - ide4school</title>
    <link rel="apple-touch-icon" href="app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/forms/select/select2.min.css">
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

<?php include 'inc/components/header.php'; ?>

<script>
// Check PW
 function PWcheck(){
  if (document.getElementById('password1').value ==
    document.getElementById('password2').value) {
    document.getElementById('pw-check-message').style.color = 'green';
    document.getElementById('pw-check-message').innerHTML = 'Passwort akzeptiert!';
    document.getElementById('updateButton').disabled = false;
  } else {
    document.getElementById('pw-check-message').style.color = 'red';
    document.getElementById('pw-check-message').innerHTML = 'Passwörter stimmen nicht überein!';
    document.getElementById('updateButton').disabled = true;
  }
}

function deleteMFAMethod(id) {
    if(confirm("Willst du diese 2FA Methode wirklich löschen?")) {
        document.getElementById("mfa_method_id").value = id;
        document.getElementById("deleteMFAForm").submit();
    }
}
</script>

<form action="securitycenter" method="post" id="deleteMFAForm">
            <input name="mfa_method_id" type="text" hidden id="mfa_method_id">
            <input name="deleteMFAMethod" type="text" hidden id="deleteMFAMethod">
        </form>

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
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header float-start mb-0">Sicherheitscenter -  <?=$getCurrentUserData['secondName']?>, <?=$getCurrentUserData['firstName']?></h2>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="content-body">
                <div class="row">
                    <div class="col-12">

                        <!-- security -->

                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Passwort ändern</h4>
                            </div>
                            <div class="card-body pt-1">
                                <!-- form -->
                                <form class="validate-form" action="securitycenter" method="POST">
                                    <div class="row">
                                        <div class="col-12 col-sm-6 mb-1">
                                            <label class="form-label" for="account-old-password">Aktuelles Passwort</label>
                                            <div class="input-group form-password-toggle input-group-merge">
                                                <input type="password" class="form-control" id="account-old-password" name="current_password" placeholder="Aktuelles Passwort eingeben" data-msg="Please current password" />
                                                <div class="input-group-text cursor-pointer">
                                                    <i data-feather="eye"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-sm-6 mb-1">
                                            <label class="form-label" for="account-new-password">Neues Passwort</label>
                                            <div class="input-group form-password-toggle input-group-merge">
                                                <input type="password" id="password1" onkeyup="PWcheck()" name="password1" class="form-control" placeholder="Neues Passwort eingeben" />
                                                <div class="input-group-text cursor-pointer">
                                                    <i data-feather="eye"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 mb-1">
                                            <label class="form-label" for="account-retype-new-password">Neues Passwort wiederholen</label>
                                            <div class="input-group form-password-toggle input-group-merge">
                                                <input type="password" class="form-control" id="password2" onkeyup="PWcheck()" name="password2" placeholder="Neues Passwort wiederholen" />
                                                <div class="input-group-text cursor-pointer"><i data-feather="eye"></i></div>
                                            </div>
                                            <span name="pw-check-message" id="pw-check-message" style="float: right;"></span>
                                        </div>
                                        
                                        <div class="col-12">
                                            <p class="fw-bolder">Empfehlung für sichere Passwörter:</p>
                                            <ul class="ps-1 ms-25">
                                                <li class="mb-50">Mindestens 8 Zeichen - je mehr, desto besser</li>
                                                <li class="mb-50">Groß- und Kleinbuchstaben verwenden</li>
                                                <li>Zahlen und Sonderzeichen verwenden</li>
                                            </ul>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" name="updatePassword" id="updateButton" class="btn btn-primary me-1 mt-1">Passwort ändern</button>
                                            <button type="reset" class="btn btn-outline-secondary mt-1">Formular leeren</button>
                                        </div>
                                    </div>
                                </form>
                                <!--/ form -->
                            </div>
                        </div>

                        <!-- two-step verification -->
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Zwei-Faktor Authentifizierung (2FA)</h4>
                            </div>
                            <div class="card-body my-2 py-25">
                                <p class="fw-bolder">Die Zwei-Faktor-Authentifizierung ist <?php
                                    if($mfa_methods != NULL) {
                                        echo 'aktiviert.';
                                    }
                                    else {
                                        echo 'noch nicht aktiviert.';
                                    }
                                    ?></p>
                                <p>
                                Die Zwei-Faktor-Authentifizierung fügt deinem Account eine zusätzliche Sicherheitsebene hinzu, indem du <br />
                                    bei der Anmeldung neben deinem Passwort, einen weiteren Code eingeben musst, welcher aller 30 Sekunden neu <br />
                                    generiert wird. Diesen kannst du dann beispielsweise von deinem Handy ablesen.
                                </p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#twoFactorAuthAppsModal">
                                <?php
                                    if($mfa_methods != NULL) {
                                        echo 'Weitere Authenticator App hinzufügen';
                                    }
                                    else {
                                        echo 'Zwei-Faktor Authentifizierung aktivieren';
                                    }
                                    ?>
                                </button>
                            </div>
                        </div>
                        <!-- / two-step verification -->

                        
                        
                        <!-- api key list -->
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Verwendete Zwei-Faktor Methoden</h4>
                            </div>
                            <div class="card-body">
                               

                                <div class="row gy-2">
                                    <?php
                                    if($mfa_methods == NULL) {
                                        echo '<div class="col-12">
                                        <span class="badge badge-light-warning mb-1">KEINE 2FA METHODEN EINGERICHTET.</div>
                                            </div>';
                                    }
                                    else {
                                        foreach($mfa_methods as $mfa_method) {
                                            echo'
                                            <div class="col-12">
                                        <div class="bg-light-secondary position-relative rounded p-2">
                                            <div class="dropdown dropstart btn-pinned">
                                                <button class="btn btn-icon rounded-circle hide-arrow dropdown-toggle p-0" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i data-feather="more-vertical" class="font-medium-4"></i>
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                   
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center" href="#" OnClick=deleteMFAMethod("'.$mfa_method['id'].'")>
                                                            <i data-feather="trash-2" class="me-50"></i><span>Löschen</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="d-flex align-items-center flex-wrap">
                                                <h4 class="mb-1 me-1">'; if($mfa_method['type'] == "1") echo 'Authenticator App'; elseif($mfa_method['type'] == "2") echo 'E-Mail'; elseif($mfa_method['type'] == "3") echo 'FIDO2 / WebAuthn Passkey';
                                                echo '</h4>
                                                '; if($mfa_method['type'] == "1") echo '<span class="badge badge-light-primary mb-1">Zwei-Faktor Authentifizierung</span>'; elseif($mfa_method['type'] == "2") echo '<span class="badge badge-light-primary mb-1">Zwei-Faktor Authentifizierung</span>'; elseif($mfa_method['type'] == "3") echo '<span class="badge badge-light-warning mb-1">Passkey</span>';
                                                echo '
                                            </div>
                                            <span>Erstellt: '.$mfa_method['creation_time'].'</span>
                                        </div>
                                    </div>
                                            ';
                                        }
                                    }
                                    ?>
                                    
                                    
                                </div>
                            </div>
                        </div>
                        <!-- / api key list -->

                        <!-- recent device -->
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Anmeldeaktivitäten</h4>
                            </div>
                            <div class="card-body my-2 py-25">
                                <div class="table-responsive">
                                    <table class="table table-bordered text-nowrap text-center">
                                        <thead>
                                            <tr>
                                                <th class="text-start">BROWSER</th>
                                                <th>GERÄT</th>
                                                <th>LETZTER ANMELDEVERSUCH</th>
                                                <th>STATUS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if($login_activitys == NULL) {
                                                echo '<tr>
                                                <td colspan="4">Keine Anmeldeaktivitäten vorhanden.</td>
                                            </tr>';
                                            }
                                            else {
                                            foreach($login_activitys as $login_activity) {
                                                echo'
                                            <tr>
                                                <td class="text-start">
                                                    <div class="avatar me-25">
                                                        <img src="app-assets/images/icons/';
                                                        if(strpos($login_activity['browser'], 'Chrome') !== false) echo 'google-chrome.png';
                                                        elseif(strpos($login_activity['browser'], 'Firefox') !== false) echo 'mozila-firefox.png';
                                                        elseif(strpos($login_activity['browser'], 'Safari') !== false) echo 'apple-safari.png';
                                                        elseif(strpos($login_activity['browser'], 'Opera') !== false) echo 'opera.png';
                                                        elseif(strpos($login_activity['browser'], 'Internet Explorer') !== false) echo 'internet-explorer.png';
                                                        else {echo 'internet.png';}
                                                        echo '" alt="browser_icon" width="20" height="20" />
                                                    </div>
                                                    <span class="fw-bolder">'.$login_activity['browser'].'</span>
                                                </td>
                                                <td>'.$login_activity['device'].'</td>
                                                <td>'.$login_activity['login_time'].'</td>
                                                <td>';
                                                if($login_activity['success'] == "1") echo '<span class="badge badge-light-success mb-1">Erfolgreich</span>'; 
                                                elseif ($login_activity['success'] == "0") echo '<span class="badge badge-light-danger mb-1">Fehlgeschlagen</span>';
                                                echo '</td>
                                            </tr>';
                                            }
                                        }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- / recent device -->

                        <!--/ security -->
                    </div>
                </div>
             

                <!-- add authentication apps modal -->
                <div class="modal fade" id="twoFactorAuthAppsModal" tabindex="-1" aria-labelledby="twoFactorAuthAppsTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg two-factor-auth-apps">
                        <div class="modal-content">
                            <div class="modal-header bg-transparent">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body pb-5 px-sm-5 mx-50">
                                <h1 class="text-center mb-2 pb-50" id="twoFactorAuthAppsTitle">Authenticator App hinzufügen</h1>

                                <h4>Authenticator Apps</h4>
                                <p>
                                Scanne mit einer Authentifizierungs-App wie Google Authenticator, Microsoft Authenticator, Authy oder 1Password den
                                QR-Code. Anschließend wird ein 6-stelliger Code generiert, den Sie unten eingeben müssen. Dieser wird alle 30 Sekunden neu generiert und in der 
                                Authenticator <br />App angezeigt.
                                </p>

                                <div class="d-flex justify-content-center my-2 py-50">
                                <?php echo '<img src='.$grCodeUri.' width="122" alt="QR Code">'; ?>
                                </div>
                                <div class="alert alert-warning" role="alert">
                                    <h4 style="word-wrap: break-word;" class="alert-heading"><?=$otp_code_secret?></h4>
                                    <div class="alert-body fw-normal">
                                    Wenn du Probleme bei der Verwendung des QR-Codes hast, wähle  die manuelle Eingabe in deiner App und gebe den Schlüssel ein.
                                    </div>
                                </div>

                                <form class="row gy-1" action="create2fa" method="POST">
                                    <div class="col-12">
                                        <input type="hidden" name="otp_secret" value="<?=$otp_code_secret?>" />
                                        <input class="form-control" id="authenticationCode" name="otp_code" type="text" placeholder="6-stelligen Code hier eingeben" />
                                    </div>
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="reset" class="btn btn-outline-secondary mt-2 me-1" data-bs-dismiss="modal" aria-label="Close">
                                            Abbrechen
                                        </button>
                                        <button type="submit" name="createOTP" id="createOTP" class="btn btn-primary mt-2">
                                            <span class="me-50">Bestätigen</span>
                                            <i data-feather="chevron-right"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- / add authentication apps modal-->

               

            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <?php include 'inc/components/footer.php'; ?>


    <!-- BEGIN: Vendor JS-->
    <script src="app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="app-assets/vendors/js/forms/select/select2.full.min.js"></script>
    <script src="app-assets/vendors/js/forms/validation/jquery.validate.min.js"></script>
    <script src="app-assets/vendors/js/forms/cleave/cleave.min.js"></script>
    <script src="app-assets/vendors/js/forms/cleave/addons/cleave-phone.us.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="app-assets/js/core/app-menu.js"></script>
    <script src="app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="app-assets/js/scripts/pages/modal-two-factor-auth.js"></script>

    <!-- END: Page JS-->

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