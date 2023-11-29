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
        header("Location: focus_lobby&return_to=project&id=".$_GET['id']);
    }
}

    $classes = $db->getAllClasses();
    $getCurrentUserData = $db->getCurrentUserInformations();
    $_SESSION['user_username'] = $getCurrentUserData['username'];
    $_SESSION['user_role'] = $getCurrentUserData['role'];
// SESSION CLASS MANAGMENT - BEGIN
$teacher_classes = $db->getAllowedClassesForTeachers();


$db->checkAssignmentRights();
if(isset($_POST['updateSessionClass'])) {
    $new_class = $_POST['class_name'];
    $_SESSION['currentSessionClass'] = $new_class;
    header("Location: submission&id=$submission_id");
}
// SESSION CLASS MANAGMENT - END

if(isset($_GET['token'])) {
    $exam_token = $_GET['token'];
}
else {
    header("Location: exams");
}

$exam_general_information = $db->getExamDataByToken($exam_token);

if($exam_general_information == NULL) {
    header("Location: exams");
}
$exam_data = $db->getExamContent($exam_general_information['exam_content_id']);

//Extrahiere JSON in PHP
$exam_data = json_decode($exam_data['json_content'], true);


if(isset($_POST['startExam'])) {
    $exam_id = $_POST['exam_id'];
    $exam_token = $_POST['exam_token'];
    if($db->startExam($exam_id)) {
        header("Location: exam&token=$exam_token");
    }
    else {
        echo "Fehler beim Starten der Prüfung!";
    }
}

if(isset($_POST['endExam'])) {
    $exam_id = $_POST['exam_id'];
    $exam_token = $_POST['exam_token'];
    if($db->endExam($exam_id)) {
        header("Location: exam&token=$exam_token");
    }
    else {
        echo "Fehler beim Beenden der Prüfung!";
    }
}

if(!isset($_SESSION['current_exam_task'])) {
    $_SESSION['current_exam_task'] = 1;
}


$tasks = $exam_data['tasks'];
$current_task_data = $exam_data['tasks'][$_SESSION['current_exam_task']-1];

if($getCurrentUserData['role'] == "Schüler") {
    
    $exam_name = $exam_general_information['title'];
    //alles klein, ohne umlaute und leerzeichen
    $exam_name = strtolower($exam_name);
    $exam_name = str_replace(" ", "-", $exam_name);
    $exam_name = str_replace("ä", "ae", $exam_name);
    $exam_name = str_replace("ö", "oe", $exam_name);
    $exam_name = str_replace("ü", "ue", $exam_name);
    $exam_name = str_replace("ß", "ss", $exam_name);
    $exam_name = str_replace(":", "", $exam_name);
    $exam_name = str_replace(";", "", $exam_name);
    $exam_name = str_replace("!", "", $exam_name);
    $exam_name = str_replace("?", "", $exam_name);
    $exam_name = str_replace("(", "", $exam_name);
    $exam_name = str_replace(")", "", $exam_name);
    $exam_name = str_replace("[", "", $exam_name);
    $exam_name = str_replace("]", "", $exam_name);
    $exam_name = str_replace("{", "", $exam_name);
    $exam_name = str_replace("}", "", $exam_name);
    $exam_name = str_replace("=", "", $exam_name);
    $exam_name = str_replace("+", "", $exam_name);
    $exam_name = str_replace("*", "", $exam_name);
    $exam_name = str_replace("/", "", $exam_name);
    $exam_name = str_replace("\\", "", $exam_name);
    $exam_name = str_replace("|", "", $exam_name);
    $exam_name = str_replace("<", "", $exam_name);
    $exam_name = str_replace(">", "", $exam_name);

    $username = $getCurrentUserData['username'];

    //Wenn Ordner nicht vorhanden, erstelle ihn
    if(!file_exists("files/exams/".$exam_name."/".$username)) {
        mkdir("files/exams/".$exam_name."/".$username, 0777, true);
    }


    //Falls datei nicht vorhanden, erstelle sie
    if(!file_exists("files/exams/".$exam_name."/".$username."/praxis.py")) {
        $praxis_file = fopen("files/exams/".$exam_name."/".$username."/praxis.py", "w");
        fclose($praxis_file);
    }

    if(!file_exists("files/exams/".$exam_name."/".$username."/theorie.txt")) {
        $theorie_file = fopen("files/exams/".$exam_name."/".$username."/theorie.txt", "w");
        fclose($theorie_file);
    }

        $theorie_file_path = "files/exams/".$exam_name."/".$username."/theorie.txt";
        $theorie_file = fopen($theorie_file_path, "r");
        fclose($theorie_file);




        $praxis_file_path = "files/exams/".$exam_name."/".$username."/praxis.py";
        $praxis_file = fopen($praxis_file_path, "r");
        fclose($praxis_file);
        if($praxis_file_content == "") {
            $praxis_file_content = "Du hast noch nichts programmiert.";
        }

}
    


