<?php
if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }
if(!$db->isUserLoggedIn()) {
    header("Location: not_authorized");
}

// SESSION CLASS MANAGMENT - BEGIN
$teacher_classes = $db->getAllowedClassesForTeachers();


$db->checkAssignmentRights();
if(isset($_POST['updateSessionClass'])) {
    $new_class = $_POST['class_name'];
    $_SESSION['currentSessionClass'] = $new_class;
    header("Location: disk");
}
// SESSION CLASS MANAGMENT - END

$user_id = $_SESSION['user_id'];
$user_data = $db->getProfileData($user_id);
$classes = $db->getAllClasses();
$getCurrentUserData = $db->getCurrentUserInformations();

$selected_class = 'Alle Klassen / Gruppen';

if(isset($_GET["class"])) {
    $selected_class = $_GET["class"];
    $selected_class = urldecode($selected_class);
    $users = $db->getAllUsersFromClass($selected_class);
    $_SESSION["class_return_destination"] = "class=" . urlencode($_GET["class"]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $countfiles = count($_FILES['file']['name']);
    $uploadTempDir = "files/administration/upload-temp";
    if (!is_dir($uploadTempDir)) {
        mkdir($uploadTempDir, 0777, true);
    }

    for ($i = 0; $i < $countfiles; $i++) {
        $filename = $_FILES['file']['name'][$i];
        $location = $uploadTempDir . "/" . $filename;
        $extension = pathinfo($location, PATHINFO_EXTENSION);
        $extension = strtolower($extension);
        $response = 0;
        if ($_FILES['file']['size'][$i] < 110000000) {
            if (in_array($extension, ['md', 'zip', 'pdf', 'docx', 'doc', 'xlsx', 'xls', 'pptx', 'ppt', 'jpg', 'jpeg', 'png', 'gif', 'mp4', 'mp3', 'txt', 'html', 'css', 'js', 'php', 'sql', 'xml', 'json', 'csv', 'svg', 'ico', 'ttf', 'eot', 'woff', 'woff2', 'otf', 'rar', '7z', 'gz', 'tar', 'bz2', 'apk', 'exe', 'msi', 'dmg', 'iso', 'img', 'bin', 'psd', 'ai', 'eps', 'ps', 'tif', 'tiff', 'bmp', 'wav', 'ogg', 'flac', 'aac', 'wma', 'm4a', 'm4p', 'm4b', 'm4r', 'm4v', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm', 'swf', '3gp', 'mpg', 'mpeg', 'm2v', 'm4v', '3g2', 'svg', 'webp', 'heic', 'heif', 'ico', 'cur', 'ani', 'ps', 'eps', 'ai', 'psd', 'svg', 'pdf', 'eps', 'ps', 'ai', 'psd', 'svg', 'pdf', 'eps', 'ps', 'ai', 'psd', 'svg', 'pdf', 'eps', 'ps', 'ai', 'psd', 'svg', 'pdf', 'eps', 'ps', 'ai', 'psd', 'svg', 'pdf', 'eps', 'ps', 'ai', 'psd', 'svg', 'pdf', 'eps', 'ps', 'ai', 'psd', 'svg', 'pdf', 'eps', 'ps', 'ai', 'psd', 'svg', 'pdf'])) {
                if(!move_uploaded_file($_FILES['file']['tmp_name'][$i], $location)) {
                    $response = 1;
                    $error_list = $error_list . $filename . ", ";
                }
            }
            else {
                //Warnung bezüglich der Dateiendung
                echo "<script>alert('Die Datei der Datei ".$_FILES['file']['name']." ist nicht erlaubt!');</script>";
                break;
            }
        } else {    
            //Warnung bezüglich der Dateigröße
            echo "<script>alert('Die Datei ".$_FILES['file']['name']." ist größer als 100MB!');</script>";
            break;
        }

        //Wenn selected_class = Alle Klassen / Gruppen, dann sind students alle Schüler
        if(isset($_GET['class'])) {
            $selected_class = $_GET['class'];
            $selected_class = urldecode($selected_class);
        }
        else {
            $selected_class = 'Alle Klassen / Gruppen';
        }
        if ($selected_class == 'Alle Klassen / Gruppen') {
            $students = $db->getAllUsers();
        } else {
            $students = $db->getAllUsersFromClass($selected_class);
        }
        foreach ($students as $student) {
            $user_dir = $db->getUserDir($student['id']);
            $destination = $user_dir . "/" . $filename;
            copy($location, $destination);
        }
        unlink($location);
    }


    function rec_rmdir($unlink_path)
    {
        if (!is_dir($unlink_path)) {
            return -1;
        }
        $dir = @opendir($unlink_path);
        if (!$dir) {
            return -2;
        }
        while ($entry = @readdir($dir)) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            if (is_dir($unlink_path . '/' . $entry)) {
                $res = rec_rmdir($unlink_path . '/' . $entry);
                if ($res == -1) {
                    @closedir($dir);
                    return -2;
                } else if ($res == -2) {
                    @closedir($dir);
                    return -2;
                } else if ($res == -3) {
                    @closedir($dir);
                    return -3;
                } else if ($res != 0) {
                    @closedir($dir);
                    return -2;
                }
            } else if (is_file($unlink_path . '/' . $entry) || is_link($unlink_path . '/' . $entry)) {
                $res = @unlink($unlink_path . '/' . $entry);
                if (!$res) {
                    @closedir($dir);
                    return -2;
                }
            } else {
                @closedir($dir);
                return -3;
            }
        }
        @closedir($dir);
        $res = @rmdir($unlink_path);
        if (!$res) {
            return -2;
        }
        return 0;
    }

    $unlink_path = "files/administration/upload-temp/*";
    rec_rmdir($unlink_path);

    if($response == 0) {
        echo "<script>alert('Die Datei(en) wurden erfolgreich hochgeladen!');</script>";
    }
    else {
        echo "<script>alert('Die Datei(en) ".$error_list." konnten nicht hochgeladen werden!');</script>";
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
    <title>Dateifreigabe - <?=$db->getCurrentInstitution();?> - ide4school</title>
    <link rel="apple-touch-icon" href="app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/file-uploaders/dropzone.min.css">
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
    <link rel="stylesheet" type="text/css" href="app-assets/css/plugins/forms/form-file-uploader.css">
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

    function startUpload() {
        document.getElementById('uploadForm').submit();
    }
</script>
<!-- Session class update hidden form -->
        <form action="disk" method="POST" id="updateSessionClassForm">
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
                
                
                
                <li class=" nav-item open"><a class="d-flex align-items-center" href="#"><i data-feather="hard-drive"></i><span class="menu-title text-truncate" data-i18n="Files">Dateien</span></a>
                    <ul class="menu-content ">
                        <li><a class="d-flex align-items-center" <?php if($getCurrentUserData['role'] == "Schüler") { echo ' href="disk&drive=my"'; } else { echo 'href="disk&drive=ad-users"'; } ?>><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="My folder"><?php if($getCurrentUserData['role'] == "Schüler") { echo 'Mein Ordner'; } else { echo 'Benutzerordner'; }?></span></a>
                        </li>
                        <li><a class="d-flex align-items-center" <?php if($getCurrentUserData['role'] == "Schüler") { echo ' href="disk&drive=class"'; } else { echo 'href="disk&drive=ad-classes"'; } ?>><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Class folder">Klassenordner</span></a>
                        </li>
                        <?php
                        if($getCurrentUserData['role'] != "Schüler") {
                            ?>
                            <li><a class="d-flex align-items-center active" href="fileshare"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Dateifreigabe">Dateifreigabe</span></a>
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
            
            <div class="content-body">
                <!-- Dropzone section start -->
                <section id="dropzone-examples">
                    <!-- warnings and primary alerts starts -->
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-warning" role="alert">
                                <div class="alert-body">
                                    <strong>Warnung:</strong> Bitte keine Dateien über 100MB hochladen!
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- warnings and primary alerts ends -->

                   
                    <!-- multi file upload starts -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Dateien an Benutzer senden</h4>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        Mithilfe dieses Tools können Sie Dateien an Benutzer einer Klasse oder an alle Benutzer senden.
                                    </p>
                                    <div class="row">

        <div class="col-md-4 user_status">
            <label class="form-label" for="FilterTransaction">Senden an</label>
            <button
                        type="button"
                        class="form-select text-capitalize mb-md-0 mb-2xx"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                      >
                        <?php echo $selected_class ?>
                      </button> 
                      <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="fileshare">Alle Klassen / Gruppen</a></li>
                      <?php foreach($classes as $class) {
                        echo '<li><a class="dropdown-item" href="fileshare&class=' . urlencode($class['name']) . '">' . $class['name'] . '</a></li>';
                        }?>
                      </ul> 
        </div>

     </div><br /><br />

                                    <form action="#" id="uploadForm" method="post" enctype="multipart/form-data">
                                        <input class="form-control" type="file" id="formFile" name="file[]" multiple>
                                    </form><br />
                                    <div class="col-sm-12 col-lg-8 ps-xl-75 ps-0">
                        
                <button  class="dt-button add-new btn btn-primary" tabindex="0" aria-controls="DataTables_Table_0" type="button" OnClick="startUpload()"><span>Dateien senden</span></button> 

        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- multi file upload ends -->
                </section>
                <!-- Dropzone section end -->

            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">
        <p class="clearfix mb-0"><span class="float-md-start d-block d-md-inline-block mt-25">COPYRIGHT &copy; 2021<a class="ms-25" href="https://1.envato.market/pixinvent_portfolio" target="_blank">Pixinvent</a><span class="d-none d-sm-inline-block">, All rights Reserved</span></span><span class="float-md-end d-none d-md-block">Hand-crafted & Made with<i data-feather="heart"></i></span></p>
    </footer>
    <button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
    <!-- END: Footer-->


    <!-- BEGIN: Vendor JS-->
    <script src="app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="app-assets/vendors/js/file-uploaders/dropzone.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="app-assets/js/core/app-menu.js"></script>
    <script src="app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="app-assets/js/scripts/forms/form-file-uploader.js"></script>
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