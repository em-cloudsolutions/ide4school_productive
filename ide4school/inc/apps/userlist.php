<?php
if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }
if(!$db->isUserLoggedIn()) {
    header("Location: not_authorized");
}
if(!isset($_SESSION['login_state']) && $db->getMFAMethodsFromUser($_SESSION['user_id']) != NULL) {
    header("Location: 2fa");
}

$users = $db->getAllUsers();
$classes = $db->getAllClasses();
$getCurrentUserData = $db->getCurrentUserInformations();

// SESSION CLASS MANAGMENT - BEGIN
$teacher_classes = $db->getAllowedClassesForTeachers();


$db->checkAssignmentRights();
if(isset($_POST['updateSessionClass'])) {
    $new_class = $_POST['class_name'];
    $_SESSION['currentSessionClass'] = $new_class;
    header("Location: users");
}
// SESSION CLASS MANAGMENT - END


$all_users_count = $db->countAllUsers();
$active_users_count = $db->countAllActiveUsers();
$focus_users_count = $db->getAllUsersWithActiveFocusMode();
$teachers_count = $db->countAllTeachers();

// Show User of specific class
$selected_class = 'Alle Klassen / Gruppen';

if(isset($_GET["class"])) {
    $selected_class = $_GET["class"];
    $users = $db->getAllUsersFromClass($selected_class);
    $_SESSION["class_return_destination"] = "class=" . $_GET["class"];
}


// Create new User

if(isset($_POST["createUser"]))
        {
          $log_user = $db->getLogUser();
          // Get Formular Data
          $firstName = $_POST['user-firstName'];
          $secondName = $_POST['user-secondName'];
          $class = $_POST['user-class'];
          $role = $_POST['user-role'];
          $unencrypted_password = $_POST['user-password'];
          $password = password_hash($unencrypted_password, PASSWORD_DEFAULT);
          $username = $_POST['user-username'];



          //Change alpha-num
          $username = str_replace("ö", "oe", $username);
          $username = str_replace("ä", "ae", $username);
          $username = str_replace("ü", "ue", $username);
          $username = str_replace("ß", "ss", $username);
          $username = str_replace("Ö", "Oe", $username);
          $username = str_replace("Ä", "Äe", $username);
          $username = str_replace("Ü", "Ue", $username);
          $username = str_replace("", "", $username);
          $username = str_replace("_", "", $username);
          $username = str_replace("-", "", $username);

          $spec_chars = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
          $username = strtr( $username, $spec_chars);

          // Create User Dir
          $user_dir = "files/users/" .  $username;
          $submission_dir = 'files/submissions/' . $username;

          // Insert in DB and create Folder
          if($db->createUser($firstName, $secondName, $class, $role, $username, $password, $user_dir, $log_user)) {
            if (!file_exists($user_dir)) {
                if(mkdir($user_dir, 0777, true)) {
                    if (!file_exists($submission_dir)) {
                        if(mkdir($submission_dir, 0777, true)) {
                            header("Location: users");
                        }
                        else {
                            $db->delete_user_emergency($firstName, $secondName, $class, $role, $username, $password, $user_dir);
                            echo'
                            <div class="alert alert-danger" role="alert">
                                                                <h4 class="alert-heading">Dateifehler</h4>
                                                                <div class="alert-body">
                                                                Benutzerordner konnte nicht erstellt werden. Vorgang abgebrochen. Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                                                </div>
                                                            </div>';
                            sleep(3);
                            header("Location: users");
                        }
                        }
                }
                else {
                    $db->delete_user_emergency($firstName, $secondName, $class, $role, $username, $password, $user_dir);
                    echo'
                    <div class="alert alert-danger" role="alert">
                                                        <h4 class="alert-heading">Datenbankfehler</h4>
                                                        <div class="alert-body">
                                                        Benutzer konnte nicht erstellt werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                                        </div>
                                                    </div>';
                    sleep(3);
                    header("Location: users");
                }
                }
            }
            else {
                echo'
                <div class="alert alert-danger" role="alert">
                                                    <h4 class="alert-heading">Datenbankfehler</h4>
                                                    <div class="alert-body">
                                                    Benutzer konnte nicht erstellt werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                                    </div>
                                                </div>';
            }
    };

    // Import users