if (isset($_POST['nextTask'])) {
    // Wenn die aktuelle Aufgabe die letzte Aufgabe ist, beende die Prüfung

    $current_task_data = $tasks[$_SESSION['current_exam_task'] - 1];

    $exam_id = $_POST['exam_id'];
    $exam_token = $_POST['exam_token'];

    $submitted_by = $_SESSION['user_id'];
    $submitted_at = date("Y-m-d H:i:s");
    $difficult_level = isset($_POST['difficult_level']) ? $_POST['difficult_level'] : "0";

    // Wenn der Typ der aktuellen Aufgabe "theorie" ist, dann ist auch $answer_type "theorie"
    $answer_type = $current_task_data['type'];

    $answer_text = $_POST['answer_text'];

    if ($answer_type == "theorie") {
        //Schreibe den Theorie Text in die Datei theorie.txt und speichere in der Datenbank den Pfad zur Datei
        $theorie_file_path = "files/exams/" . $exam_name . "/" . $username . "/theorie.txt";
        $theorie_file = fopen($theorie_file_path, "w");
        fwrite($theorie_file, $answer_text);
        fclose($theorie_file);
        $answer_text = "files/exams/" . $exam_name . "/" . $username . "/theorie.txt";
    } elseif ($answer_type = "praxis") {
        $answer_text = $praxis_file_path;
    }

    $answered_at = date("Y-m-d H:i:s");

    // Berechne die Zeit, wie lange der Schüler für die Aufgabe gebraucht hat
    if ($_SESSION['current_exam_task'] == 1) {
        $time_needed = strtotime($answered_at) - strtotime($exam_general_information['started_at']);
        // Formatiere zum timestamp
        $time_needed = gmdate("H:i:s", $time_needed);
    } else {
        // Hole den Zeitpunkt der letzten Antwort aus dem JSON-Array (answer_json)
        $answer_json = json_decode($exam_general_information['answer_json'], true);
        $last_answered_at = end($answer_json[$_SESSION['user_id']]['answers']['answered_at']);
        $time_needed = strtotime($answered_at) - strtotime($last_answered_at);
        // Formatiere zum timestamp
        $time_needed = gmdate("H:i:s", $time_needed);
    }

    $final_comment = "0";
    $final_grade = "0";
    if (!isset($forced_submission)) {
        $forced_submission = "0";
    }

    // Prüfe, ob bereits Prüfungsantworten vorhanden sind
    if (isset($exam_general_information['answer_json'])) {
        $jsonArray = json_decode($exam_general_information['answer_json'], true);

        // Extrahiere vorhandene Aufgaben und Antworten
        $answers = $jsonArray[$_SESSION['user_id']]['answers'];
        $completed_tasks = $jsonArray[$_SESSION['user_id']]['details']['tasks_completed'];

        // Füge den neuen Task der vorhandenen Antwort hinzu
        $answers['answer_for_task'][] = $_SESSION['current_exam_task'];
        $answers['type'][] = $answer_type;
        $answers['answer_text'][] = $answer_text;
        $answers['answered_at'][] = $answered_at;
        $answers['time_needed'][] = $time_needed;
        $answers['forced_submission'][] = $forced_submission;

        // Aktualisiere die Details (Anzahl der abgeschlossenen Aufgaben)
        $jsonArray[$_SESSION['user_id']]['details']['tasks_completed'] = $completed_tasks + 1;

        // Aktualisiere das JSON-Array mit den neuen Aufgaben und Antworten
        $jsonArray[$_SESSION['user_id']]['answers'] = $answers;
    } else {
        // Erstelle ein neues JSON-Array, wenn noch keine Prüfungsantworten vorhanden sind
        $jsonArray = array(
            $_SESSION['user_id'] => array(
                "details" => array(
                    "completed_at" => $submitted_at,
                    "tasks_completed" => $_SESSION['current_exam_task'],
                    "final_grade" => $final_grade,
                    "final_comment" => $final_comment
                ),
                "answers" => array(
                    "answer_for_task" => array($_SESSION['current_exam_task']),
                    "type" => array($answer_type),
                    "answer_text" => array($answer_text),
                    "answered_at" => array($answered_at),
                    "time_needed" => array($time_needed),
                    "forced_submission" => array($forced_submission)
                )
            )
        );
    }

    $jsonString = json_encode($jsonArray);

    // Speichere die Prüfungsantwort in der Datenbank
    if ($db->saveExamAnswer($exam_id, $jsonString)) {
        if ($_SESSION['current_exam_task'] == count($tasks)) {
            $exam_id = $_POST['exam_id'];
            $exam_token = $_POST['exam_token'];
            if ($db->completeExam($exam_id)) {
                unset($_SESSION['current_exam_task']);
                header("Location: exam&token=$exam_token");
                exit;
            } else {
                echo "Fehler beim Beenden der Prüfung!";
                exit;
            }
        } else {
            $_SESSION['current_exam_task']++;
            header("Location: exam&token=$exam_token");
        }
    } else {
        echo "Fehler beim Speichern der Prüfungsantwort!";
        exit;
    }
}

if(isset($_POST['joinExam'])) {
    if($db->joinExam($exam_general_information['id'])) {
        header("Location: exam&token=$exam_token");
    } else {
        echo "Fehler beim Beitreten zur Prüfung!";
        exit;
    }
}

