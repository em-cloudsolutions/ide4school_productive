<?php
// Prüfen, ob das Formular gesendet wurde
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (isset($data['install'])) {
    // Überprüfen Sie die Formularfelder auf Gültigkeit
        $db_host = trim($data['db_host']);
        $db_name = trim($data['db_name']);
        $db_user = trim($data['db_user']);
        $db_pass = trim($data['db_password']);
        $school_name = trim($data['school_name']);
        $school_id = trim($data['school_id']);
        $admin_first_name = trim($data['admin_first_name']);
        $admin_second_name = trim($data['admin_second_name']);
        $admin_password = trim($data['admin_password']);
        $username = trim($data['admin_username']);


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
        // Weitere Formularfelder verarbeiten

       
        if (empty($db_host) || empty($db_name) || empty($db_user) || empty($username) || empty($admin_password) || empty($school_name) || empty($school_id) || empty($admin_first_name) || empty($admin_second_name)) {
            die('Bitte füllen Sie alle erforderlichen Felder aus');
        }
       

        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die('Verbindung zur Datenbank fehlgeschlagen: ' . $e->getMessage());
        }

        $curl_handle = curl_init();
        $url = "https://dev.em-cloud-solutions.de/ide4school-latest.zip";
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        

        $zip_contents = curl_exec($curl_handle);
        if (curl_errno($curl_handle)) {
            die('Konnte ZIP-Archiv nicht herunterladen: ' . curl_error($curl_handle));
        }
        $zip_file = 'app.zip';
        file_put_contents($zip_file, $zip_contents);


        $zip = new ZipArchive;
        if ($zip->open($zip_file) !== TRUE) {
            die('Konnte das ZIP-Archiv nicht öffnen');
        }
        $zip->extractTo('./');
        $zip->close();
        unlink($zip_file);



        $sql_file = 'sql_data.sql';
        $sql_contents = file_get_contents($sql_file);
        $pdo->exec($sql_contents);
        unlink($sql_file);


        $admin_hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (firstName, secondName, username, password, role, class, institution, user_dir) VALUES(:firstName, :secondName, :username, :password, :role, :class, :institution, :user_dir)";
        $stmt = $pdo->prepare($sql);
        $user_dir = "files/users/" .  $username;
        $role = "Administrator";
        $class = "Lehrer";
        $stmt->bindParam(":secondName", $secondName);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":class", $class);
        $stmt->bindParam(":user_dir", $user_dir);
        $stmt->bindParam(':institution', $school_name);
        $stmt->bindParam(':firstName', $admin_first_name);
        $stmt->bindParam(':secondName', $admin_second_name);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $admin_hashed_password);
        // Weitere Formularfelder hinzufügen
        $stmt->execute();

        $user_dir = "files/users/" .  $username;
        $submission_dir = 'files/submissions/' . $username;
        mkdir($user_dir, 0777, true);
        mkdir($submission_dir, 0777, true);

        if (isset($data['connect_repo']) && $data['connect_repo'] == 'on') {
          $connect_repo = 1;

          // Pfad zur index.php-Datei ermitteln
          $scriptPath = $_SERVER['PHP_SELF'];
          $scriptDirectory = pathinfo($scriptPath, PATHINFO_DIRNAME);
          $baseURL = $scriptDirectory . '/index.php';

          // Daten in eine base64-kodierte Zeichenkette umwandeln
          $encodedData = base64_encode($school_name . ',' . $baseURL . ',' . $school_id);

          // URL des Skripts, das die Daten empfängt
          $targetUrl = 'https://dev.em-cloud-solutions.de/connectmanager.php';

          // GET-Anfrage senden
          $url = $targetUrl . '?data=' . urlencode($encodedData);
          $response = file_get_contents($url);
          if ($response === FALSE) {
              echo "Fehler beim Senden der Daten.";
          } else {
              echo "Daten erfolgreich gesendet.";
          }

        }
        else {
          $connect_repo = 0;
        }
        $sql3 = "INSERT INTO settings (connect_repo, institution_name) VALUES(:connect_repo, :institution_name)";
        $stmt1 = $pdo->prepare($sql3);
        $stmt1->bindParam(":connect_repo", $connect_repo);
        $stmt1->bindParam(":institution_name", $school_name);
        $stmt1->execute();

        if (isset($data['remember_me']) && $data['remember_me'] == 'on') {
          $sql1 = "SELECT id FROM users WHERE firstName=:firstName AND secondName=:secondName AND username=:username";
          $user_id_db = $pdo->prepare($sql1);
          $user_id_db->bindParam(":firstName", $admin_first_name);
          $user_id_db->bindParam(":secondName", $admin_second_name);
          $user_id_db->bindParam(":username", $username);
          $user_id_db->execute();
          $result = $user_id_db->fetch(PDO::FETCH_OBJ);

            $long_term_key = bin2hex(random_bytes(32));
            $sql2 = "INSERT INTO user_tokens (token, user_id) VALUES(:token, :user_id)";
            $user_token_db = $pdo->prepare($sql2);
            $user_token_db->bindParam(":token", $long_term_key);
            $user_token_db->bindParam(":user_id", $result->id);
            if($user_token_db->execute()) {
                $day = 30;
                $expired_seconds = time() + 60 * 60 * 24 * $day;
                // Setze den langfristigen Sitzungsschlüssel als Cookie (mit HttpOnly-Flag)
                setcookie('remember_me', $long_term_key, $expired_seconds);
            }
        } 
         
        $configFileContent = "<?php\n";
        $configFileContent .= "define('DB_USERNAME', '" . $db_user . "');\n";
        $configFileContent .= "define('DB_PASSWORD', '" . $db_pass . "');\n";
        $configFileContent .= "define('DB_HOST', '" . $db_host . "');\n";
        $configFileContent .= "define('DB_NAME', '" . $db_name . "');\n";
        $configFileContent .= "?>";

        $configFile = fopen("core/internal/database_config.php", "w") or die("Konfigurationsdatei konnte nicht erstellt werden!");
        fwrite($configFile, $configFileContent);
        fclose($configFile);

        $response = array('success' => true, 'redirect' => 'index.php');
        echo json_encode($response);
        exit();
}
?>
<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="author" content="PIXINVENT, Elias Müller">
    <title>ide4school installieren</title>
    <link rel="shortcut icon" type="image/x-icon" href="../app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="../app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/vendors/css/forms/wizard/bs-stepper.min.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/vendors/css/forms/select/select2.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="../app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/css/themes/bordered-layout.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/css/themes/semi-dark-layout.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="../app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/css/plugins/forms/form-wizard.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/css/plugins/forms/form-validation.css">
    <link rel="stylesheet" type="text/css" href="../app-assets/css/pages/authentication.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <!-- END: Custom CSS-->

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="blank-page">
    <!-- BEGIN: Content-->
    <script>
      function getInstitutionName() {
        x = document.getElementById('school_name').value;
        document.getElementById('finalSchoolName').innerHTML = x;
      }

      function getInstitutionID() {
        x = document.getElementById('school_id').value;
        document.getElementById('finalSchoolID').innerHTML = x;
      }

      function getConnectRepo() {
        x = document.getElementById('connect_repo').checked;
        if(x=='1') {
          document.getElementById('finalConnectRepo').innerHTML = "Ja";
        }
        if(x=='0') {
          document.getElementById('finalConnectRepo').innerHTML = "Nein";
        }

      }

      function getDatabaseHost() {
        x = document.getElementById('db_host').value;
        document.getElementById('finalDatabaseHost').innerHTML = x;
      }

      function getDatabaseName() {
        x = document.getElementById('db_name').value;
        document.getElementById('finalDatabaseName').innerHTML = x;
      }

      function getDatabaseUser() {
        x = document.getElementById('db_user').value;
        document.getElementById('finalDatabaseUser').innerHTML = x;
      }

      function getDatabasePassword() {
        document.getElementById('finalDatabasePassword').innerHTML = "******************";
      }

      function getAdminFirstName() {
        x = document.getElementById('admin_first_name').value;
        document.getElementById('finalAdminFirstName').innerHTML = x;
      }

      function getAdminSecondName() {
        x = document.getElementById('admin_second_name').value;
        document.getElementById('finalAdminSecondName').innerHTML = x;
      }

      function getAdminUsername() {
        x = document.getElementById('admin_username').value;
        document.getElementById('finalAdminUsername').innerHTML = x;
      }

      function getAdminPassword() {
        document.getElementById('finalAdminPassword').innerHTML = "******************";
      }
    </script>
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <div class="auth-wrapper auth-cover">
                    <div class="auth-inner row m-0">
                        <!-- Brand logo-->
                        <a class="brand-logo" href="index.html">
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
                        <div class="col-lg-3 d-none d-lg-flex align-items-center p-0">
                            <div class="w-100 d-lg-flex align-items-center justify-content-center">
                                <img class="img-fluid w-100" src="../app-assets/images/illustration/create-account.svg" alt="multi-steps" />
                            </div>
                        </div>
                        <!-- /Left Text-->

                        <!-- Register-->
                        <div class="col-lg-9 d-flex align-items-center auth-bg px-2 px-sm-3 px-lg-5 pt-3">
                            <div class="width-900 mx-auto">
                                <div class="bs-stepper register-multi-steps-wizard shadow-none">
                                    <div class="bs-stepper-header px-0" role="tablist">
                                      <div class="step" data-target="#school-data" role="tab" id="school-data-trigger">
                                            <button type="button" class="step-trigger">
                                                <span class="bs-stepper-box">
                                                    <i data-feather="home" class="font-medium-3"></i>
                                                </span>
                                                <span class="bs-stepper-label">
                                                    <span class="bs-stepper-title">Schuldaten</span>
                                                    <span class="bs-stepper-subtitle">Schuldaten eingeben</span>
                                                </span>
                                            </button>
                                      </div>
                                      <div class="line">
                                            <i data-feather="chevron-right" class="font-medium-2"></i>
                                        </div>
                                        <div class="step" data-target="#account-details" role="tab" id="account-details-trigger">
                                            <button type="button" class="step-trigger">
                                                <span class="bs-stepper-box">
                                                    <i data-feather="server" class="font-medium-3"></i>
                                                </span>
                                                <span class="bs-stepper-label">
                                                    <span class="bs-stepper-title">Datenbank</span>
                                                    <span class="bs-stepper-subtitle">Zugangsdaten eingeben</span>
                                                </span>
                                            </button>
                                        </div>
                                        <div class="line">
                                            <i data-feather="chevron-right" class="font-medium-2"></i>
                                        </div>
                                        <div class="step" data-target="#personal-info" role="tab" id="personal-info-trigger">
                                            <button type="button" class="step-trigger">
                                                <span class="bs-stepper-box">
                                                    <i data-feather="user" class="font-medium-3"></i>
                                                </span>
                                                <span class="bs-stepper-label">
                                                    <span class="bs-stepper-title">Administration</span>
                                                    <span class="bs-stepper-subtitle">Konto anlegen</span>
                                                </span>
                                            </button>
                                        </div>
                                        <div class="line">
                                            <i data-feather="chevron-right" class="font-medium-2"></i>
                                        </div>
                                        <div class="step" data-target="#billing" role="tab" id="billing-trigger">
                                            <button type="button" class="step-trigger">
                                                <span class="bs-stepper-box">
                                                    <i data-feather="info" class="font-medium-3"></i>
                                                </span>
                                                <span class="bs-stepper-label">
                                                    <span class="bs-stepper-title">Überprüfen</span>
                                                    <span class="bs-stepper-subtitle">Installation starten</span>
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="bs-stepper-content px-0 mt-4">
                                    <div id="school-data" class="content" role="tabpanel" aria-labelledby="school-data-trigger">
                                            <div class="content-header mb-2">
                                                <h2 class="fw-bolder mb-75">Schulinformationen</h2>
                                                <span>Bitte gib die Informationen für Ihre Schule ein.</span>
                                            </div>
                                            <form>
                                                <div class="row">
                                                    <div class="col-md-6 mb-1">
                                                        <label class="form-label" for="school_name">Name Ihrer Institution</label>
                                                        <input type="text" OnChange="getInstitutionName()" name="school_name" id="school_name" class="form-control" placeholder="Der Name Ihrer Schule" />
                                                    </div>
                                                    <div class="col-md-6 mb-1">
                                                        <label class="form-label" for="school_id">Schul-Kürzel</label> <span data-bs-toggle="tooltip" data-bs-placement="top" title data-bs-original-title=' (meist eine Kombination der Anfangsbuchstaben - z.B. "tmgb" für "Timon-Manu Gymnasium Berlin")'><i data-feather="info" class="font-medium-1"></i></span>
                                                        <input type="text" OnChange="getInstitutionID()" name="school_id" id="school_id" class="form-control" placeholder="Der Name Ihrer Datenbank" />
                                                    </div>
                                                </div>
                                                <div class="demo-inline-spacing">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" OnChange="getConnectRepo()" type="checkbox" id="connect_repo" name="connect_repo" value="checked" checked="">
                                            <label class="form-check-label" for="connect_repo">Diese Institution im Connect-Repository anmelden.</label> <span data-bs-toggle="tooltip" data-bs-placement="top" title data-bs-original-title='Das Connect-Repository bietet die Möglichkeit, sich über die offizielle ide4school Webseite mit ihrerem Schul-Kürzel an Ihrer Institution anzumelden. So können Sie ide4school auf Ihrem eigenen Server hosten, sich aber auch über die offzielle Webseite anmelden. Dies ist für Schüler und Lehrer eventuell einfacher zu benutzen. Diese Einstellung kann jederzeit in den Einstellungen geändert werden.'><i data-feather="info" class="font-medium-1"></i></span>
                                        </div>
                                    </div>
                                            </form>

                                            <div class="d-flex justify-content-between mt-2">
                                                <button class="btn btn-primary btn-next">
                                                    <span class="align-middle d-sm-inline-block d-none">Weiter</span>
                                                    <i data-feather="chevron-right" class="align-middle ms-sm-25 ms-0"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="account-details" class="content" role="tabpanel" aria-labelledby="account-details-trigger">
                                            <div class="content-header mb-2">
                                                <h2 class="fw-bolder mb-75">Datenbankinformationen</h2>
                                                <span>Bitte gib die Zugangsdaten für die Datenbank ein.</span>
                                            </div>
                                            <form>
                                                <div class="row">
                                                    <div class="col-md-6 mb-1">
                                                        <label class="form-label" for="db_host">Datenbank Server</label>
                                                        <input type="text" OnChange="getDatabaseHost()" name="db_host" id="db_host" class="form-control" placeholder="Der Server Ihrer Datenbank" />
                                                    </div>
                                                    <div class="col-md-6 mb-1">
                                                        <label class="form-label" for="db_name">Datenbank Name</label>
                                                        <input type="text"  OnChange="getDatabaseName()" name="db_name" id="db_name" class="form-control" placeholder="Der Name Ihrer Datenbank" />
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-1">
                                                        <label class="form-label" for="db_user">Datenbank Benutzer</label>
                                                        <div class="input-group input-group-merge form-password-toggle">
                                                            <input type="text" OnChange="getDatabaseUser()" name="db_user" id="db_user" class="form-control" placeholder="Der Benutzer Ihrer Datenbank" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-1">
                                                        <label class="form-label" for="db_password">Datenbank Passwort</label>
                                                        <div class="input-group input-group-merge form-password-toggle">
                                                            <input type="password" OnChange="getDatabasePassword()" name="db_password" id="db_password" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                                                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>

                                            <div class="d-flex justify-content-between mt-2">
                                            <button class="btn btn-primary btn-prev">
                                                    <i data-feather="chevron-left" class="align-middle me-sm-25 me-0"></i>
                                                    <span class="align-middle d-sm-inline-block d-none">Zurück</span>
                                                </button>
                                                <button class="btn btn-primary btn-next">
                                                    <span class="align-middle d-sm-inline-block d-none">Weiter</span>
                                                    <i data-feather="chevron-right" class="align-middle ms-sm-25 ms-0"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="personal-info" class="content" role="tabpanel" aria-labelledby="personal-info-trigger">
                                            <div class="content-header mb-2">
                                                <h2 class="fw-bolder mb-75">Administratorkonto</h2>
                                                <span>Geben Sie jetzt die gewünschten Anmeldeinformationen für das Adminstratorkonto ein.</span>
                                            </div>
                                            <form>
                                                <div class="row">
                                                    <div class="mb-1 col-md-6">
                                                        <label class="form-label" for="admin_first_name">Vorname</label>
                                                        <input type="text" name="admin_first_name" OnChange="getAdminFirstName()" id="admin_first_name" class="form-control" placeholder="Max" />
                                                    </div>
                                                    <div class="mb-1 col-md-6">
                                                        <label class="form-label" for="admin_second_name">Nachname</label>
                                                        <input type="text" name="admin_second_name" OnChange="getAdminSecondName()" id="admin_second_name" class="form-control" placeholder="Mustermann" />
                                                    </div>  
                                                </div>
                                                
                                                <div class="row">
                                                <div class="mb-1 col-md-6">
                                                        <label class="form-label" for="admin_username">Benutzername<br />(erster Buchstabe des Vornames + Nachname, ohne Umlaute und Sonderzeichen. z.B. MMustermann)</label>
                                                        <input type="text" name="admin_username" OnChange="getAdminUsername()" id="admin_username" class="form-control" placeholder="Mustermann" />
                                                    </div>
                                                    <div class="col-md-6 mb-1">
                                                        <label class="form-label" for="admin_password">Passwort (min. 8 Zeichen)<br /><i>Groß-/Kleinbuchstaben, Zahlen & Sonderzeichen</i></label>
                                                        <div class="input-group input-group-merge form-password-toggle">
                                                            <input type="password" name="admin_password" OnChange="getAdminPassword()" id="admin_password" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                                                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-12 mb-1">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="remember_me" name="remember_me" id="remember_me" />
                                                            <label class="form-check-label" for="remember_me">Angemeldet bleiben</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>

                                            <div class="d-flex justify-content-between mt-2">
                                                <button class="btn btn-primary btn-prev">
                                                    <i data-feather="chevron-left" class="align-middle me-sm-25 me-0"></i>
                                                    <span class="align-middle d-sm-inline-block d-none">Zurück</span>
                                                </button>
                                                <button class="btn btn-primary btn-next">
                                                    <span class="align-middle d-sm-inline-block d-none">Weiter</span>
                                                    <i data-feather="chevron-right" class="align-middle ms-sm-25 ms-0"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="billing" class="content" role="tabpanel" aria-labelledby="billing-trigger">
                                        <div class="content-header mb-2">
                                                <h2 class="fw-bolder mb-75">Installationszusammenfassung:</h2>
                                                <span>Überprüfen Sie nocheinmal alle eingegebenen Informationen und starten Sie die Installation.</span>
                                            </div>
                                            
                                                <div class="row">
                                                          
                                                              <dl class="row">
                                                                  <dt class="col-sm-3">Name der Institution:</dt>
                                                                  <dd class="col-sm-9" id="finalSchoolName">NOCH NICHT GESETZT</dd>
                                                              </dl>
                                                              <dl class="row">
                                                                  <dt class="col-sm-3">Schul-Kürzel:</dt>
                                                                  <dd class="col-sm-9" id="finalSchoolID">NOCH NICHT GESETZT</dd>
                                                              </dl>
                                                              <dl class="row">
                                                                  <dt class="col-sm-3">In Connect-Repository eintragen?</dt>
                                                                  <dd class="col-sm-9" id="finalConnectRepo">Ja</dd>
                                                              </dl>
                                                              <dl class="row">
                                                                  <dt></dt>
                                                                  <dd></dd>
                                                              </dl>
                                                              <div class="mb-1 col-md-6">
                                                              <dl class="row">
                                                                  <dt class="col-sm-3">Datenbank Server:</dt>
                                                                  <dd class="col-sm-9" id="finalDatabaseHost">NOCH NICHT GESETZT</dd>
                                                              </dl>
                                                              <dl class="row">
                                                                  <dt class="col-sm-3">Datenbank Name:</dt>
                                                                  <dd class="col-sm-9" id="finalDatabaseName">NOCH NICHT GESETZT</dd>
                                                              </dl>
                                                              <dl class="row">
                                                                  <dt class="col-sm-3">Datenbank Benutzer:</dt>
                                                                  <dd class="col-sm-9" id="finalDatabaseUser">NOCH NICHT GESETZT</dd>
                                                              </dl>
                                                              <dl class="row">
                                                                  <dt class="col-sm-3">Datenbank Passwort:</dt>
                                                                  <dd class="col-sm-9" id="finalDatabasePassword">NOCH NICHT GESETZT</dd>
                                                              </dl>
                                                    </div>
                                                    <div class="mb-2 col-md-6">
                                                      <dl class="row">
                                                                    <dt class="col-sm-3">Vorname des Administrators:</dt>
                                                                    <dd class="col-sm-9" id="finalAdminFirstName">NOCH NICHT GESETZT</dd>
                                                                </dl>
                                                                <dl class="row">
                                                                <dt class="col-sm-3">Nachname des Administrators:</dt>
                                                                    <dd class="col-sm-9" id="finalAdminSecondName">NOCH NICHT GESETZT</dd>
                                                                </dl>
                                                                <dl class="row">
                                                                <dt class="col-sm-3">Benutzername des Administrators:</dt>
                                                                    <dd class="col-sm-9" id="finalAdminUsername">NOCH NICHT GESETZT</dd>
                                                                </dl>
                                                                <dl class="row">
                                                                <dt class="col-sm-3">Passwort des Administrators:</dt>
                                                                    <dd class="col-sm-9" id="finalAdminPassword">NOCH NICHT GESETZT</dd>
                                                                </dl>
                                                    
                                                              
                                                              
                                                              <br /><br />
                                                      </div>
                                                <div class="row">                                                 
                                                    <div class="col-12 mb-1">
                                                        <div class="form-check">
                                                            <input class="form-check-input" OnClick="document.getElementById('submit_installation').disabled = false;" type="checkbox" value="" name="confirmInstallation" id="confirmInstallation" />
                                                            <label class="form-check-label" for="confirmInstallation">Ich habe alle Informationen sorgfältig geprüft und möchte die Installation starten.</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            

                                            <div class="d-flex justify-content-between mt-1">
                                            <button class="btn btn-primary btn-prev" id="last_prev_button">
                                                    <i data-feather="chevron-left" class="align-middle me-sm-25 me-0"></i>
                                                    <span class="align-middle d-sm-inline-block d-none">Zurück</span>
                                            </button>
                                                <button class="btn btn-success btn-submit" id="submit_installation" disabled>
                                                    <i id="submit_button_feather" data-feather="check" class="align-middle me-sm-25 me-0"></i>
                                                    <span id="submit_button_text" class="align-middle d-sm-inline-block d-none">Installation starten</span>
                                                </button>
                                            </div>
                                            <input type="text" name="install" value="install" id="install" hidden>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- END: Content-->


    <!-- BEGIN: Vendor JS-->
    <script src="../app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="../app-assets/vendors/js/forms/wizard/bs-stepper.min.js"></script>
    <script src="../app-assets/vendors/js/forms/select/select2.full.min.js"></script>
    <script src="../app-assets/vendors/js/forms/validation/jquery.validate.min.js"></script>
    <script src="../app-assets/vendors/js/forms/cleave/cleave.min.js"></script>
    <script src="../app-assets/vendors/js/forms/cleave/addons/cleave-phone.de.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="../app-assets/js/core/app-menu.js"></script>
    <script src="../app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    
    <script>
        /*=========================================================================================
  File Name: auth-register.js
  Description: Auth register js file.
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: PIXINVENT
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

$(function () {
  ('use strict');

  var assetsPath = '../',
    registerMultiStepsWizard = document.querySelector('.register-multi-steps-wizard'),
    pageResetForm = $('.auth-register-form'),
    select = $('.select2'),
    creditCard = $('.credit-card-mask'),
    expiryDateMask = $('.expiry-date-mask'),
    cvvMask = $('.cvv-code-mask'),
    mobileNumberMask = $('.mobile-number-mask'),
    pinCodeMask = $('.pin-code-mask');

  if ($('body').attr('data-framework') === 'laravel') {
    assetsPath = $('body').attr('data-asset-path');
  }

  // jQuery Validation
  // --------------------------------------------------------------------
  if (pageResetForm.length) {
    pageResetForm.validate({
      /*
      * ? To enable validation onkeyup
      onkeyup: function (element) {
        $(element).valid();
      },*/
      /*
      * ? To enable validation on focusout
      onfocusout: function (element) {
        $(element).valid();
      }, */
      rules: {
        'db_host': {
          required: true
        },
        'db_name': {
          required: true
        },
        'db_user': {
          required: true
        },
        'db_password': {
          required: true
        }
      }
    });
  }

  // multi-steps registration
  // --------------------------------------------------------------------

  // Horizontal Wizard
  if (typeof registerMultiStepsWizard !== undefined && registerMultiStepsWizard !== null) {
    var numberedStepper = new Stepper(registerMultiStepsWizard),
      $form = $(registerMultiStepsWizard).find('form');
    $form.each(function () {
      var $this = $(this);
      $this.validate({
        rules: {
          username: {
            required: true
          },
          email: {
            required: true
          },
          password: {
            required: true,
            minlength: 8
          },
          'admin_password2': {
            required: true,
            minlength: 8,
            equalTo: '#admin_password'
          },
          'admin_first_name': {
            required: true
          },
          'admin_second_name': {
            required: true
          },
          addCard: {
            required: true
          }
        },
        messages: {
          password: {
            required: 'Gib ein neues Passwort ein',
            minlength: 'Muss mindestens 8 Zeichen enthalten'
          },
          'admin_password2': {
            required: 'Bitte Passwort bestätigen',
            minlength: 'Muss mindestens 8 Zeichen enthalten',
            equalTo: 'Passwörter stimmen nicht überein'
          }
        }
      });
    });

    $(registerMultiStepsWizard)
      .find('.btn-next')
      .each(function () {
        $(this).on('click', function (e) {
          var isValid = $(this).parent().siblings('form').valid();
          if (isValid) {
            numberedStepper.next();
          } else {
            e.preventDefault();
          }
        });
      });

    $(registerMultiStepsWizard)
      .find('.btn-prev')
      .on('click', function () {
        numberedStepper.previous();
      });

    $(registerMultiStepsWizard)
      .find('.btn-submit')
      .on('click', function () {
        
          // Erstelle ein neues Objekt, das die Eingaben aus allen drei Formularen enthält
          var formData = {
            db_host: document.getElementById('db_host').value,
            db_name: document.getElementById('db_name').value,
            db_user: document.getElementById('db_user').value,
            db_password: document.getElementById('db_password').value,
            admin_first_name: document.getElementById('admin_first_name').value,
            admin_second_name: document.getElementById('admin_second_name').value,
            admin_password: document.getElementById('admin_password').value,
            school_name: document.getElementById('school_name').value,
            admin_username: document.getElementById('admin_username').value,
            school_id: document.getElementById('school_id').value,
            install: document.getElementById('install').value,
            remember_me: document.getElementById('remember_me').checked,
            connect_repo: document.getElementById('connect_repo').checked
          }
            var xhr = new XMLHttpRequest();
                xhr.open('POST', 'installer.php', true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    var response = JSON.parse(this.responseText);
                    if (response.success) {
                    window.location.href = response.redirect;
                    } else {
                    console.log('Installation fehlgeschlagen.');
                    alert("Installation fehlgeschlagen. Bitte Datenbankinhalt und bereits geschriebene Dateien löschen und nocheinmal versuchen. Wenn das Problem immernoch auftritt, melden Sie sich bitte bei ide4school@em-cloud-solutions.de!")
                    }
                }
                };

                xhr.send(JSON.stringify(formData));
                document.getElementById('submit_installation').disabled = true;
                document.getElementById('last_prev_button').disabled = true;
                document.getElementById('confirmInstallation').disabled = true;
                document.getElementById('submit_button_text').innerHTML = 'Installation läuft. Bitte warten.';
                $("svg.feather.feather-check").replaceWith(feather.icons.loader.toSvg());
        
      });
  }
  

  // select2
  select.each(function () {
    var $this = $(this);
    $this.wrap('<div class="position-relative"></div>');
    $this.select2({
      // the following code is used to disable x-scrollbar when click in select input and
      // take 100% width in responsive also
      dropdownAutoWidth: true,
      width: '100%',
      dropdownParent: $this.parent()
    });
  });

    // credit card

  // Credit Card
  if (creditCard.length) {
    creditCard.each(function () {
      new Cleave($(this), {
        creditCard: true,
        onCreditCardTypeChanged: function (type) {
          const elementNodeList = document.querySelectorAll('.card-type');
          if (type != '' && type != 'unknown') {
            //! we accept this approach for multiple credit card masking
            for (let i = 0; i < elementNodeList.length; i++) {
              elementNodeList[i].innerHTML =
                '<img src="' + assetsPath + 'images/icons/payments/' + type + '-cc.png" height="24"/>';
            }
          } else {
            for (let i = 0; i < elementNodeList.length; i++) {
              elementNodeList[i].innerHTML = '';
            }
          }
        }
      });
    });
  }

  // Expiry Date Mask
  if (expiryDateMask.length) {
    new Cleave(expiryDateMask, {
      date: true,
      delimiter: '/',
      datePattern: ['m', 'y']
    });
  }

  // CVV
  if (cvvMask.length) {
    new Cleave(cvvMask, {
      numeral: true,
      numeralPositiveOnly: true
    });
  }

  // phone number mask
  if (mobileNumberMask.length) {
    new Cleave(mobileNumberMask, {
      phone: true,
      phoneRegionCode: 'US'
    });
  }

  // Pincode
  if (pinCodeMask.length) {
    new Cleave(pinCodeMask, {
      delimiter: '',
      numeral: true
    });
  }

  // multi-steps registration
  // --------------------------------------------------------------------
});

    </script>
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