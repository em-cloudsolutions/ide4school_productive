<?php if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); } ?>
<?php 
	if($db->isUserLoggedIn() == true) {
		if($db->LoginAllowed() == false) {
            header("Location: login_deactivated");
        }
        else {
            header("Location: dashboard");
        }
	} else {

        if (isset($_COOKIE['remember_me'])) {
            $remember_me = $_COOKIE['remember_me'];
            $result = $db->checkRememberMe($remember_me);
            if($result) {
                $_SESSION['user_id'] = $result;
                if($db->autoLoginRememberMe()) {
                    header("Location: dashboard");
                }
            }
            elseif($result == false) {
                setcookie("remember_me","",time() - 3600);
                unset($_COOKIE['remember_me']);
            }
        }

            $_SESSION['login_error'] = 0;
        
        $authentication_failed_error_msg = "Falscher Benutzername und / oder falsches Passwort! Bitte überprüfe deine Anmeldedaten und versuche es noch einmal!";
            
		if(isset($_POST['login'])) {
			$username = $_POST['benutzername'];
			$passwort = $_POST['passwort'];
            $browser = $_SERVER['HTTP_USER_AGENT'];

            if (strpos($browser, 'Chrome') !== false) {
                $browser_name = "Chrome";
            }
            elseif (strpos($browser, 'Firefox') !== false) {
                $browser_name = "Firefox";
            }
            elseif (strpos($browser, 'Safari') !== false) {
                $browser_name = "Safari";
            }
            elseif (strpos($browser, 'Opera') !== false) {
                $browser_name = "Opera";
            }
            elseif (strpos($browser, 'MSIE') !== false) {
                $browser_name = "Internet Explorer";
            }
            elseif (strpos($browser, 'Edge') !== false) {
                $browser_name = "Edge";
            }
            elseif (strpos($browser, 'Trident') !== false) {
                $browser_name = "Internet Explorer";
            }
            elseif (strpos($browser, 'Netscape') !== false) {
                $browser_name = "Netscape";
            }
            elseif (strpos($browser, 'Maxthon') !== false) {
                $browser_name = "Maxthon";
            }
            elseif (strpos($browser, 'Konqueror') !== false) {
                $browser_name = "Konqueror";
            }
            elseif (strpos($browser, 'UCBrowser') !== false) {
                $browser_name = "UCBrowser";
            }
            elseif (strpos($browser, 'Vivaldi') !== false) {
                $browser_name = "Vivaldi";
            }

            //Ermittle den Gerätenamen (Windows, Android, iPhone, iPad, etc.)
            if (strpos($browser, 'Windows') !== false) {
                $device = "Windows";
            }
            elseif (strpos($browser, 'Android') !== false) {
                $device = "Android";
            }
            elseif (strpos($browser, 'iPhone') !== false) {
                $device = "iPhone";
            }
            elseif (strpos($browser, 'iPad') !== false) {
                $device = "iPad";
            }
            elseif (strpos($browser, 'Macintosh') !== false) {
                $device = "Macintosh";
            }
            elseif (strpos($browser, 'Linux') !== false) {
                $device = "Linux";
            }
            elseif (strpos($browser, 'Ubuntu') !== false) {
                $device = "Ubuntu";
            }
            elseif (strpos($browser, 'Debian') !== false) {
                $device = "Debian";

            }
            elseif (strpos($browser, 'Fedora') !== false) {
                $device = "Fedora";
            }
            elseif (strpos($browser, 'FreeBSD') !== false) {
                $device = "FreeBSD";
            }
            elseif (strpos($browser, 'OpenBSD') !== false) {
                $device = "OpenBSD";
            }
            elseif (strpos($browser, 'NetBSD') !== false) {
                $device = "NetBSD";
            }
            elseif (strpos($browser, 'OpenSUSE') !== false) {
                $device = "OpenSUSE";
            }
            elseif (strpos($browser, 'Chrome OS') !== false) {
                $device = "Chrome OS";
            }
            elseif (strpos($browser, 'BlackBerry') !== false) {
                $device = "BlackBerry";
            }
            elseif (strpos($browser, 'Mobile') !== false) {
                $device = "Mobile";
            }
            elseif (strpos($browser, 'Windows Phone') !== false) {
                $device = "Windows Phone";
            }

            $browser = $browser_name." auf ".$device;

                // Wenn der Benutzer das "Remember Me" Kontrollkästchen ausgewählt hat, setze $remember_me auf true
                if (isset($_POST['rememberMe']) && $_POST['rememberMe'] == 'on') {
                    $remember_me = true;
                }
                else {
                    $remember_me = false;
                }

			if($db->login($username, $passwort, $remember_me, $browser, $device) == true) {
                if($_SESSION['login_error'] == 3) {
                    header("Location: login_deactivated");
                }
                else {
                    if(isset($_POST['game'])) {
                        $redirect_to_game_link = $_POST['game'];
                        header("Location: game&token=$redirect_to_game_link");
                    }
                    else {
                        $_SESSION['2fa_passed'] = false;
                        header("Location: dashboard");
                    }
                    
                }
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
    <title>Anmelden - ide4school</title>
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
    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <div class="auth-wrapper auth-cover">
                    <div class="auth-inner row m-0">
                        <!-- Brand logo--><a class="brand-logo" href="index.php">
                            <svg viewBox="0 0 139 95" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="28">
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
                        <!-- /Brand logo-->
                        <!-- Left Text-->
                        <div class="d-none d-lg-flex col-lg-8 align-items-center p-5">
                            <div class="w-100 d-lg-flex align-items-center justify-content-center px-5"><img class="img-fluid" src="app-assets/images/pages/login-v2.svg" alt="Login V2" /></div>
                        </div>
                        <!-- /Left Text-->
                        <!-- Login-->
                        <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
                            <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
                                <h2 class="card-title fw-bold mb-1">Willkommen bei ide4school! 👋</h2>
                                <p class="card-text mb-2">Bitte melde dich an, um dein Abenteuer zu beginnen.</p>
                                <form class="auth-login-form mt-2" action="index.php?page=login" method="POST">
                                    
                                    <div class="mb-1">
                                        <label class="form-label" for="login-email">Benutzername</label>
                                        <input class="form-control" id="login-email" type="text" name="benutzername" placeholder="Dein Benutzername" aria-describedby="login-email" tabindex="1" required/>
                                    </div>
                                    <div class="mb-1">
                                        <div class="d-flex justify-content-between">
                                            <label class="form-label" for="login-password">Passwort</label>
                                        </div>
                                        <div class="input-group input-group-merge form-password-toggle">
                                            <input class="form-control form-control-merge" id="login-password" type="password" name="passwort" placeholder="Dein Passwort" aria-describedby="login-password" tabindex="2" required/><span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                        </div>
                                        
                                        <?php
                                        echo '<br /><p style="color: red;">';
                                        if($_SESSION['login_error'] == 2) {
                                            echo $authentication_failed_error_msg;
                                        }
                                        elseif($_SESSION['login_error'] == 3) {
                                            echo $school_login_deactivated_error;
                                        }
                                        echo ' </p>';
                                         ?>
                                    </div>
                                    <div class="mb-1">
                                        <div class="form-check">
                                            <input class="form-check-input" name="rememberMe" id="rememberMe" type="checkbox" tabindex="3">
                                            <label class="form-check-label" for="rememberMe"> Angemeldet bleiben</label>
                                        </div>
                                    </div>
                                    <?php
                                    if(isset($_GET['agb_not_confirmed'])) {
            echo '<h4 style="color: red; text-align: center;">Da du unsere Nutzungsbedingungen und Datenschutzrichtlinien nicht akzeptiert hast, wurdest du nun abgemeldet. Du kannst dich jederzeit wieder anmelden, um die Richtlinien zu aktzeptieren. Dein Passwort wurde NICHT geändert!</h4>';
                   }?>
                                    <button class="btn btn-primary w-100" tabindex="4" type="submit" value="login" name="login">Anmelden</button>
                                    <?php if(isset($_GET['game'])) { echo '<input type="hidden" name="game" value="'.$_GET['game'].'">'; }?>'
                                </form>
                                </div>
                        </div>
                        <!-- /Login-->
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
    <script src="app-assets/js/scripts/pages/auth-login.js"></script>
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

<?php } ?>