if(isset($_POST['changeLobbyState'])) {
    if($db->changeExamLobbyState($exam_general_information['id'])) {
        header("Location: exam&token=$exam_token");
    } else {
        echo "Fehler beim Ändern des Lobby-Status!";
        exit;
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
    <title><?=$exam_general_information['title']?> - Prüfung - ide4school</title>
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

    
    <link rel="stylesheet" type="text/css" href="app-assets/css/plugins/forms/form-quill-editor.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/editors/quill/katex.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/editors/quill/monokai-sublime.min.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/editors/quill/quill.snow.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/editors/quill/quill.bubble.css">

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
<form action="exam&token=<?=$exam_general_information['token']?>" method="POST" id="updateSessionClassForm">
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
                <section class="app-user-view-account">
                    <div class="row">
                        <!-- User Sidebar -->
                        <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                            <!-- User Card -->
                            <div class="card">
                                <div class="card-body">
                                <?php
                                    if($getCurrentUserData['role'] != "Schüler" || $exam_general_information['status'] != "1") {
                                        ?>
                                    <div class="user-avatar-section">
                                        <div class="d-flex align-items-center flex-column">
                                            <img class="img-fluid rounded mt-3 mb-2" src="app-assets/images/illustration/api.svg" height="450px" width="450px" alt="User avatar" />
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
                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if($getCurrentUserData['role'] != "Schüler" || $exam_general_information['status'] != "1") {
                                        ?>
                                        
                                    <h4 class="fw-bolder border-bottom pb-50 mb-1">Prüfungsdetails</h4>
                                    <div class="info-container">
                                        <ul class="list-unstyled">


                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Titel:</span>

                                                <span><?=$exam_data['settings']['title']?></span>
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Nachricht:</span>
                                                <span><?=$exam_data['settings']['message']?></span>
                                            </li>
                                            <?php
                                            if($exam_general_information['status'] == "0") {
                                                ?>
                                                <li class="mb-75">
                                                    <span class="fw-bolder me-25">Durchführung geplant am:</span>
                                                    <span><?=$exam_general_information['planned_for']?></span>
                                                </li>
                                                <?php
                                            }elseif ($exam_general_information['status'] == "1") {
                                                ?>
                                                <li class="mb-75">
                                                    <span class="fw-bolder me-25">Durchführung gestartet:</span>
                                                    <span><?=$exam_general_information['started_at']?></span>
                                                </li>
                                                <?php
                                            }
                                            else {
                                                ?>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Durchgeführt am:</span>
                                                <span><?=$exam_general_information['started_at']?></span>
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Beendet am:</span>
                                                <span><?=$exam_general_information['finished_at']?></span>
                                            </li>
                                            <?php
                                            }
                                            ?>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Aufgabenanzahl:</span>
                                                <?php
                                                //Zähle die tasks aus dem json array
                                                $task_count = count($exam_data['tasks']);
                                                echo $task_count;
                                                ?>
                                            </li>
                                            <li class="mb-75">
                                                <span class="fw-bolder me-25">Status:</span>
                                                <?php
                                                if($exam_general_information['status'] == "0") {
                                                    echo '<span class="badge bg-light-danger">Geplant - Noch nicht gestartet</span>';
                                                } elseif($exam_general_information['status'] == "1") {
                                                    echo '<span class="badge bg-light-warning">Durchführung läuft</span>';
                                                }
                                                elseif($exam_general_information['status'] == "2" && $exam_general_information['reviewed'] == "0") {
                                                    echo '<span class="badge bg-light-info">Zur Bewertung ausstehend</span>';
                                                }
                                                elseif($exam_general_information['status'] == "2" && $exam_general_information['reviewed'] == "1") {
                                                    echo '<span class="badge bg-light-success">Bewertet</span>';
                                                }
                                                ?>
                                            </li>
                                            
                                            
    
                                        </ul>
                                        <?php
                                        echo "</div>";
                                            }
                                        ?>
                                        
                                            
                                        <br /><?php
                                        if($getCurrentUserData['role'] == "Lehrer" OR $getCurrentUserData['role'] == "Administrator") {
                                        ?>
                                            <form action="exam&token=<?=$exam_general_information['token']?>" method="POST">
                                            <h4 class="fw-bolder border-bottom pb-50 mb-1">Prüfungsverwaltung</h4>
                                            <div class="info-container">
                                                <div class="row">
                                                     <div class="col-12">
                                                    

                                                                                                    <?php
                                                $currentTimestamp = time();
                                                $countdownStartedAt = strtotime($exam_general_information['countdown_started_at']);
                                                $endTime = $countdownStartedAt + ($exam_data['settings']['time'] * 60);
                                                $timeRemaining = $endTime - $currentTimestamp;

                                                $remainingMinutes = floor($timeRemaining / 60);
                                                $remainingSeconds = $timeRemaining % 60;

                                                //Countdown text wenn status != 1 Geplante Zeit, wenn status == 1 Verbleibende Zeit und bei status == 2 Gegebene Zeit
                                                if($exam_general_information['status'] == "0") {
                                                    $countdownText = "Geplante";
                                                } elseif($exam_general_information['status'] == "1") {
                                                    $countdownText = "Verbleibende";
                                                } else {
                                                    $countdownText = "Gegebene";
                                                }

                                                ?>

                                                <ul class="list-unstyled">
                                                    <li class="mb-75">
                                                        <span class="fw-bolder me-25">
                                                            <?=$countdownText?> Zeit:
                                                        </span>
                                                        <span id="countdown">
                                                            <?php
                                                            if($exam_general_information['status'] == "0") {
                                                                echo $exam_data['settings']['time'];
                                                            } elseif($exam_general_information['status'] == "1") {
                                                                echo $remainingMinutes . ':' . str_pad($remainingSeconds, 2, "0", STR_PAD_LEFT);
                                                            }
                                                            else {
                                                                //Gebe aus wieviel Zeit benötigt wurde -> berechne aus started_at und finished_at und gebe als timestamp aus
                                                                $startedAt = strtotime($exam_general_information['started_at']);
                                                                $finishedAt = strtotime($exam_general_information['finished_at']);
                                                                $timeNeeded = $finishedAt - $startedAt;
                                                                $timeNeededMinutes = floor($timeNeeded / 60);
                                                                $timeNeededSeconds = $timeNeeded % 60;
                                                                echo $timeNeededMinutes . ':' . str_pad($timeNeededSeconds, 2, "0", STR_PAD_LEFT);
                                                            }
                                                            ?>
                                                             Minuten
                                                        </span>
                                                    </li>
                                                </ul>

                                                <script>
                                                    var seconds = <?=$timeRemaining?>;
                                                    function secondPassed() {
                                                        var minutes = Math.floor(seconds / 60),
                                                            remainingSeconds = seconds % 60;

                                                        if (remainingSeconds < 10) {
                                                            remainingSeconds = "0" + remainingSeconds;
                                                        }

                                                        var countdownElement = document.getElementById('countdown');
                                                        countdownElement.innerHTML = minutes + ":" + remainingSeconds + " Minuten";

                                                        if (minutes <= 15) {
                                                            countdownElement.style.color = "orange";
                                                        }

                                                        if (minutes <= 10) {
                                                            countdownElement.style.color = "red";
                                                        }

                                                        if (minutes <= 5) {
                                                            countdownElement.style.animation = "pulse 1s infinite";
                                                        } else {
                                                            countdownElement.style.animation = "none";
                                                        }

                                                        if (seconds == 0) {
                                                            clearInterval(countdownTimer);
                                                            countdownElement.innerHTML = "Zeit abgelaufen";
                                                        } else {
                                                            seconds--;
                                                        }
                                                    }

                                                    <?php if($exam_general_information['status'] == "1"): ?>
                                                    var countdownTimer = setInterval(secondPassed, 1000);
                                                    <?php endif; ?>
                                                    </script>


                                                    </div>
                                                    <input type="hidden" name="exam_token" value="<?=$exam_general_information['token']?>">
                                                    <input type="hidden" name="exam_id" value="<?=$exam_general_information['id']?>">
                                                    <input type="hidden" name="<?php 
                                                    if($exam_general_information['status'] == "0") {
                                                        echo 'startExam';
                                                    } elseif($exam_general_information['status'] == "1") {
                                                        echo 'endExam';
                                                    }
                                                    ?>">
                                                    <?php
                                                    if($exam_general_information['status'] == "0") {
                                                        ?>
                                                    <button type="button" OnClick="changeLobbyState()" class="btn btn-warning me-1" id="exam_open_lobby_button">
                                                    Teilnehmer in Prüfung leiten:  <?php 
                                                    if($exam_general_information['exam_redirect'] == "1") {
                                                        echo 'aktiv';
                                                    } elseif($exam_general_information['exam_redirect'] == "0") {
                                                        echo 'inaktiv';
                                                    }
                                                    }
                                                    ?>
                                                </button><p></p>
                                                    <button <?php 
                                                    if($exam_general_information['status'] == "1" || $exam_general_information['status'] == "0") {
                                                         echo 'type="submit"';
                                                         } else {
                                                             echo 'type="button"';
                                                            } 
                                                             ?>  
                                                             class="btn btn-danger me-1" id="exam_control_button">
                                                    Prüfung <?php 
                                                    if($exam_general_information['status'] == "0") {
                                                        echo 'starten';
                                                    } elseif($exam_general_information['status'] == "1") {
                                                        echo 'beenden';
                                                    }
                                                    else {
                                                        echo 'bereits durchgeführt';
                                                    }
                                                    ?>
                                                </button>
                                                </div>
                                                <?php
                                                if($exam_general_information['status'] == "1") {
                                                    echo '<div class="col-12 mt-1" style="text-align: center;">
                                                    <a href="#" onClick="forceSubmit" >
                                                        Schüler zur Abgabe zwingen
                                                    </a>
                                                </div>';
                                                }
                                                ?>
                                                 <?php
                                                if($exam_general_information['status'] == "2") {
                                                    echo '<div class="col-12 mt-1" style="text-align: center;">
                                                    
                                                       
                                                    
                                                    <a href="exam_review&token='.$exam_general_information['token'].'"><button type="button" class="btn btn-primary me-1">
                                                    Prüfung bewerten
                                            </button></a> 
                                                    
                                                    <a OnClick="deleteExam()" class="btn btn-outline-danger suspend-user">Löschen</a>
                                                </div>';
                                                }
                                                ?>
                                            </div>
                                        
                                        
                                        </form>
                                        <?php
                                        }
                                        elseif($getCurrentUserData['role'] == "Schüler") {
                                        ?>
                                        <form action="exam&token=<?=$exam_general_information['token']?>" method="POST">
                                            <h4 class="fw-bolder border-bottom pb-50 mb-1">
                                                <?php
                                                if($exam_general_information['status'] != "1") {
                                                    echo 'Prüfungsinformationen';
                                                } elseif($exam_general_information['status'] == "1") {
                                                    echo 'Prüfungsinformationen';
                                                }
                                                ?>
                                            </h4>
                                            <div class="info-container">
                                                <div class="row">
                                                     <div class="col-12">
                                                    

                                                                                                    <?php
                                                $currentTimestamp = time();
                                                $countdownStartedAt = strtotime($exam_general_information['countdown_started_at']);
                                                $endTime = $countdownStartedAt + ($exam_data['settings']['time'] * 60);
                                                $timeRemaining = $endTime - $currentTimestamp;

                                                $remainingMinutes = floor($timeRemaining / 60);
                                                $remainingSeconds = $timeRemaining % 60;

                                                $countdownText = ($exam_general_information['status'] != "1") ? 'Geplante' : 'Verbleibende';

                                                ?>

                                                <ul class="list-unstyled">
                                                    <li class="mb-75">
                                                        <span class="fw-bolder me-25">
                                                            <?=$countdownText?> Zeit:
                                                        </span>
                                                        <span id="countdown">
                                                            <?php
                                                            if($exam_general_information['status'] != "1") {
                                                                echo $exam_data['settings']['time'];
                                                            } else {
                                                                echo $remainingMinutes . ':' . str_pad($remainingSeconds, 2, "0", STR_PAD_LEFT);
                                                            }
                                                            ?>
                                                             Minuten
                                                        </span>
                                                    </li>
                                                </ul>

                                                    <script>
                                                    var seconds = <?=$timeRemaining?>;
                                                    function secondPassed() {
                                                        var minutes = Math.floor(seconds / 60),
                                                            remainingSeconds = seconds % 60;

                                                        if (remainingSeconds < 10) {
                                                            remainingSeconds = "0" + remainingSeconds;
                                                        }

                                                        var countdownElement = document.getElementById('countdown');
                                                        countdownElement.innerHTML = minutes + ":" + remainingSeconds + " Minuten";

                                                        if (minutes <= 15) {
                                                            countdownElement.style.color = "orange";
                                                        }

                                                        if (minutes <= 10) {
                                                            countdownElement.style.color = "red";
                                                        }

                                                        if (minutes <= 5) {
                                                            countdownElement.style.animation = "pulse 1s infinite";
                                                        } else {
                                                            countdownElement.style.animation = "none";
                                                        }

                                                        if (seconds <= 0) {
                                                            clearInterval(countdownTimer);
                                                            countdownElement.innerHTML = "Zeit abgelaufen";
                                                        } else {
                                                            seconds--;
                                                        }
                                                    }

                                                    <?php if($exam_general_information['status'] == "1"): ?>
                                                    var countdownTimer = setInterval(secondPassed, 1000);
                                                    <?php endif; ?>
                                                    </script>


                                                    </div>
                                                    
                                                </div>
                                                
                                            </div>
                                        
                                        
                                        </form>

                                        <?php
                                        }
                                        ?>
                                        <?php
                                if($exam_general_information['status'] == "1" && $getCurrentUserData['role'] == "Schüler") {
                                    echo '<br />
                                    <h4 class="fw-bolder border-bottom pb-50 mb-1">Prüfungsaufgaben</h4>
                                    <section id="accordion-with-border">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div id="accordionWrapa50" role="tablist" aria-multiselectable="true">
                                                <div class="card">
                                                    <div class="card-header">
                                                    <h6><b>Wichtig:</b></h6>
                                                    <p class="card-text">
                                                            <i>Bearbeite nun nach und nach deine Prüfungsaufgaben. Wenn du mit einer Aufgabe fertig bist, kannst du diese als "Abgeschlossen" markieren und mit der nächsten Aufgabe weiter machen. Sobald du alle Aufgaben abgeschlossen hast, kannst du deine Prüfung beenden. Beachte, dass Aufgaben die als "Abgeschlossen" markiert wurden, nicht mehr bearbeitet werden können.</i>
                                                        </p>
                                                        </div>
                                                    <div class="card-body">
                                                        <div class="accordion accordion-border" id="accordionBorder" data-toggle-hover="true">';
                                                        $answer_json = json_decode($exam_general_information['answer_json'], true);
                                                        foreach($tasks as $task) {
                                                            //aktuelle aufgabe mit index
                                                            $currentTaskIndex = array_search($task, $tasks);
                                                            $currentTaskIndex = $currentTaskIndex + 1;
                                                            
                                                            // Überprüfe, ob die Aufgabe bereits im JSON-Array vorhanden ist
                                                            $taskCompleted = false;
                                                            if (isset($answer_json[$_SESSION['user_id']]['answers']['answer_for_task'])) {
                                                                $completedTasks = $answer_json[$_SESSION['user_id']]['answers']['answer_for_task'];
                                                                if (in_array($currentTaskIndex, $completedTasks)) {
                                                                    $taskCompleted = true;
                                                                }
                                                            }
                                                            
                                                            $title = $currentTaskIndex . ". " . $task['name'];
                                                            if ($taskCompleted) {
                                                                $title = "<s>" . $title . "</s>";
                                                            }

                                                            $allTasksCompleted = false;
                                                            if (isset($answer_json[$_SESSION['user_id']]['answers']['answer_for_task'])) {
                                                                $completedTasks = $answer_json[$_SESSION['user_id']]['answers']['answer_for_task'];
                                                                $totalTasks = count($tasks);
                                                                if (count($completedTasks) === $totalTasks) {
                                                                    $allTasksCompleted = true;
                                                                }
                                                            }
                                                            
                                                            echo '
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="headingBorder'.$currentTaskIndex.'">
                                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordionBorder'.$currentTaskIndex.'" aria-expanded="false" aria-controls="accordionBorder'.$currentTaskIndex.'">
                                                                        '.$title.'
                                                                    </button>
                                                                </h2>
                                                                <div id="accordionBorder'.$currentTaskIndex.'" class="accordion-collapse collapse" aria-labelledby="headingBorder'.$currentTaskIndex.'" data-bs-parent="#accordionBorder" style="">
                                                                    <div class="accordion-body">
                                                                        '.$task['description'].'<br /><br />
                                                                        <button ';
                                                            if ($_SESSION['current_exam_task'] != $currentTaskIndex || $taskCompleted) {
                                                                echo 'disabled ';
                                                            }
                                                            echo ' class="btn btn-success" OnClick="submitAnswer()" type="button"><i data-feather="check-square"></i> ';
                                                            if ($_SESSION['current_exam_task'] < $currentTaskIndex && !$taskCompleted) {
                                                                echo 'Aktuelle Aufgabe abschließen ';
                                                            } elseif ($_SESSION['current_exam_task'] > $currentTaskIndex || $taskCompleted) {
                                                                echo 'Aufgabe bereits erledigt ';
                                                            } else {
                                                                echo 'Als erledigt markieren';
                                                            }
                                                            echo '</button>
                                                                    </div>
                                                                </div>
                                                            
                                                         
                                                            </div>';
                                                        }
                                                        echo '
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>';
                                    



                                }
                                ?>
                                        
                                    </div>
                                   
                                        
                                        
                                      
                                    
                                    
                                </div>
                            </div>
                            <!-- /User Card -->
                            
                        
                        <!--/ User Sidebar -->

                        <?php
                        if($getCurrentUserData['role'] == "Schüler") {
                        ?>
                        <!-- User Content -->
                        <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-5">
                           
                           
                            <div>
                                <?php
                                $single_task_data = $tasks[$_SESSION['current_exam_task']-1];
                                    if($single_task_data['type'] == "theorie" && $exam_general_information['status'] == "1" && $getCurrentUserData['role'] == "Schüler" && !$allTasksCompleted  && $db->isStudentInThisExam($_SESSION['user_id'], $exam_general_information['id'])) {
                                        //Zeige den Quill Editor an
                                        echo '
                                        <section class="full-editor">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h4 class="card-title">Theorieaufgabe: '.$single_task_data['name'].'</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        <p class="card-text">
                                                           Schreibe jetzt hier deine Antworten zu den Theorieaufgaben auf.
                                                        </p>

                                                        <button class="btn btn-success" OnClick="submitAnswer()" type="button"><i data-feather="check-square"></i> Als erledigt markieren</button>
                                                        <br /><br /><hr><br />
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                            <div id="full-wrapper">
                                                            <div id="full-container">
                                                                <div class="editor ql-container ql-snow" style="min-height: 425px;">
                                                                    <div class="ql-editor" data-gramm="false" contenteditable="true">'.$theorie_file_content.'</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>';
                                    }
                                        elseif($single_task_data['type'] == "praxis" && $exam_general_information['status'] == "1" && $getCurrentUserData['role'] == "Schüler" && !$allTasksCompleted  && $db->isStudentInThisExam($_SESSION['user_id'], $exam_general_information['id'])) {


                                            echo '
                                        <section class="full-editor">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h4 class="card-title">Praxisaufgabe: '.$single_task_data['name'].'</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        <p class="card-text">
                                                          Öffne die Entwicklungsumgebung und beginne mit dem Programmieren. <br />Klicke auf den Button "Aktualisieren" um den Code ggf. aus der Entwicklungsumgebung zu laden.
                                                        </p>
                                                        <button class="btn btn-primary" OnClick="openPraxisEditor()" type="button"><i data-feather="edit"></i> Entwicklungsumgebung öffnen</button>
                                                        <button class="btn btn-success" OnClick="submitAnswer()" type="button"><i data-feather="check-square"></i> Als erledigt markieren</button>
                                                        <br /><br /><hr><br />
                                                        <div class="row">
                                                        <p><span>
                                                            <b>Aktueller Programmcode:</b>
                                                            </span><br />
                                                            <span style="text-align: right;">
                                                            <button style="text-align: right;" class="btn btn-primary" OnClick="refreshPraxisCode()" type="button"><i data-feather="refresh-cw"></i> Aktualisieren</button>
                                                            </span>
                                                            </p>
                                                            <br /><br />
                                                            <pre><code class="language-python"><br />'.$praxis_file_content.'<br /><br /></code></pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>';

                                    if($single_task_data['vof'] == "on") {
                                        $_SESSION['disk_state'] = "non_writable";
                                        $_SESSION['exam_token_identifier'] = $exam_general_information['token'];
                                        echo '<div class="col-xl-12 col-lg-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="card-title">Zugriff auf alte Dateien (schreibgeschützt)</h4>
                                            </div>
                                            <div class="card-body">
                                                <ul class="nav nav-tabs" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link '; if($_GET['vof'] == "usr" || !isset($_GET['vof'])) { echo 'active';} echo '" id="userDataIcon-tab"  href="exam&token='.$exam_general_information['token'].'&vof=usr" aria-controls="home" role="tab" aria-selected="true"><i data-feather="hard-drive"></i> Benutzerdateien</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link '; if($_GET['vof'] == "prj") { echo 'active';} echo '" id="projectDataIcon-tab" href="exam&token='.$exam_general_information['token'].'&vof=prj" aria-controls="profile" role="tab" aria-selected="false"><i data-feather="layers"></i> Projektdateien</a>
                                                    </li>
                                                </ul>
                                                <div class="tab-content" style="min-height: 250px;">
                                                ';
                                                if($_GET['vof'] == "usr" || !isset($_GET['vof'])) {
                                                    echo '<div class="tab-pane active" style="min-height: 250px;" id="userDataIcon" aria-labelledby="userDataIcon-tab" role="tabpanel">
                                                    <iframe width="100%" height="100%" style="min-height: 250px;" src="inc/apps/files-core.php?p=&drive=exam-my-folder"></iframe>
                                                    </div>';
                                                }
                                                elseif($_GET['vof'] == "prj") {
                                                    echo '<div class="tab-pane active" style="min-height: 250px;" id="projectDataIcon" aria-labelledby="projectDataIcon-tab" role="tabpanel">
                                                    <iframe width="100%" height="100%" style="min-height: 250px;" src="inc/apps/files-core.php?p=&drive=exam-projects"></iframe>
                                                    </div>';
                                                } 
                                            
                                                    
                                                    
                                                   echo '
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                                    }
                                        }
                                        elseif($getCurrentUserData['role'] == "Schüler" && $allTasksCompleted || $exam_general_information['status'] == "2" && $getCurrentUserData['role'] == "Schüler" && $db->isStudentInThisExam($_SESSION['user_id'], $exam_general_information['id'])) {
                                            echo '
                                        <section class="full-editor">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h4 class="card-title">Prüfung beendet.</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        <p class="card-text">
                                                         Du hast die Prüfung erfolgreich abgeschlossen. <br />Du kannst die Prüfungslobby nun verlassen.
                                                        </p>
                                                        <button class="btn btn-success" OnClick="leaveExam()" type="button"><i data-feather="log-out"></i> Prüfungslobby verlassen</button>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>';
                                        }
                                        elseif($exam_general_information['status'] == "0" && $getCurrentUserData['role'] == "Schüler" && !$allTasksCompleted && $db->isStudentInThisExam($_SESSION['user_id'], $exam_general_information['id'])) {
                                            echo '
                                        <section class="full-editor">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h4 class="card-title">Prüfung wurde noch nicht gestartet.</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        <p class="card-text">
                                                            Die Prüfung wurde noch nicht gestartet. <br />Bitte warte bis der Lehrer die Prüfung startet und aktualisiere dann die Seite mit dem Button "Prüfungsstatus abfragen".
                                                        </p>
                                                        <button class="btn btn-danger" OnClick="refreshPraxisCode()" type="button"><i data-feather="refresh-cw"></i> Prüfungsstatus abfragen</button>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>';
                                        }
                                        elseif(!$db->isStudentInThisExam($_SESSION['user_id'], $exam_general_information['id'])) {
                                            echo '
                                        <section class="full-editor">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h4 class="card-title">Prüfung beitreten</h4>
                                                    </div>
                                                    <div class="card-body">
                                                        <p class="card-text">
                                                            Um der Prüfung beizutreten, klicke bitte auf den Button "Prüfung beitreten". <br />Du wirst dann automatisch in die Prüfungslobby weitergeleitet.
                                                        </p>
                                                        <button class="btn btn-success" OnClick="joinExam()" type="button"><i data-feather="log-in"></i> Prüfung beitreten</button>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>';
                                        }
                                ?>
                            </div>
                               

                            
                        </div>
                        <!--/ User Content -->
                        <?php
                        }
                        ?>

                        <?php
                        if($getCurrentUserData['role'] != "Schüler") {
                            ?>

<!-- User Content -->
<div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-5">
                           
                           
                           <div>
                            <!-- Student table -->
                            <div class="card">
                                <h4 class="card-header">Prüfungsübersicht: Schüler:innen - <?php
                                $class_id = $exam_general_information['class'];
                                $class_name = $db->getClassNameViaID($class_id);
                                echo $class_name;
                                $students = $db->getAllUsersFromClass($class_name);
                                ?></h4>
                                <div class="table-responsive">
                                    <table class="user-list-table table">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Name</th>
                                                <th>Status</th>
                                    
                                                <th>Prüfung beigetreten am</th>
                                                <th>Abgegeben am</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                            <?php
                                            if(!$students) {
                                                echo '<td>Keine Benutzer in dieser Klasse.</td>';
                                            }
                                            else {
                                            foreach($students as $student) {
                                                echo '<tr>';
                                                echo '<td><a href="user&id=' . $student['id'] . '">' . $student['secondName'].", ". $student['firstName'] . '</a></td>';
                                                if($db->isStudentInThisExam($student['id'], $exam_general_information['id']) && $exam_general_information['status'] == "0") {
                                                    echo '<td><span class="badge rounded-pill badge-light-warning">In Prüfungslobby</span></td>';
                                                }
                                                elseif($db->isStudentInThisExam($student['id'], $exam_general_information['id']) && $exam_general_information['status'] == "1") {
                                                    echo '<td><span class="badge rounded-pill badge-light-info">In Prüfung</span></td>';
                                                }
                                                elseif($db->hasStudentSubmittedExam($student['id'], $exam_general_information['id'])) {
                                                    echo '<td><span class="badge rounded-pill badge-light-success">Prüfung abgegeben</span></td>';
                                                }
                                                elseif($exam_general_information['status'] == "2") {
                                                    echo '<td><span class="badge rounded-pill badge-light-danger">Prüfung zwangsmäßig beendet</span></td>';
                                                }
                                                else {
                                                    echo '<td><span class="badge rounded-pill badge-light-danger">Nicht in Prüfungslobby</span></td>';
                                                }
                                    
                                                if($student['exam_join_timestamp'] == "0000-00-00 00:00:00") {
                                                    echo '<td style="text-align: center;">---</td>';
                                                }
                                                else {
                                                    echo '<td style="text-align: center;">'.$student['exam_join_timestamp'].'</td>';
                                                }
                                                if($db->hasStudentSubmittedExam($student['id'], $exam_general_information['id'])) {
                                                    //Nimm dir die answer_json und suche dir den wert von [$student['id']]['details']['completed_at']
                                                    $json_data = json_decode($exam_general_information['answer_json'], true);
                                                    $completed_at = $json_data[$student['id']]['details']['completed_at'];
                                                    echo '<td style="text-align: center;">'.$completed_at.'</td>';
                                                }
                                                else {
                                                    echo '<td style="text-align: center;">---</td>';
                                                }
                                                echo '</tr>';
                                            }
                                        }
                                            ?>
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /Student table -->
                           </div>
                              

                           
                       </div>
                       <!--/ User Content -->


                            <?php
                        }
                        ?>
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

    <script src="app-assets/vendors/js/editors/quill/katex.min.js"></script>
    <script src="app-assets/vendors/js/editors/quill/highlight.min.js"></script>
    <script src="app-assets/vendors/js/editors/quill/quill.min.js"></script>
    <script src="app-assets/js/scripts/forms/form-quill-editor.js"></script>

    <!-- BEGIN: Page JS-->
    <script src="app-assets/js/scripts/pages/modal-edit-user.js"></script>

    <!-- END: Page JS-->
    <?php include('inc/components/createEnviroment.php'); ?>
    <?php
if($getCurrentUserData['role'] == "Schüler") {
    echo "
    <style>
    #menucontainer.disabled {
     opacity: 0.5; /* Verringere die Deckkraft, um es als deaktiviert anzuzeigen */
     pointer-events: none; /* Verhindere, dass Ereignisse an das Element weitergeleitet werden */
}

    </style>

    <script>
    var menucontainer = document.getElementById('menucontainer');
    menucontainer.classList.add('disabled');
    </script>";
}
if($getCurrentUserData['role'] == "Schüler" && $exam_general_information['status'] != "1") {
    echo "
    <style>
    #exam_submit_button.disabled {
     opacity: 0.5; /* Verringere die Deckkraft, um es als deaktiviert anzuzeigen */
     pointer-events: none; /* Verhindere, dass Ereignisse an das Element weitergeleitet werden */
}

    </style>

    <script>
    var exam_submit_button = document.getElementById('exam_submit_button');
    exam_submit_button.classList.add('disabled');
    </script>";
}

if($getCurrentUserData['role'] != "Schüler" && $exam_general_information['status'] == "2") {
    echo "
    <style>
    #exam_control_button.disabled {
     opacity: 0.5; /* Verringere die Deckkraft, um es als deaktiviert anzuzeigen */
     pointer-events: none; /* Verhindere, dass Ereignisse an das Element weitergeleitet werden */
}

    </style>

    <script>
    var exam_control_button = document.getElementById('exam_control_button');
    exam_control_button.classList.add('disabled');
    </script>";
}
?>