if(isset($_POST["importUsers"]))
{
  $log_user = $db->getLogUser();
  $extension = strtolower(pathinfo($_FILES['list-file']['name'], PATHINFO_EXTENSION));

    require('src/spreadsheet_reader/php-excel-reader/excel_reader2.php');
    require('src/spreadsheet_reader/SpreadsheetReader.php');

        //Überprüfung der Dateiendung
        $allowed_extensions = array('xlsx');
        if(!in_array($extension, $allowed_extensions)) {
        die("Ungültige Dateiendung. Nur xlsx-Dateien sind erlaubt");
        }
        
        //Überprüfung der Dateigröße
        $max_size = 50000*1024; //50 MB
        if($_FILES['list-file']['size'] > $max_size) {
        die("Bitte keine Dateien größer als 50MB hochladen");
        }
 

        $uploadFilePath = 'files/administration/importlists/'.basename($_FILES['list-file']['name']);
        move_uploaded_file($_FILES['list-file']['tmp_name'], $uploadFilePath);


        $Reader = new SpreadsheetReader($uploadFilePath);


        $totalSheet = count($Reader->sheets());

        /* For Loop for all sheets */
        for($i=0;$i<$totalSheet;$i++){


        $Reader->ChangeSheet($i);

        function randomPassword() {
            $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
            $pass = array(); //remember to declare $pass as an array
            $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
            for ($i = 0; $i < 8; $i++) {
                $n = rand(0, $alphaLength);
                $pass[] = $alphabet[$n];
            }
            return implode($pass); //turn the array into a string
        }

        $request_header = '
        <img src="app-assets/images/logo/logo.png">
        ide4school
        ide4school.de';
         
         
        $request_footer = "<br /><br />Teilen Sie bitte die Zugangsdaten Ihrer Klasse aus!";
         
        
        $pdfName = "ide4school Mitgliederimport.pdf";
         
        
         
        $html = '
        <table cellpadding="5" cellspacing="0" style="width: 100%; ">
        
         
         <tr>
         <td style="font-size:1.3em; font-weight: bold;">
        
        ide4school Mitgliederimport
        <br>
         </td>
         </tr>
         
        
        </table>
        <br><br>Guten Tag!<br />
        <br />
        Nachfolgend finden Sie die Zugangsdaten der Nutzer, die Sie soeben importiert haben.<br /><br /><br />

        <hr>
        <table cellpadding="5" cellspacing="0" style="width: 100%;" border="0">
       
                    <tr>
                        <td><b>Name</b></td>
                        <td><b>Vorname</b></td>
                        <td><b>Klasse</b></td>
                        <td><b>Rolle</b></td>
                        <td><b>Nutzername</b></td>
                        <td><b>Passwort</b></td>
                    </tr>';

        foreach ($Reader as $Row)
            {
                // ------ FirstName
                
                $firstName = isset($Row[0]) ? $Row[0] : '';

                // ------ SecondName

                $secondName = isset($Row[1]) ? $Row[1] : '';

                // ------ Class

                if(isset($_POST['list-class'])){
                    $class = isset($Row[2]) ? $Row[2] : '';
                }
                else {
                    $class = $_POST['class'];
                }

                // ------ Role

                if(isset($_POST['list-class'])) {
                    if(isset($_POST['list-role'])){
                        $role = isset($Row[3]) ? $Row[3] : '';
                    } 
                    else {
                    $role = $_POST['role'];
                    }
                }
                else {
                    if(isset($_POST['list-role'])){
                        $role = isset($Row[2]) ? $Row[2] : '';
                    } 
                    else {
                        $role = $_POST['role'];
                    }
                }

                // ------ Username

                if(isset($_POST['list-class'])) {
                    if(isset($_POST['list-role'])){
                        $username = isset($Row[4]) ? $Row[4] : '';
                    } 
                    else {
                        $username = isset($Row[3]) ? $Row[3] : '';
                    }
                }
                else {
                    if(isset($_POST['list-role'])){
                        $username = isset($Row[3]) ? $Row[3] : '';
                    } 
                    else {
                        $username = isset($Row[2]) ? $Row[2] : '';
                    }
                }

                // ------ Password

                if(isset($_POST['list-class'])) {
                    if(isset($_POST['list-role'])){
                        if(!isset($_POST['list-auto-gen-pw'])){
                            $password = isset($Row[5]) ? $Row[5] : '';
                        }
                        else {$password = randomPassword();}
                    } 
                    else {
                        if(!isset($_POST['list-auto-gen-pw'])){
                            $password = isset($Row[4]) ? $Row[4] : '';
                        }
                        else {$password = randomPassword();}
                    }
                }
                else {
                    if(isset($_POST['list-role'])){
                        if(!isset($_POST['list-auto-gen-pw'])){
                            $password = isset($Row[4]) ? $Row[4] : '';
                        }
                        else {$password = randomPassword();}
                    } 
                    else {
                        if(!isset($_POST['list-auto-gen-pw'])){
                            $password = isset($Row[3]) ? $Row[3] : '';
                        }
                        else {$password = randomPassword();}
                    }
                }



                //Change alpha-num
          $username = str_replace("ö", "oe", $username);
          $username = str_replace("ä", "ae", $username);
          $username = str_replace("ü", "ue", $username);
          $username = str_replace("ß", "ss", $username);
          $username = str_replace("Ö", "Oe", $username);
          $username = str_replace("Ä", "Äe", $username);
          $username = str_replace("Ü", "Ue", $username);
          $username = str_replace("", "", $username);
          $username = str_replace("_", "", $username);
          $username = str_replace("-", "", $username);

          $spec_chars = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
          $username = strtr( $username, $spec_chars);   
           
          $html .= '
                      <tr><td>' . $secondName . '</td><td>' . $firstName . '</td><td>' . $class . '</td><td>' . $role . '</td><td>' . $username . '</td><td>' . $password . '</td></tr>
        ';
           
          $unencrypted_password = $password;
          $password = password_hash($unencrypted_password, PASSWORD_DEFAULT);

          // Create User Dir
          $user_dir = "files/users/" .  $username;
          $submission_dir = 'files/submissions/' . $username;
          $project_dir = 'files/projects/' . $username;

          // Insert in DB and create Folder
          if($db->createUser($firstName, $secondName, $class, $role, $username, $password, $user_dir, $log_user)) {
            if (!file_exists($user_dir)) {
                if(mkdir($user_dir, 0777, true)) {
                    if (!file_exists($submission_dir)) {
                        if(mkdir($submission_dir, 0777, true)) {
                            if (!file_exists($project_dir)) {
                                if(mkdir($project_dir, 0777, true)) {
                                    $success = true;
                                }
                                else {
                                    $db->delete_user_emergency($firstName, $secondName, $class, $role, $username, $password, $user_dir);
                                    echo'
                                    <div class="alert alert-danger" role="alert">
                                                                        <h4 class="alert-heading">Dateifehler</h4>
                                                                        <div class="alert-body">
                                                                        Benutzerordner konnte nicht erstellt werden. Vorgang abgebrochen. Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                                                        </div>
                                                                    </div>';
                                    sleep(3);
                                    header("Location: users");
                                }
                            }
                            else {
                                $db->delete_user_emergency($firstName, $secondName, $class, $role, $username, $password, $user_dir);
                                echo'
                                <div class="alert alert-danger" role="alert">
                                                                    <h4 class="alert-heading">Dateifehler</h4>
                                                                    <div class="alert-body">
                                                                    Benutzerordner konnte nicht erstellt werden. Vorgang abgebrochen. Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                                                    </div>
                                                                </div>';
                                sleep(3);
                                header("Location: users");
                            }
                        }
                        else {
                            $db->delete_user_emergency($firstName, $secondName, $class, $role, $username, $password, $user_dir);
                            echo'
                            <div class="alert alert-danger" role="alert">
                                                                <h4 class="alert-heading">Dateifehler</h4>
                                                                <div class="alert-body">
                                                                Benutzerordner konnte nicht erstellt werden. Vorgang abgebrochen. Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                                                </div>
                                                            </div>';
                            sleep(3);
                            header("Location: users");
                        }
                        }
                }
                else {
                    $db->delete_user_emergency($firstName, $secondName, $class, $role, $username, $password, $user_dir);
                    echo'
                    <div class="alert alert-danger" role="alert">
                                                        <h4 class="alert-heading">Datenbankfehler</h4>
                                                        <div class="alert-body">
                                                        Benutzer konnte nicht erstellt werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                                        </div>
                                                    </div>';
                    sleep(3);
                    header("Location: users");
                }
                }
            }
            else {
                echo'
                <div class="alert alert-danger" role="alert">
                                                    <h4 class="alert-heading">Datenbankfehler</h4>
                                                    <div class="alert-body">
                                                    Benutzer konnte nicht erstellt werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                                    </div>
                                                </div>';
            }


                
            }

            

        }

    if(isset($success)) {
        $html .= '                  </table>
            <br><br />';
             
            $html .= nl2br($request_footer);

             //////////////////////////// Erzeugung des PDF Dokuments \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
           
          // TCPDF Library laden
          require_once('src/tcpdf/tcpdf.php');
           
          // Erstellung des PDF Dokuments
          $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
           
          // Dokumenteninformationen
          $pdf->SetCreator(PDF_CREATOR);
          $pdfAuthor = "em CLOUDsolutions";
          $pdf->SetAuthor($pdfAuthor);
          $pdf->SetTitle("ide4school Mitgliederimport");
          $pdf->SetSubject("ide4school Mitgliederimport");
           
           
          // Header und Footer Informationen
          $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
          $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
           
          // Auswahl des Font
          $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
           
          // Auswahl der MArgins
          $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
          $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
          $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
           
          // Automatisches Autobreak der Seiten
          $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
           
          // Image Scale 
          $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
           
          // Schriftart
          $pdf->SetFont('dejavusans', '', 10);
           
          // Neue Seite
          $pdf->AddPage();
           
          // Fügt den HTML Code in das PDF Dokument ein
          $pdf->writeHTML($html, true, false, true, false, '');
           
          //Ausgabe der PDF
           
          //Variante 1: PDF direkt an den Benutzer senden:
          $pdf->Output($pdfName, 'I');
           
          //Variante 2: PDF im Verzeichnis abspeichern:
          //$pdf->Output(dirname(__FILE__).'/'.$pdfName, 'F');
          //echo 'PDF herunterladen: <a href="'.$pdfName.'">'.$pdfName.'</a>';
    }
    unlink($uploadFilePath);
  
};


    // Set Focus Mode offline
        if(isset($_GET["action"])) {
            {
            $action = $_GET["action"];
            $user_id = $_GET["user_id"];

            if($action == "setUserListenModeOffline") {
                if($db->setUserListenModeToOffline($user_id)) {
                  $return_to = $_SESSION["class_return_destination"];
                  header("Location: users&$return_to");
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
            if($action == "setUserListenModeOnline") {
              if($db->setUserListenModeToOnline($user_id)) {
                $return_to = $_SESSION["class_return_destination"];
                header("Location: users&$return_to");
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

    // Delete User
    if(isset($_POST["delete"])) {
        {
        $id = $_POST["id"];
        $username = $db->getUsernameViaID($id);
        $user_dir_for_delete = $db->getUserDir($id);
        $submission_dir_for_delete = 'files/submissions/' . $username;

        if($db->deleteUser($id)) {

                // Delete user folder
                  // -- Delete function
  function rec_rmdir ($unlink_path) {
    if (!is_dir ($unlink_path)) {
        return -1;
    }
    $dir = @opendir ($unlink_path);
    if (!$dir) {
        return -2;
    }
    while ($entry = @readdir($dir)) {
        if ($entry == '.' || $entry == '..') continue;
        if (is_dir ($unlink_path.'/'.$entry)) {
            $res = rec_rmdir ($unlink_path.'/'.$entry);
            if ($res == -1) {
                @closedir ($dir);
                return -2;
            } else if ($res == -2) {
                @closedir ($dir);
                return -2;
            } else if ($res == -3) {
                @closedir ($dir); 
                return -3; 
            } else if ($res != 0) {
                @closedir ($dir);
                return -2;
            }
        } else if (is_file ($unlink_path.'/'.$entry) || is_link ($unlink_path.'/'.$entry)) {
            $res = @unlink ($unlink_path.'/'.$entry);
            if (!$res) {
                @closedir ($dir);
                return -2;
            }
        } else {
            @closedir ($dir);
            return -3;
        }
    }
    @closedir ($dir);
    $res = @rmdir ($unlink_path);
    if (!$res) {
        return -2;
    }
    return 0;
}

    // -- Delete command 
    $unlink_path = $user_dir_for_delete;
    $res = rec_rmdir ($unlink_path);
    switch ($res) {
      case 0:
        $unlink_path = $submission_dir_for_delete;
        $res = rec_rmdir ($unlink_path);
        switch ($res) {
          case 0:
            header("users");
            break;
          case -1:
            echo('Das war kein Verzeichnis! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com');
            break;
          case -2:
            
            echo('Fehler beim Löschen des Verzeichnisses! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com');
            break;
          case -3:
           
            echo('Dieser Dateityp wird nicht unterstützt! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com');
            break;
          default:
            echo('Unbekannter Fehler! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com');
            break;
          }
        break;
      case -1:
        echo('Das war kein Verzeichnis! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com');
        break;
      case -2:
        
        echo('Fehler beim Löschen des Verzeichnisses! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com');
        break;
      case -3:
       
        echo('Dieser Dateityp wird nicht unterstützt! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com');
        break;
      default:
        echo('Unbekannter Fehler! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com');
        break;
      }


                // Redirect
                header("Location: users");
                
          }

        else {
            echo'
            <div class="alert alert-danger" role="alert">
                                                <h4 class="alert-heading">Datenbankfehler</h4>
                                                <div class="alert-body">
                                                Benutzer konnten nicht gelöscht werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                                </div>
                                            </div>';
              sleep(3);
             
                header("Location: users");
        }
    
        }
      }

// Logout User

if(isset($_POST["remoteLogout"]))
{
    $id = $_POST['user_id'];
    if($db->LogoutUser($id)) {
        header("Location: users");
            }
    else {
        echo'
                    <div class="alert alert-danger" role="alert">
                                                        <h4 class="alert-heading">Datenbankfehler</h4>
                                                        <div class="alert-body">
                                                       Benutzer konnte nicht abgemeldet werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                                        </div>
                                                    </div>';
        sleep(3);
        header("Location: users");
    }
};

if(isset($_POST["remoteLogoutClass"]))
{
    $class_name = $_POST['logout_class_name'];
    if($db->LogoutClass($class_name)) {
        header("Location: users");  
            }
    else {
        echo'
                    <div class="alert alert-danger" role="alert">
                                                        <h4 class="alert-heading">Datenbankfehler</h4>
                                                        <div class="alert-body">
                                                     Benutzer dieser Klasse konnten nicht abgemeldet werden! Bitte versuchen Sie es nocheinmal oder kontaktieren Sie support@ide4school.com
                                                        </div>
                                                    </div>';
        sleep(3);
        header("Location: users");
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
    <title>Benutzerliste - <?php echo $db->getCurrentInstitution() ?> - ide4school</title>
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

    function remoteLogout(user_id){
        if(confirm("Benutzer wirklich abmelden?")) {
        document.getElementById("user_id").value = user_id;
        document.getElementById("remoteLogoutForm").submit();
        }
    }

    function remoteLogoutClass(class_name){
        if(class_name == "Alle Klassen / Gruppen") {
            if(confirm("Benutzer aller Klassen und Gruppen wirklich abmelden?")) {
            document.getElementById("logout_class_name").value = class_name;
            document.getElementById("remoteLogoutClassForm").submit();
            }
        }   
        else {
            if(confirm("Benutzer der "+class_name+" wirklich abmelden?")) {
            document.getElementById("logout_class_name").value = class_name;
            document.getElementById("remoteLogoutClassForm").submit();
            }
        }
    }

</script>
<!-- Session class update hidden form -->
<form action="users" method="POST" id="updateSessionClassForm">
            <input name="class_name" type="text" hidden id="class_name">
            <input name="updateSessionClass" type="text" hidden id="updateSessionClass">
        </form>

 <!-- user logout hidden form -->

        <form action="users" method="post" id="remoteLogoutForm">
            <input name="user_id" type="text" hidden id="user_id">
            <input name="remoteLogout" type="text" hidden id="remoteLogout">
        </form>

        <form action="users" method="post" id="remoteLogoutClassForm">
            <input name="logout_class_name" type="text" hidden id="logout_class_name">
            <input name="remoteLogoutClass" type="text" hidden id="remoteLogoutClass">
        </form>


        <!-- //Manage Focus Modus -->
        <script>
  function setListenModeOffline(user_id){
    window.location.href="users&action=setUserListenModeOffline&user_id=" + user_id;
  }

  function setListenModeOnline(user_id){
    window.location.href="users&action=setUserListenModeOnline&user_id=" + user_id;
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
                <!-- users list start -->
                <section class="app-user-list">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <h3 class="fw-bolder mb-75"><?=$all_users_count?></h3>
                                        <span>Registrierte Nutzer</span>
                                    </div>
                                    <div class="avatar bg-light-primary p-50">
                                        <span class="avatar-content">
                                            <i data-feather="user" class="font-medium-4"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <h3 class="fw-bolder mb-75"><?=$active_users_count?></h3>
                                        <span>Angemeldete Nutzer</span>
                                    </div>
                                    <div class="avatar bg-light-success p-50">
                                        <span class="avatar-content">
                                            <i data-feather="user-check" class="font-medium-4"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <h3 class="fw-bolder mb-75"><?=$focus_users_count?></h3>
                                        <span>Nutzer mit Fokus Modus</span>
                                    </div>
                                    <div class="avatar bg-light-warning p-50">
                                        <span class="avatar-content">
                                            <i data-feather="user-x" class="font-medium-4"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="card">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <h3 class="fw-bolder mb-75"><?=$teachers_count?></h3>
                                        <span>Registrierte Lehrer</span>
                                    </div>
                                    <div class="avatar bg-light-danger p-50">
                                        <span class="avatar-content">
                                            <i data-feather="user-plus" class="font-medium-4"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- list and filter start -->
                    <div class="card">
                    <div class="card-body border-bottom">
    <h4 class="card-title">Benutzerliste</h4>
    <div class="row">

        <div class="col-md-4 user_status">
            <label class="form-label" for="FilterTransaction">Filtern nach Klassenstufe</label>
            <button
                        type="button"
                        class="form-select text-capitalize mb-md-0 mb-2xx"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                      >
                        <?php echo $selected_class ?>
                      </button> 
                      <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="users">Alle Klassen / Gruppen</a></li>
                      <?php foreach($classes as $class) {
                        echo '<li><a class="dropdown-item" href="users&class=' . $class['name'] . '">' . $class['name'] . '</a></li>';
                        }?>
                      </ul>
        </div>
        <div class="col-sm-12 col-lg-8 ps-xl-75 ps-0">
                        <?php
                        if($db->isAdmin()) {
                           ?>
                <button style="float: right;" class="dt-button add-new btn btn-primary" tabindex="0" aria-controls="DataTables_Table_0" type="button" data-bs-toggle="modal" data-bs-target="#modals-slide-in"><span>Benutzer hinzufügen</span></button> 
                <button style="float: right; margin-right: 15px;" class="dt-button add-new btn btn-primary" tabindex="0" aria-controls="DataTables_Table_0" type="button" data-bs-toggle="modal" data-bs-target="#import-users"><span>Benutzerliste importieren</span></button> 
                <button style="float: right; margin-right: 15px;" class="dt-button add-new btn btn-primary" tabindex="0" aria-controls="DataTables_Table_0" type="button" OnClick="remoteLogoutClass('<?=$selected_class?>')"><span>Benutzer abmelden</span></button> 
                            <?php
                        }
                        ?>
        </div>
     </div>
</div>
                       
<br />
                            <table class="user-list-table table">
                                <thead class="table-light">
                                    <tr>
                                      
                                        <th>Name</th>
                                        <th>Klasse</th>
                                        <th>Rolle</th>
                                        <th>Fokus Modus</th>
                                        <th>Status</th>
                                        <th>Aktionen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($users as $user) {
                                    echo '
                                <tr class="odd">
    <td class=" control" tabindex="0" style="display: none;"></td>
    <td class="sorting_1">
        <div class="d-flex justify-content-left align-items-center">
            <div class="avatar-wrapper">
                <div class="avatar  me-1"><a href="user&id=' . $user['id'] . '" ><img src="app-assets/images/avatars/'.$db->getUserAvatar($user['id']).'.png" alt="Avatar" height="32" width="32"></a></div>
            </div>
            <div class="d-flex flex-column"><a href="user&id=' . $user['id'] . '" class="user_name text-truncate text-body"><span class="fw-bolder">' . $user['secondName'] . ', ' . $user['firstName'] . '</span></a><small class="emp_post text-muted">@' . $user['username'] . '</small></div>
        </div>
    </td>
    <td>' . $user['class'] . '</td>';
    echo '<td>';
    if($user['role'] == "Administrator") {
        echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings font-medium-3 text-warning me-50"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
        Administrator
        ';
    }
     
    if($user['role'] == "Lehrer") {
        echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 font-medium-3 text-info me-50"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
        Lehrer
        ';
    }

    if($user['role'] == "Schüler") {
        echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user font-medium-3 text-primary me-50"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        Schüler
        ';
    }
    echo '</td>';
    
    if($user['focus_mode'] == "1") {
    echo '<td><span class="badge rounded-pill badge-light-warning" text-capitalized="">Aktiv</span></td>';
    }
    else {
    echo '<td><span class="badge rounded-pill badge-light-danger" text-capitalized="">Inaktiv</span></td>';
    }

    
    if($user['status'] == "1") {
    echo '<td><span class="badge rounded-pill badge-light-success" text-capitalized="">Online</span></td>';
    }
    else {
    echo '<td><span class="badge rounded-pill badge-light-secondary" text-capitalized="">Offline</span></td>';
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
                if($user['focus_mode'] == "1") {
                    echo '<a onclick=setListenModeOffline(' . $user['id'] . ') class="dropdown-item delete-record">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
             </svg>
             Fokus Modus deaktivieren
             </a>
                ';
                    }
                    else {
                        echo '<a onclick=setListenModeOnline(' . $user['id'] . ') class="dropdown-item delete-record">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                         </svg>
                         Fokus Modus aktivieren
                         </a>
                            ';
                    }

            echo '
            <a href="user&id=' . $user['id'] . '" class="dropdown-item delete-record">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-info">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                Benutzerinformationen
            </a>
            <a href="javascript:;" OnClick="remoteLogout(' . $user['id'] . ')" class="dropdown-item delete-record">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                  Benutzer abmelden
            </a>';

                        if($db->isAdmin()) {
                            echo'
            <a onclick=deleteUser(' . $user['id'] . ') class="dropdown-item delete-record">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
                Benutzer löschen
            </a>'; }
            echo '
        </div>
    </div>
</td>
</tr>';
} ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if($db->isAdmin()) {?>
                        <!-- Modal to add new user starts-->
                        <div class="modal modal-slide-in new-user-modal fade" id="modals-slide-in">
                            <div class="modal-dialog">
                                <form class="add-new-user modal-content pt-0" action="users" method="POST">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                                    <div class="modal-header mb-1">
                                        <h5 class="modal-title" id="exampleModalLabel">Benutzer hinzufügen</h5>
                                    </div>
                                    <div class="modal-body flex-grow-1">
                                        <div class="mb-1">
                                            <label class="form-label" for="basic-icon-default-fullname">Vorname</label>
                                            <input type="text" class="form-control dt-full-name" id="basic-icon-default-fullname" placeholder="Max" name="user-firstName" />
                                        </div>
                                        <div class="mb-1">
                                            <label class="form-label" for="basic-icon-default-fullname">Nachname</label>
                                            <input type="text" class="form-control dt-full-name" id="basic-icon-default-fullname" placeholder="Mustermann" name="user-secondName" />
                                        </div>
                                        <div class="mb-1">
                                        <div class="mb-1">
                                        <label class="form-label" for="basicSelect">Klasse</label>
                                        <select class="form-select" id="basicSelect" name="user-class">
                                        <option value=""> Klasse auswählen</option>
                                            <?php
                                            foreach($classes as $class) {
                                            echo '<option value="' . $class['name'] . '" class="text-capitalize">' . $class['name'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                        </div>
                                        <div class="mb-1">
                                        <label class="form-label" for="place">Rolle</label>
                                        <input type="hidden" id="place" hidden><br />
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="user-role" id="inlineRadio1" value="Schüler">
                                            <label class="form-check-label" for="inlineRadio1">Schüler</label>
                                            <br />
                                            <input class="form-check-input" type="radio" name="user-role" id="inlineRadio2" value="Lehrer">
                                            <label class="form-check-label" for="inlineRadio2">Lehrer</label>
                                            <br />
                                            <input class="form-check-input" type="radio" name="user-role" id="inlineRadio3" value="Administrator">
                                            <label class="form-check-label" for="inlineRadio3">Administrator</label>
                                        </div>
                                        </div>
                                        <div class="mb-1">
                                            <label class="form-label" for="basic-icon-default-fullname">Benutzername</label>
                                            <input type="text" class="form-control dt-full-name" id="basic-icon-default-fullname" placeholder="MMustermann" name="user-username" />
                                        </div>
                                        
                                        <div class="mb-1">
                                            <label class="form-label" for="basic-icon-default-contact">Passwort</label>
                                            <input type="text" id="user-password" class="form-control dt-contact" placeholder="**************" name="user-password" />
                                            <b><a OnClick="genPassword()">Passwort generieren</a></b>
                                            <b style="float: right;"><a OnClick="copyPassword()">Kopieren</a></b>
                                        </div>
                                        <br />
                                        
                                        <button type="submit" class="btn btn-primary me-1 data-submit" name="createUser">Benutzer hinzufügen</button>
                                        <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">Abbrechen</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Modal to add new user Ends-->

                         <!-- Modal to import new users starts-->
                         <div class="modal modal-slide-in new-user-modal fade" id="import-users">
                            <div class="modal-dialog">
                                <form class="add-new-user modal-content pt-0" action="users" method="POST" enctype="multipart/form-data">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
                                    <div class="modal-header mb-1">
                                        <h5 class="modal-title" id="exampleModalLabel">Benutzerliste importieren</h5>
                                    </div>
                                    <div class="modal-body flex-grow-1">
                                    
                                            <div class="mb-1">
                                                <h5>Datei Informationen</h5>
                                            </div>
                                        <div class="mb-1">
                                        <p>Die Datei muss im XLSX Excel-Format vorliegen! Nur Dateien mit dieser Dateiendung werden ordnungsgemäß importiert!</p>
                                        <h6>Die Spalten müssen folgende Anordnung haben:</h6>
                                        <p>
                                            1. Spalte: Vorname<br />
                                            2. Spalte: Nachnahme<br />
                                            3. Splate: ggf. Klasse<br />
                                            4. Spalte: ggf. Rolle<br />
                                            5. Spalte: Benutzername<br />
                                            6. Spalte: ggf. Passwort<br />
                                        </p>
                                        <p style="color: red;">Die Einträge in der Liste müssen sofort mit den Benutzerdaten starten! Überschriften wie "Name", "Klasse" oder "Passwort" werden ansonsten als neue Benutzer angelegt! <br /><br /> Beim abwählen bestimmter Spalten rutschen die folgenden automatisch um die Anzahl der abgewählten Spalten vor!</p>
                                        

                                        
                                        </div><div class="accordion" id="accordionExample" data-toggle-hover="true">
                                        <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingOne">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordionOne" aria-expanded="false" aria-controls="accordionOne">
                                                        Spaltenauswahl
                                                    </button>
                                                </h2>
                                                <div id="accordionOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample" style="">
                                                    <div class="accordion-body">
                                        <div class="mb-1">
                                        <label class="form-label" for="place">In der Datei müssen folgende Spalten vorhanden sein:</label><br />
                                        <input type="hidden" id="place" hidden><br />
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="list-firstName" id="inlineRadio1" checked disabled>
                                            <label class="form-check-label" for="inlineRadio1">Vorname</label>
                                            <br />
                                            <input class="form-check-input" type="checkbox" name="list-secondName" id="inlineRadio1" checked disabled>
                                            <label class="form-check-label" for="inlineRadio1">Nachname</label>
                                            <br />
                                            <input class="form-check-input" type="checkbox" name="list-username" id="inlineRadio1" checked disabled>
                                            <label class="form-check-label" for="inlineRadio1">Benutzername</label>
                                            <br />
                                        </div>
                                        </div><br />
                                        <div class="mb-1">
                                        <p>In der Datei könnten folgende Spalten zusätzlich vorhanden sein (Sind diese nicht vorhanden sind sie in der "Benutzer Voreinstellung" anzugeben):</p>
                                        <input type="hidden" id="place" hidden><br />
                                        <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="list-class" id="inlineRadio1" checked>
                                            <label class="form-check-label" for="inlineRadio1">Klasse</label>
                                            <br />
                                            <input class="form-check-input" type="checkbox" name="list-role" id="inlineRadio1" checked>
                                            <label class="form-check-label" for="inlineRadio1">Rolle</label>
                                            <br />
                                            <input class="form-check-input" type="checkbox" name="list-password" id="inlineRadio1" checked>
                                            <label class="form-check-label" for="inlineRadio1">Passwort</label>
                                            <br /><br />
                                            
                                        </div>
                                        </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingTwo">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordionTwo" aria-expanded="false" aria-controls="accordionOne">
                                                        Benutzer Voreinstellung
                                                    </button>
                                                </h2>
                                                <div id="accordionTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample" style="">
                                                    <div class="accordion-body">
                                            <div class="mb-1">
                                            <p style="color: red;">ide4school kann Startpasswörter automatisch generieren (Anschließende Auflistung in einer Tabelle)</p>
                                            <input class="form-check-input" type="checkbox" name="list-auto-gen-pw" id="inlineRadio1">
                                            <label class="form-check-label" for="inlineRadio1">Startpasswörter automatisch erstellen (Passwörter in der Datei werden ignoriert)</label>
                                            <br />
                                            </div>
                                        <div class="mb-1">
                                        <div class="mb-1">
                                        <label class="form-label" for="basicSelect">Klasse</label>
                                        <select class="form-select" id="basicSelect" name="class">
                                        <option value=""> Klasse wählen</option>
                                            <?php
                                            foreach($classes as $class) {
                                            echo '<option value="' . $class['name'] . '" class="text-capitalize">' . $class['name'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
        
                                        </div>
                                        <div class="mb-1">
                                        <label class="form-label" for="place">Rolle</label>
                                        <input type="hidden" id="place" hidden><br />
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="role" id="inlineRadio1" value="Schüler">
                                            <label class="form-check-label" for="inlineRadio1">Schüler</label>
                                            <br />
                                            <input class="form-check-input" type="radio" name="role" id="inlineRadio2" value="Lehrer">
                                            <label class="form-check-label" for="inlineRadio2">Lehrer</label>
                                            <br />
                                            <input class="form-check-input" type="radio" name="role" id="inlineRadio3" value="Administrator">
                                            <label class="form-check-label" for="inlineRadio3">Administrator</label>
                                        </div>
                                        
                                        </div><br />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        <br />
                                        <div class="mb-1">
                                                <h5>Liste auswählen</h5>
                                            </div>
                                            <div class="mb-1">
                                            <label for="formFile" class="form-label">Liste hochladen</label>
                                            <input class="form-control" type="file" id="formFile" name="list-file">
                                            <p style="color: red;">Erlaubter Dateityp: .xlsx</p>

                                        </div>
                                            
                                        <br />
                                        
                                        <button type="submit" class="btn btn-primary me-1 data-submit" name="importUsers">Import starten</button>
                                        <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal">Abbrechen</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Modal to import new users Ends-->

                        <?php } ?>
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

    <script>
        
// Generate Password for User Registration
function genPassword() {
   var chars = "0123456789abcdefghijklmnopqrstuvwxyz!@#$%^&*()ABCDEFGHIJKLMNOPQRSTUVWXYZ";
   var passwordLength = 12;
   var password = "";
for (var i = 0; i <= passwordLength; i++) {
  var randomNumber = Math.floor(Math.random() * chars.length);
  password += chars.substring(randomNumber, randomNumber +1);
 }
       document.getElementById("user-password").value = password;
}

// Copy entered Password

function copyPassword() {
  var copyText = document.getElementById("user-password");
  copyText.select();
  document.execCommand("copy");  
}
        </script>
            <?php include('inc/components/createEnviroment.php'); ?>
            <?php include('inc/components/createProject.php'); ?>

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