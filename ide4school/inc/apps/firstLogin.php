<?php
if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }
if(!$db->isUserLoggedIn()) {
    header("Location: not_authorized");
}
if(!isset($_SESSION['login_state']) && $db->getMFAMethodsFromUser($_SESSION['user_id']) != NULL) {
    header("Location: 2fa");
}


$getCurrentUserData = $db->getCurrentUserInformations();

if(isset($_POST["firstLogin"]))
{
  $password1 = $_POST['password1'];
  $password2 = $_POST['password2'];

  if($password1 != NULL && $password1 == $password2) {
        $unencrypted_password = $password1;
        $password = password_hash($unencrypted_password, PASSWORD_DEFAULT);

        // Insert in DB and create Folder

    if(isset($_POST["agb_confirmed"])) {
        if($db->firstStartConfirm($password)) {
            header("Location: dashboard");
                }
            else {
                echo'
            <div class="alert alert-danger" role="alert">
                                                <h4 class="alert-heading">Datenbankfehler</h4>
                                                <div class="alert-body">
                                                Passwort konnten nicht aktualisert werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                                </div>
                                            </div>';
                sleep(3);
                header("Location: dashboard");
            }
    }
    else {
        header("Location: logout&agb_not_confirmed");
    }
    }
    else {
        echo'
            <div class="alert alert-danger" role="alert">
                                                <h4 class="alert-heading">Fehler</h4>
                                                <div class="alert-body">
                                                Passwörter müssen übereinstimmen! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
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
    <title>Erster Login - <?=$getCurrentUserData['secondName']?> , <?=$getCurrentUserData['firstName']?> - ide4school</title>
    <link rel="apple-touch-icon" href="app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/vendors.min.css">
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
    <link rel="stylesheet" type="text/css" href="app-assets/css/pages/authentication.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <!-- END: Custom CSS-->

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="blank-page">

<script>
// Check PW
 function PWcheck(){
  if (document.getElementById('password1').value == document.getElementById('password2').value && document.getElementById('password1').value != "") {
    document.getElementById('pw-check-message').style.color = 'green';
    document.getElementById('pw-check-message').innerHTML = 'Passwort akzeptiert!';
    readyCheck();
  } else {
    document.getElementById('pw-check-message').style.color = 'red';
    document.getElementById('pw-check-message').innerHTML = 'Passwörter stimmen nicht überein!';
    readyCheck();
  }
}

function readyCheck() {
    if (document.getElementById('password1').value == document.getElementById('password2').value && document.getElementById('password1').value != "" && document.getElementById('agb_confirmed').checked) {
        document.getElementById('updateButton').disabled = false;
    }
    else {
        document.getElementById('updateButton').disabled = true;
    }
}
</script>

    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <div class="auth-wrapper auth-basic px-2">
                    <div class="auth-inner my-2">
                        <!-- Reset Password basic -->
                        <div class="card mb-0">
                            <div class="card-body">
                                <a href="dashboard" class="brand-logo">
                                    <svg viewbox="0 0 139 95" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="28">
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
                                                    <path class="text-primary" id="Path" d="M-5.68434189e-14,2.84217094e-14 L39.1816085,2.84217094e-14 L69.3453773,32.2519224 L101.428699,2.84217094e-14 L138.784583,2.84217094e-14 L138.784199,29.8015838 C137.958931,37.3510206 135.784352,42.5567762 132.260463,45.4188507 C128.736573,48.2809251 112.33867,64.5239941 83.0667527,94.1480575 L56.2750821,94.1480575 L6.71554594,44.4188507 C2.46876683,39.9813776 0.345377275,35.1089553 0.345377275,29.8015838 C0.345377275,24.4942122 0.230251516,14.560351 -5.68434189e-14,2.84217094e-14 Z" style="fill: currentColor"></path>
                                                    <path id="Path1" d="M69.3453773,32.2519224 L101.428699,1.42108547e-14 L138.784583,1.42108547e-14 L138.784199,29.8015838 C137.958931,37.3510206 135.784352,42.5567762 132.260463,45.4188507 C128.736573,48.2809251 112.33867,64.5239941 83.0667527,94.1480575 L56.2750821,94.1480575 L32.8435758,70.5039241 L69.3453773,32.2519224 Z" fill="url(#linearGradient-1)" opacity="0.2"></path>
                                                    <polygon id="Path-2" fill="#000000" opacity="0.049999997" points="69.3922914 32.4202615 32.8435758 70.5039241 54.0490008 16.1851325"></polygon>
                                                    <polygon id="Path-21" fill="#000000" opacity="0.099999994" points="69.3922914 32.4202615 32.8435758 70.5039241 58.3683556 20.7402338"></polygon>
                                                    <polygon id="Path-3" fill="url(#linearGradient-2)" opacity="0.099999994" points="101.428699 0 83.0667527 94.1480575 130.378721 47.0740288"></polygon>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                    <h2 class="brand-text text-primary ms-1">ide4school</h2>
                                </a>

                                <h4 class="card-title mb-1">Herzlich Willkommen, <?=$getCurrentUserData['firstName']?> 👋</h4>
                                <p class="card-text mb-2">Wir begrüßen dich herzlich bei ide4school! Doch bevor du unsere Plattform nutzen kannst, brauchen wir noch eine Bestätigung von dir.</p>

                                <form class="auth-reset-password-form mt-2" action="first-login" method="POST">
                                <div class="mb-1">
                                        <div class="form-check">
                                            <input class="form-check-input" id="agb_confirmed" type="checkbox" name="agb_confirmed" onclick="readyCheck()"/>
                                            <label class="form-check-label" for="agb_confirmed"> Hiermit bestätige ich, dass ich die <a href="https://ide4school.com/terms-of-use" target="_blank">Nutzungsbedingungen</a> und <a href="https://ide4school.com/privacy" target="_blank">Datenschutzrichtlinien</a> von ide4school vollständig gelesen habe und diese akzeptiere.</label>
                                        </div>
                                    </div><br />
                                    <p>Als nächstes musst du dein Passwort ändern, um sicherzustellen, dass sich niemand Fremdes mit deinem Startpasswort in deinem Account einloggen kann. </p> 
                                    <p class="card-text mb-2"><b>Ein sicheres Passwort beinhaltet:</b><br /> 8-12 Zeichen, Kleinbuchstaben, Großbuchstaben, Zahlen & Sonderzeichen</p>
                                    <div class="mb-1">
                                        <div class="d-flex justify-content-between">
                                            <label class="form-label" for="reset-password-new">Neues Passwort</label>
                                        </div>
                                        <div class="input-group input-group-merge form-password-toggle">
                                            <input type="password" class="form-control form-control-merge" id="password1" onkeyup="PWcheck()" name="password1" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="reset-password-new" tabindex="1" autofocus />
                                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                        </div>
                                    </div>
                                    <div class="mb-1">
                                        <div class="d-flex justify-content-between">
                                            <label class="form-label" for="reset-password-confirm">Passwort wiederholen</label>
                                        </div>
                                        <div class="input-group input-group-merge form-password-toggle">
                                            <input type="password" class="form-control form-control-merge" id="password2" onkeyup="PWcheck()" name="password2" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="reset-password-confirm" tabindex="2" />
                                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                        </div>
                                        <span name="pw-check-message" id="pw-check-message" style="float: right;"></span><br />
                                    </div>
                                
                                    <button type="submit" value="firstLogin" name="firstLogin" id="updateButton" class="btn btn-primary w-100" disabled>Das Abenteuer beginnen 🚀</button>
                                </form>

                                
                            </div>
                        </div>
                        <!-- /Reset Password basic -->
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- END: Content-->


    <!-- BEGIN: Vendor JS-->
    <script src="app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="app-assets/vendors/js/forms/validation/jquery.validate.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="app-assets/js/core/app-menu.js"></script>
    <script src="app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="app-assets/js/scripts/pages/auth-reset-password.js"></script>
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