<script>

    <?php
    if($exam_data['tasks'][$_SESSION['current_exam_task']-1]['type'] == "theorie") {
        echo 'var answercontainer = document.getElementsByClassName("ql-editor")[0];
        answercontainer.setAttribute("id", "answercontainer");';
    }
    ?>
    

    function submitAnswer() {
        <?php
    if($exam_data['tasks'][$_SESSION['current_exam_task']-1]['type'] == "theorie") {
        echo "var answer = document.getElementById('answercontainer').innerHTML;
        document.getElementById('answer_text').value = answer;";
    } 
    elseif($exam_data['tasks'][$_SESSION['current_exam_task']-1]['type'] == "praxis") {
        ?>
        document.getElementById('answer_text').value = `<?=$praxis_file_content?>`;
        <?php
    }
    ?>
        
        document.getElementById("submitAnswerForm").submit();
    }

    function refreshPraxisCode() {
        window.location.href = "exam&token=<?=$exam_general_information['token']?>";
    }

    function openPraxisEditor() {
        document.getElementById("openPraxisIDE").submit();
    }

    function leaveExam() {
        window.location.href = "dashboard&return_from_exam";
    }

    function joinExam() {
        document.getElementById("joinExamForm").submit();
    }

    function changeLobbyState() {
        document.getElementById("changeLobbyStateForm").submit();
    }
</script>


<form action="exam&token=<?=$exam_general_information['token']?>" method="POST" id="submitAnswerForm">
    <input type="hidden" name="nextTask" value="nextTask">
    <input type="hidden" id="answer_text" name="answer_text">
    <input type="hidden" name="exam_id" value="<?=$exam_general_information['id']?>">
    <input type="hidden" name="exam_token" value="<?=$exam_general_information['token']?>">
</form>

<form id="openPraxisIDE" action="editor_main.php" method="POST">
    <input type="hidden" name="exam_id" value="<?=$exam_general_information['id']?>">
    <input type="hidden" name="exam_token" value="<?=$exam_general_information['token']?>">
    <input type="hidden" name="praxis_filepath" value="<?=$praxis_file_path?>">
    <input type="hidden" name="praxis_filename" value="Prüfungsaufgabe: <?=$exam_data['tasks'][$_SESSION['current_exam_task']-1]['name']?>">
    <input type="hidden" name="exam">
</form>

<form action="exam&token=<?=$exam_general_information['token']?>" method="POST" id="joinExamForm">
    <input type="hidden" name="joinExam" value="joinExam">
</form>

<form action="exam&token=<?=$exam_general_information['token']?>" method="POST" id="changeLobbyStateForm">
    <input type="hidden" name="changeLobbyState" value="changeLobbyState">
</form>

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