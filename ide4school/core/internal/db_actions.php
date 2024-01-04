<?php
include 'database_config.php';

class DB {
    private static $_db_username = DB_USERNAME;
    private static $_db_password = DB_PASSWORD;
    private static $_db_host = DB_HOST;
    private static $_db_name = DB_NAME;
    private static $_db;

    function __construct() {
        try {
            self::$_db = new PDO("mysql:host=" . self::$_db_host . ";dbname=" . self::$_db_name . ";charset=utf8mb4",  self::$_db_username , self::$_db_password);
        } catch(PDOException $e) {
            echo "Datenbankverbindung gescheitert!";
            die();
        }
    }

    // USER INTERACTIONS

    // - Authentications

    // -- Login

    function LoginAllowed() {
        if(isset($_SESSION['user_id'])) {
        $stmt = self::$_db->prepare("SELECT allow_login FROM settings");
        $stmt->execute();

        $result = $stmt->fetch();
        if($result['allow_login'] == '1') {
            return true;
        } else {
            return false;
        }
        }
        else {
            return false;
        }
    }

   

    function login($username, $passwort, $remember_me, $browser, $device) {
        //Set table names manually
        $user_table = "users";
        $log_table = "logs";
        $statement = self::$_db->prepare("SELECT * FROM " . $user_table . " WHERE username = :username");
        $result = $statement->execute(array('username' => $username));
        $user = $statement->fetch();

        if($user !== false && password_verify($passwort, $user['password'])) {
            //Wenn 2 Faktor Authentifizierung an (ein Eintrag mit der Nutzer ID in der Tabelle 2fa gefunden wird) soll der Nutzer auf die 2FA Seite weitergeleitet werden
            $_SESSION['user_id'] = $user['id'];
            if(!self::LoginAllowed() && !self::IsAdmin()) {
                $_SESSION['login_error'] = 3;
            }

            if($remember_me == true) {
                $_SESSION['remember_me'] = true;
            }

            if($_SESSION['2fa_passed'] == false) {
                $stmt = self::$_db->prepare("SELECT * FROM 2fa WHERE owner = :user_id AND status = '1' AND type = '1'");
                $stmt->execute(array('user_id' => $user['id']));
                $result = $stmt->fetch(); 

                if($result !== false) {
                    header("Location: 2fa");
                    exit;
                }
            }
            
            $_SESSION['login_state'] = true;

            //Create Session DB Link's

            $_SESSION['settings_db'] = "settings";
            $_SESSION['messages_db'] = "messages";
            $_SESSION['users_db'] = "users";
            $_SESSION['logs_db'] = "logs";
            $_SESSION['todo_db'] = "todos";
            $_SESSION['class_db'] = "classes";
            $_SESSION['emails_db'] = "emails";
            $_SESSION['submission_db'] = "submissions";
            $_SESSION['notifications_db'] = "notifications";
            $_SESSION['assignments_db'] = "assignments";
            $_SESSION['settings_db'] = "settings";
            $_SESSION['features_db'] = "features";
            $_SESSION['compiler_lang_db'] = "compiler_langs";
            $_SESSION['games_db'] = "games";
            $_SESSION['game_sessions_db'] = "game_sessions";
            $_SESSION['game_invitations_db'] = "sgb_game_invitations";


            //Initialize User ID in Session

            $_SESSION['currentSessionClass'] = "Alle Benutzer und Klassen";

            if(!self::LoginAllowed() && !self::IsAdmin()) {
                $_SESSION['login_error'] = 3;
            }

            if($_SESSION['remember_me'] == true) {
                self::createRememberMe($user['id']);
            }
            unset($_SESSION['remember_me']);

            $stmt = self::$_db->prepare("UPDATE " . $user_table . " SET status='1' WHERE id=:user_id");
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $user_fullname = self::getLogUser();
            $log_message = $user_fullname .' hat sich im Benutzerportal angemeldet.';
            $log->bindParam(":log_entry", $log_message);
            $stmt->bindParam(":user_id", $_SESSION['user_id']);

            $stmt->execute();
            $log->execute();
            self::createLoginAttempt($username, $browser, $device, "1");
            return true;
        } else {
            $fail_log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $fail_log_message = 'Jemand hat sich versucht mit dem Benutzernamen "'. $username .'" im Benutzerportal einzuloggen.';
            $fail_log->bindParam(":log_entry", $fail_log_message);
            $fail_log->execute();
            self::createLoginAttempt($username, $browser, $device, "0");
            $_SESSION['login_error'] = 2;
            return false;
        }   
    }

    function continueLogin($browser, $device) {
        $_SESSION['login_state'] = true;
        //Create Session DB Link's

        $_SESSION['settings_db'] = "settings";
        $_SESSION['messages_db'] = "messages";
        $_SESSION['users_db'] = "users";
        $_SESSION['logs_db'] = "logs";
        $_SESSION['todo_db'] = "todos";
        $_SESSION['class_db'] = "classes";
        $_SESSION['emails_db'] = "emails";
        $_SESSION['submission_db'] = "submissions";
        $_SESSION['notifications_db'] = "notifications";
        $_SESSION['assignments_db'] = "assignments";
        $_SESSION['settings_db'] = "settings";
        $_SESSION['features_db'] = "features";
        $_SESSION['compiler_lang_db'] = "compiler_langs";
        $_SESSION['games_db'] = "games";
        $_SESSION['game_sessions_db'] = "game_sessions";
        $_SESSION['game_invitations_db'] = "sgb_game_invitations";


        //Initialize User ID in Session

        $_SESSION['currentSessionClass'] = "Alle Benutzer und Klassen";

        $username = self::getUsernameViaID($_SESSION['user_id']);
        if($_SESSION['remember_me'] == true) {
            self::createRememberMe($_SESSION['user_id']);
        }

        $stmt = self::$_db->prepare("UPDATE users SET status='1' WHERE id=:user_id");
        $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
        $user_fullname = self::getLogUser();
        $log_message = $user_fullname .' hat sich im Benutzerportal angemeldet.';
        $log->bindParam(":log_entry", $log_message);
        $stmt->bindParam(":user_id", $_SESSION['user_id']);
        $stmt->execute();
        $log->execute();
        self::createLoginAttempt($username, $browser, $device, "1");
        unset($_SESSION['remember_me']);
    }

    function deleteUserToken($user_id, $token) {
        $stmt = self::$_db->prepare("DELETE FROM user_tokens WHERE user_id=:user_id AND token=:token");
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":token", $token);
        $stmt->execute();
    }

    function createRememberMe($user_id) {
        // Wenn der Benutzer "Remember Me" ausgewählt hat, generiere einen neuen langfristigen Sitzungsschlüssel
            // Generiere einen zufälligen langfristigen Sitzungsschlüssel
            $long_term_key = bin2hex(random_bytes(32));
            
            // Speichere den langfristigen Sitzungsschlüssel in der Datenbank zusammen mit der ID des Benutzers
            $stmt = self::$_db->prepare("INSERT INTO user_tokens (token, user_id) VALUES(:token, :user_id)");
            $stmt->bindParam(":token", $long_term_key);
            $stmt->bindParam(":user_id", $user_id);
            if($stmt->execute()) {
                $day = 30;
                $expired_seconds = time() + 60 * 60 * 24 * $day;
                // Setze den langfristigen Sitzungsschlüssel als Cookie (mit HttpOnly-Flag)
                setcookie('remember_me', $long_term_key, $expired_seconds);
            }
    }


    function checkRememberMe($token) {
        $stmt = self::$_db->prepare("SELECT user_id FROM user_tokens WHERE token = :token");
        $stmt->bindParam(":token", $token);
        $stmt->execute();
        $result = $stmt->fetch();
        if($result) {
            return $result['user_id'];
        }
        else {
            return false;
        }
    }

    function autoLoginRememberMe() {
        if(!self::LoginAllowed() && !self::IsAdmin()) {
            $_SESSION['login_error'] = 3;
            unset($_SESSION['user_id']);
            header("Location: login.php");
        }

            $_SESSION['settings_db'] = "settings";
            $_SESSION['messages_db'] = "messages";
            $_SESSION['users_db'] = "users";
            $_SESSION['logs_db'] = "logs";
            $_SESSION['todo_db'] = "todos";
            $_SESSION['class_db'] = "classes";
            $_SESSION['emails_db'] = "emails";
            $_SESSION['submission_db'] = "submissions";
            $_SESSION['notifications_db'] = "notifications";
            $_SESSION['assignments_db'] = "assignments";
            $_SESSION['settings_db'] = "settings";
            $_SESSION['features_db'] = "features";
            $_SESSION['compiler_lang_db'] = "compiler_langs";
            $_SESSION['games_db'] = "games";
            $_SESSION['game_sessions_db'] = "game_sessions";
            $_SESSION['game_invitations_db'] = "sgb_game_invitations";
            $_SESSION['currentSessionClass'] = "Alle Benutzer und Klassen";

            $stmt = self::$_db->prepare("UPDATE users SET status='1' WHERE id=:user_id");
            $stmt->bindParam(":user_id", $_SESSION['user_id']);
            $_SESSION['login_state'] = true;

            if($stmt->execute()) {
                return true;
            }
            else {
                $fail_log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
                $fail_log_message = 'Jemand hat sich versucht mit dem Benutzernamen "'. $username .'" im Benutzerportal einzuloggen.';
                $fail_log->bindParam(":log_entry", $fail_log_message);
                $fail_log->execute();
                $_SESSION['login_error'] = 2;
                return false;
            }

    }

    //Check Login Status
    function isUserLoggedIn() {
        if(isset($_SESSION['user_id'])) {
            $stmt = self::$_db->prepare("SELECT firstName FROM users WHERE status='1' AND id=:user_id");
            $stmt->bindParam(":user_id", $_SESSION['user_id']);
            $stmt->execute();

            if($stmt->rowCount() === 1) {
                return true;
            } else {
                return false;
            }
        }
            else {
            return false;
        }
    }

    function createLoginAttempt($username, $browser, $device, $success) {
        $user = self::getUserIDViaUsername($username);
        $stmt = self::$_db->prepare("INSERT INTO last_logins (user, browser, device, success) VALUES(:user, :browser, :device, :success)");
        $stmt->bindParam(":user", $user);	
        $stmt->bindParam(":browser", $browser);
        $stmt->bindParam(":device", $device);
        $stmt->bindParam(":success", $success);
        $stmt->execute();
    }
    
    function getUserIDViaUsername($username) {
        $stmt = self::$_db->prepare("SELECT id FROM users WHERE username=:username");
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $result = $stmt->fetch();
        if($result) {
            return $result['id'];
        }
        else {
            return false;
        }
    }

    function IsAdmin() {
        if(isset($_SESSION['user_id'])) {
        $stmt = self::$_db->prepare("SELECT role FROM users WHERE id=:user_id");
        $stmt->bindParam(":user_id", $_SESSION['user_id']);
        $stmt->execute();

        $result = $stmt->fetch();
        if($result['role'] == 'Administrator') {
            return true;
        } else {
            return false;
        }
        }
        else {
            return false;
        }
    }

    function noStudent() {
        if(isset($_SESSION['user_id'])) {
        $stmt = self::$_db->prepare("SELECT role FROM users WHERE id=:user_id");
        $stmt->bindParam(":user_id", $_SESSION['user_id']);
        $stmt->execute();

        $result = $stmt->fetch();
        if($result['role'] != 'Schüler') {
            return true;
        } else {
            return false;
        }
        }
        else {
            return false;
        }
    }

    function logout($benutzername) {
        $stmt = self::$_db->prepare("UPDATE users SET status='0', last_logout=:last_logout WHERE id=:user_id");
        $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
        $log_message = $benutzername .' hat sich ausgeloggt.';
        $date_time = date("Y-m-d H:i:s");
        $log->bindParam(":log_entry", $log_message);
        $stmt->bindParam(":user_id", $_SESSION['user_id']);
        $stmt->bindParam(":last_logout", $date_time);
        $log->execute();
        $stmt->execute();
    }

    // GET My profile data

    function getCurrentUserInformations() {
        $stmt = self::$_db->prepare("SELECT * FROM users WHERE id=:id");
        $stmt->bindParam(":id", $_SESSION['user_id']);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // GET profile data of other users

    function getProfileData($user_id) {
        $stmt = self::$_db->prepare("SELECT * FROM users WHERE id=:id");
        $stmt->bindParam(":id", $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function firstStartConfirm($password) {
        $stmt = self::$_db->prepare("UPDATE users SET password=:password, agb_confirmed='1', agb_confirmed_at=:agb_confirmed_at WHERE id=:id");
        $stmt->bindParam(":id", $_SESSION['user_id']);
        $stmt->bindParam(":password", $password);
        $now = new DateTime();
        $now = $now->format('Y-m-d H:i:s');
        $stmt->bindParam(":agb_confirmed_at", $now);
        $user = self::getCurrentFullName();

        if($stmt->execute()) {
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_message = $user .' hat sich das erste mal angemeldet, die Nutzungsbedingungen und Datenschutzrichtlinien akzeptiert und das Passwort geändert.';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();
            return true;
        }

        else {
                $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
                $log_message = $user .' hat sich das erste mal angemeldet und versucht die Nutzungsbedingungen und Datenschutzrichtlinien zu akzeptieren sowie das Passwort zu ändern. Es trat ein Datenbankfehler auf.';
                $log->bindParam(":log_entry", $log_message);
                $log->execute();
                return false;
        }
    }

    function getLogUser() {
        $stmt = self::$_db->prepare("SELECT firstName, secondName FROM users WHERE id=:id");
        $stmt->bindParam(":id", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->secondName . ", " . $result->firstName;
    }

    function getCurrentInstitution() {
        $stmt = self::$_db->prepare("SELECT institution_name FROM settings WHERE id=:id");
        $id = "1";
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->institution_name;
    }

    function getClassDir($id) {
        $stmt = self::$_db->prepare("SELECT class_dir FROM classes WHERE id=:id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->class_dir;
    }

    function getCurrentUserPasswort($user_id) {
        $stmt = self::$_db->prepare("SELECT password FROM users WHERE id=:id");
        $stmt->bindParam(":id", $user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->password;
    }
    
    function getUserActivity($user_fullname) {
        $stmt = self::$_db->prepare("SELECT * FROM logs WHERE text LIKE '%$user_fullname%' ORDER BY date DESC LIMIT 6");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    function getCurrentUserClass() {
        $stmt = self::$_db->prepare("SELECT class FROM users WHERE id=:id");
        $stmt->bindParam(":id", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->class;
    }

    function getClassNameViaID($id) {
        $stmt = self::$_db->prepare("SELECT name FROM classes WHERE id=:id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->name;
    }

    function getCurrentFullName() {
        $stmt = self::$_db->prepare("SELECT firstName, secondName FROM users WHERE id=:id");
        $stmt->bindParam(":id", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->secondName . ', ' . $result->firstName;
    }

    function getCurrentUsername() {
        $stmt = self::$_db->prepare("SELECT username FROM users WHERE id=:id");
        $stmt->bindParam(":id", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->username;
    }

    function getUsernameViaID($id) {
        $stmt = self::$_db->prepare("SELECT username FROM users WHERE id=:id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->username;
    }



    // Focus Mode 

    //Check ListenModeStatus (Online)

    function isListenModeOn() {
        $stmt = self::$_db->prepare("SELECT focus_mode FROM users WHERE id=:user_id AND role='Schüler'");
        $stmt->bindParam(":user_id", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->fetch();
        if($result['focus_mode'] == '1') {
            return true;
        } else {
            return false;
        }
    }

   

    //Set Group Focus Mode to offline

    function setGroupListenModeToOffline($class_id) {
        $stmt = self::$_db->prepare("UPDATE classes SET focus_mode=:listen_mode WHERE id=:class_id");
        $listen_mode = '0';
        $stmt->bindParam(":listen_mode", $listen_mode);
        $stmt->bindParam(":class_id", $class_id);
        if($stmt->execute()) {

            $stmt = self::$_db->prepare("UPDATE users SET focus_mode='0' WHERE class=:class");
            $class_name = self::getCurrentUserClass();
            $stmt->bindParam(":class", $class_name);
            if($stmt->execute()) {
                return true;
        } else {
            return false;
        }
    }
    }

    function setGroupListenModeToOnline($class_id) {
        $stmt = self::$_db->prepare("UPDATE classes SET focus_mode=:listen_mode WHERE id=:class_id");
        $listen_mode = '1';
        $stmt->bindParam(":listen_mode", $listen_mode);
        $stmt->bindParam(":class_id", $class_id);
        if($stmt->execute()) {

            $stmt = self::$_db->prepare("UPDATE users SET focus_mode='1' WHERE class=:class");
            $class_name = self::getCurrentUserClass();
            $stmt->bindParam(":class", $class_name);
            if($stmt->execute()) {
                return true;
        } else {
            return false;
        }
    }
    }

    function setUserListenModeToOffline($user_id) {
        $stmt = self::$_db->prepare("UPDATE users SET focus_mode=:listen_mode WHERE id=:user_id");
        $listen_mode = '0';
        $stmt->bindParam(":listen_mode", $listen_mode);
        $stmt->bindParam(":user_id", $user_id);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function setUserListenModeToOnline($user_id) {
        $stmt = self::$_db->prepare("UPDATE users SET focus_mode=:listen_mode WHERE id=:user_id");
        $listen_mode = '1';
        $stmt->bindParam(":listen_mode", $listen_mode);
        $stmt->bindParam(":user_id", $user_id);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }


    // Users & Groups

    function getAllUsers() {
        $stmt = self::$_db->prepare("SELECT * FROM users ORDER BY secondName ASC");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getAllUsersFromClass($selected_class) {
        $stmt = self::$_db->prepare("SELECT * FROM users WHERE class=:class ORDER BY secondName ASC");
        $stmt->bindParam(":class", $selected_class);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    

    function getAllClasses() {
        $stmt = self::$_db->prepare("SELECT * FROM classes ORDER BY name ASC");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    function countAllUsers() {
        $stmt = self::$_db->prepare("SELECT * FROM users");
        $stmt->execute();
        $result = $stmt->rowCount();
        return $result;
    }

    function countAllActiveUsers() {
        $stmt = self::$_db->prepare("SELECT * FROM users WHERE status='1'");
        $stmt->execute();
        $result = $stmt->rowCount();
        return $result;
    }

    function getAllUsersWithActiveFocusMode() {
        $stmt = self::$_db->prepare("SELECT * FROM users WHERE focus_mode='1'");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return count($result);
    }

    function countAllTeachers() {
        $stmt = self::$_db->prepare("SELECT * FROM users WHERE role=:user_role");
        $user_group = 'Lehrer';
        $stmt->bindParam(":user_role", $user_group);
        $stmt->execute();
        $result = $stmt->rowCount();
        return $result;
    }

    function getClassDirViaName($className) {
        $stmt = self::$_db->prepare("SELECT class_dir FROM classes WHERE name=:name");
        $stmt->bindParam(":name", $className);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->class_dir;
        
    }

    function getAssignedClassNameViaAssignID($id) {
        $stmt = self::$_db->prepare("SELECT teacher FROM ". $_SESSION['assignments_db'] . " WHERE id=:id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->teacher;
    }

    function getClassIDViaName($className) {
        $stmt = self::$_db->prepare("SELECT id FROM classes WHERE name=:name");
        $stmt->bindParam(":name", $className);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->id;
    }

    function getCurrentUserClassID() {
        $stmt = self::$_db->prepare("SELECT id FROM classes WHERE name=:name");
        $user_class = self::getCurrentUserClass();
        $stmt->bindParam(":name", $user_class);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->id;
    }



    function createUser($firstName, $secondName, $class, $role, $username, $password, $log_user) {
        $stmt = self::$_db->prepare("INSERT INTO users (firstName, secondName, username, password, role, class, institution) VALUES(:firstName, :secondName, :username, :password, :role, :class, :institution)");
        $stmt->bindParam(":firstName", $firstName);
        $stmt->bindParam(":secondName", $secondName);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":class", $class);

        $institution = self::getCurrentInstitution();

        $stmt->bindParam(":institution", $institution);
        if($stmt->execute()) {
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_message = $log_user .' hat einen neuen Benutzer mit dem Namen "'. $firstName . ' ' . $secondName .'" erstellt.';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();

            // Create Assignments
            if($role == "Lehrer") {
            $available_classes = self::getAllClasses();
            foreach ($available_classes as $available_class) {
                $assign = self::$_db->prepare("INSERT INTO ". $_SESSION['assignments_db'] . " (teacher, class, access) VALUES(:teacher, :class, :access)");
                $user_id = self::getUserIDViaUsername($username);
                $assign->bindParam(":teacher", $user_id);
                $assign->bindParam(":class", $available_class['name']);
                $pre_access = '1';
                $assign->bindParam(":access", $pre_access);
                $assign->execute();
            }
        }

            if($role == "Administrator") {
            $available_classes = self::getAllClasses();
            foreach ($available_classes as $available_class) {
                $assign = self::$_db->prepare("INSERT INTO ". $_SESSION['assignments_db'] . " (teacher, class, access) VALUES(:teacher, :class, :access)");
                $user_id = self::getUserIDViaUsername($username);
                $assign->bindParam(":teacher", $user_id);
                $assign->bindParam(":class", $available_class['name']);
                $pre_access = '1';
                $assign->bindParam(":access", $pre_access);
                $assign->execute();
            }
        }

            return true;
        }

        else {
                $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
                $log_message = $log_user .' hat versucht einen Benutzer mit dem Namen "'. $firstName . ' ' . $secondName .'" zu erstellen. Der Benutzer konnte jedoch nicht erstellt werden.';
                $log->bindParam(":log_entry", $log_message);
                $log->execute();
                return false;
        }
    }

    function createClass($name, $description, $class_dir, $log_user) {
        $stmt = self::$_db->prepare("INSERT INTO classes (name, description, class_dir) VALUES(:name, :description, :class_dir)");
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":class_dir", $class_dir);

        if($stmt->execute()) {
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_message = $log_user .' hat eine neue Klasse mit dem Namen "'. $name .'" erstellt.';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();

            // Create Assignments

                $teachersandadmins = self::getTeacherAndAdmins();
                foreach ($teachersandadmins as $person) {
                    $assign = self::$_db->prepare("INSERT INTO ". $_SESSION['assignments_db'] . " (teacher, class, access) VALUES(:teacher, :class, :access)");
                    $assign->bindParam(":teacher", $person['id']);
                    $assign->bindParam(":class", $name);
                    if($person['role'] == "Lehrer") {
                        $pre_access = '1';
                    }
                    if($person['role'] == "Administrator") {
                        $pre_access = '1';
                    }                    
                    $assign->bindParam(":access", $pre_access);
                    $assign->execute();
                }


            return true;
        }

        else {
                $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
                $log_message = $log_user .' hat versucht eine Klasse mit dem Namen "'. $name .'" zu erstellen. Der Benutzer konnte jedoch nicht erstellt werden.';
                $log->bindParam(":log_entry", $log_message);
                $log->execute();
                return false;
        }
    }

    function LogoutUser($id) {
        $stmt = self::$_db->prepare("UPDATE users SET status='0', last_logout=:last_logout WHERE id=:id");
        $stmt->bindParam(":id", $id);
        $date_time = date("Y-m-d H:i:s");
        $stmt->bindParam(":last_logout", $date_time);
        $log_user = self::getLogUser();

        if($stmt->execute()) {
            $log_user = self::getLogUser();
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $logout_name = self::getFullNameViaID($id);
            $log_message = $log_user .' hat den Benutzer '. $logout_name .' abgemeldet.';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();
            return true;
        }

        else {
                $logout_name = self::getFullNameViaID($id);
                $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
                $log_message = $log_user .' hat versucht den Benutzer mit dem Namen "'. $logout_name .'" abzumelden. Der Benutzer konnte jedoch nicht abgemeldet werden.';
                $log->bindParam(":log_entry", $log_message);
                $log->execute();
                return false;
        }
    }

    function LogoutClass($class_name) {
        if($class_name == "Alle Klassen / Gruppen") {
            $students = self::getAllUsers();
            foreach ($students as $student) {
                $stmt = self::$_db->prepare("UPDATE users SET status='0' WHERE id !=:current_user_id");
                $stmt->bindParam(":current_user_id", $_SESSION['user_id']);
                $stmt->execute();
            }
                    $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
                    $log_user = self::getLogUser();
                    $log_message = $log_user .' hat alle Benutzer abgemeldet.';
                    $log->bindParam(":log_entry", $log_message);
                    $log->execute();
                    return true;
        }
        else {
            $students = self::getAllUsersFromClass($class_name);
            foreach ($students as $student) {
                $stmt = self::$_db->prepare("UPDATE users SET status='0' WHERE id=:id AND id !=:current_user_id");
                $stmt->bindParam(":id", $student['id']);
                $stmt->bindParam(":current_user_id", $_SESSION['user_id']);
                $stmt->execute();
            }
                    $log_user = self::getLogUser();
                    $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
                    $log_message = $log_user .' hat die Benutzer der ' . $class_name . " abgemeldet.";
                    $log->bindParam(":log_entry", $log_message);
                    $log->execute();
                    return true;
                    
        }
    }

    function updatePasswort($password) {
        $stmt = self::$_db->prepare("UPDATE users SET password=:password WHERE id=:id");
        $stmt->bindParam(":id", $_SESSION['user_id']);
        $stmt->bindParam(":password", $password);

        if($stmt->execute()) {
            $user = self::getCurrentFullName();
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_message = $user .' hat sein Passwort geändert.';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();

            $stmt = self::$_db->prepare("DELETE FROM user_tokens WHERE user_id=:user_id");
            $stmt->bindParam(":user_id", $_SESSION['user_id']);
            $stmt->execute();

            return true;
        }

        else {
                $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
                $log_message = $log_user .' hat versucht sein Passwort zu ändern. Der Vorgang konnte jedoch nicht abgeschlossen werden. Es trat ein Datenbankfehler auf.';
                $log->bindParam(":log_entry", $log_message);
                $log->execute();
                return false;
        }
    }    
    

    function updateUser($firstName, $secondName, $class, $role, $username, $password, $log_user, $user_id) {
        $stmt = self::$_db->prepare("UPDATE users SET firstName=:firstName, secondName=:secondName, username=:username, password=:password, role=:role, class=:class, institution=:institution WHERE id=:id");
        $stmt->bindParam(":firstName", $firstName);
        $stmt->bindParam(":secondName", $secondName);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":class", $class);
        $stmt->bindParam(":id", $user_id);
        $institution = self::getCurrentInstitution();
        $stmt->bindParam(":institution", $institution);
        if($stmt->execute()) {
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_message = $log_user .' hat den Benutzer mit dem Namen "'. $firstName . ' ' . $secondName .'" bearbeitet.';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();
            return true;
        }

        else {
                $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
                $log_message = $log_user .' hat versucht den Benutzer mit dem Namen "'. $firstName . ' ' . $secondName .'" zu bearbeiten. Der Benutzer konnte jedoch nicht bearbeitet werden.';
                $log->bindParam(":log_entry", $log_message);
                $log->execute();
                return false;
        }
    }

    function deleteUser($id) {
        $stmt = self::$_db->prepare("DELETE FROM users WHERE id=:id");
        $stmt->bindParam(":id", $id);
        $assign = self::$_db->prepare("DELETE FROM ". $_SESSION['assignments_db'] . " WHERE teacher=:teacher");
        $assign->bindParam(":teacher", $id);
        $delete_name = self::getFullNameViaID($id);

        if($stmt->execute()) {
            $assign->execute();
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_user = self::getLogUser();
            $log_message = $log_user .' hat den Benutzer "'. $delete_name .'" gelöscht.';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();

           

            return true;
        } else {
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_message = $log_user .' hat versucht den Benutzer "'. $delete_name .'" zu löschen. Der Vorgang konnte nicht erfolgreich abgeschlossen werden.';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();
            return false;
        }
    }

    function deleteClass($id) {
        $stmt = self::$_db->prepare("DELETE FROM classes WHERE id=:id");
        $stmt->bindParam(":id", $id);

        $assign = self::$_db->prepare("DELETE FROM ". $_SESSION['assignments_db'] . " WHERE class=:class");
        $class = self::getClassNameViaID($id);
        $assign->bindParam(":class", $class);

        if($stmt->execute()) {
            $assign->execute();
            return true;
        }
        else {
            return false;
        }
        
    }



    function delete_user_emergency($firstName, $secondName, $class, $role, $username, $password) {
        $stmt = self::$_db->prepare("DELETE FROM users WHERE firstName=:firstName AND secondName=:secondName AND class=:class AND role=:role");
        $stmt->bindParam(":firstName", $firstName);
        $stmt->bindParam(":secondName", $secondName);
        $stmt->bindParam(":class", $class);
        $stmt->bindParam(":role", $role);
        $stmt->execute();
    }

    function delete_class_emergency($name, $description, $class_dir) {
        $stmt = self::$_db->prepare("DELETE FROM classes WHERE name=:name AND description=:description AND class_dir=:class_dir");
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":class_dir", $class_dir);
        $stmt->execute();
    }

    function getSessionClassName() {
        $stmt = self::$_db->prepare("SELECT name FROM classes WHERE id=:id");
        $stmt->bindParam(":id", $_SESSION['currentSessionClass']);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->name;
    }

    // TODO

    function getTodosFromFolder($current_folder) {
        $stmt = self::$_db->prepare("SELECT * FROM ". $_SESSION['todo_db'] . " WHERE folder=:folder AND receiver=:receive_user OR folder=:folder AND receiver=:receive_class ORDER BY creation_date DESC");
        $stmt->bindParam(":folder", $current_folder);
        $userFullName = self::getCurrentFullName();
        $stmt->bindParam(":receive_user", $userFullName);
        $userClass = self::getCurrentUserClass();
        $stmt->bindParam(":receive_class", $userClass);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getAllTodos($current_folder, $currentSelectedClass) {
        $stmt = self::$_db->prepare("SELECT * FROM ". $_SESSION['todo_db'] . " WHERE folder=:folder AND class=:class ORDER BY creation_date DESC");
        $stmt->bindParam(":folder", $current_folder);
        $stmt->bindParam(":class", $currentSelectedClass);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getClassMembers($currentSelectedClass) {
        $stmt = self::$_db->prepare("SELECT * FROM users WHERE class=:class ORDER BY secondName ASC");
        $stmt->bindParam(":class", $currentSelectedClass);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function createTodo($titel, $sender, $zuteilung, $fertig, $description, $class, $log_user) {
        $stmt = self::$_db->prepare("INSERT INTO ". $_SESSION['todo_db'] . " (title, sender, receiver, due_date, text, class) VALUES(:title, :sender, :receiver, :due_date, :text, :class)");
        $stmt->bindParam(":title", $titel);
        $stmt->bindParam(":sender", $sender);
        $stmt->bindParam(":receiver", $zuteilung);
        $stmt->bindParam(":due_date", $fertig);
        $stmt->bindParam(":text", $description);
        $stmt->bindParam(":class", $class);
        if($stmt->execute()) {
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_message = $log_user .' hat eine neue Aufgabe mit dem Titel "'. $titel .'" erstellt.';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();
            
            $notification = self::$_db->prepare("INSERT INTO notifications (user, heading, text) VALUES(:user, :heading, :text)");
            $heading = "Du hast eine neue Aufgabe! ";
            $notify_text = "Schau im Aufgabenbereich vorbei! ✅";
            $notification->bindParam(":user", $zuteilung);
            $notification->bindParam(":heading", $heading);
            $notification->bindParam(":text", $notify_text);
            $notification->execute();

            return true;
        }

        else {
                $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
                $log_message = $log_user .' hat versucht eine Aufgabe mit dem Titel "'. $titel .'" zu erstellen. Die Aufgabe konnte jedoch nicht erstellt werden.';
                $log->bindParam(":log_entry", $log_message);
                $log->execute();
                return false;
        }
    }

    function completeTodo($id) {
        $stmt = self::$_db->prepare("UPDATE ". $_SESSION['todo_db'] . " SET folder=:new_folder WHERE id=:id");
        $new_folder = "completed";
        $stmt->bindParam(":new_folder", $new_folder);
        $stmt->bindParam(":id", $id);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function deleteTodo($id) {
        $stmt = self::$_db->prepare("UPDATE ". $_SESSION['todo_db'] . " SET folder=:new_folder WHERE id=:id");
        $new_folder = "deleted";
        $stmt->bindParam(":new_folder", $new_folder);
        $stmt->bindParam(":id", $id);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function recoverTodo($id) {
        $stmt = self::$_db->prepare("UPDATE ". $_SESSION['todo_db'] . " SET folder=:new_folder WHERE id=:id");
        $new_folder = "inbox";
        $stmt->bindParam(":new_folder", $new_folder);
        $stmt->bindParam(":id", $id);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }


    // MESSAGES

    function createMessage($titel, $sender, $zuteilung, $message, $class, $log_user) {
        $stmt = self::$_db->prepare("INSERT INTO messages (title, text, sender, receiver, class) VALUES(:title, :text, :sender, :receiver, :class)");
        $stmt->bindParam(":title", $titel);
        $stmt->bindParam(":text", $message);
        $stmt->bindParam(":sender", $sender);
        $stmt->bindParam(":receiver", $zuteilung);
        $stmt->bindParam(":class", $class);
        if($stmt->execute()) {
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_message = $log_user .' hat eine neue Nachricht mit dem Titel "'. $titel .'" erstellt.';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();

            $notification = self::$_db->prepare("INSERT INTO notifications (user, heading, text) VALUES(:user, :heading, :text)");
            $heading = "Du hast eine neue Mitteilung! 🗪";
            $notify_text = "Schau im Mitteilungsbereich nach!";
            $notification->bindParam(":user", $zuteilung);
            $notification->bindParam(":heading", $heading);
            $notification->bindParam(":text", $notify_text);
            $notification->execute();

            return true;
        }

        else {
                $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
                $log_message = $log_user .' hat versucht eine Nachricht mit dem Titel "'. $titel .'" zu erstellen. Die Nachricht konnte jedoch nicht erstellt werden.';
                $log->bindParam(":log_entry", $log_message);
                $log->execute();
                return false;
        }
    }

    function getAllMessages($currentSelectedClass) {
        $stmt = self::$_db->prepare("SELECT * FROM messages WHERE class=:class ORDER BY date DESC");
        $stmt->bindParam(":class", $currentSelectedClass);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getMessagesForStudents() {
        $stmt = self::$_db->prepare("SELECT * FROM messages WHERE receiver=:receive_user OR receiver=:receive_class ORDER BY date DESC");
        $userFullName = self::getCurrentFullName();
        $stmt->bindParam(":receive_user", $userFullName);
        $userClass = self::getCurrentUserClass();
        $stmt->bindParam(":receive_class", $userClass);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function deleteMessage($id, $titel, $log_user) {
        $stmt = self::$_db->prepare("DELETE FROM messages WHERE id=:id");
        $stmt->bindParam(":id", $id);

        if($stmt->execute()) {
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_message = $log_user .' hat die Nachricht mit dem Titel "'. $titel .'" gelöscht.';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();
            return true;
        } else {
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_message = $log_user .' hat versucht die Nachricht mit dem Titel "'. $titel . '" zu löschen. Der Vorgang konnte nicht erfolgreich abgeschlossen werden.';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();
            return false;
        }
    }

    // Logs

    function getAllLogs() {
        $stmt = self::$_db->prepare("SELECT * FROM logs ORDER BY date DESC");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getAllLogs4Dashboard() {
        $stmt = self::$_db->prepare("SELECT * FROM logs ORDER BY date DESC LIMIT 7");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function deleteAllLogs() {
        $tablename = "logs";
        $log_user = self::getCurrentFullName();
        $stmt = self::$_db->prepare("TRUNCATE TABLE $tablename;");
        if($stmt->execute()) {
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:text)");
            $log_message = $log_user .' hat das Systemprotokoll geleert.';
            $log->bindParam(":text", $log_message);
            $log->execute();
            return true;
        } else {
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:text)");
            $log_message = $log_user .' hat versucht das Systemprotokoll zu leeren. Der Vorgang konnte jedoch nicht abgeschlossen werden.';
            $log->bindParam(":text", $log_message);
            $log->execute();
            return false;
        }
    }

    // ASSIGNMENTS

    function getTeacher() {
        $stmt = self::$_db->prepare("SELECT * FROM users WHERE role='Lehrer' ORDER BY secondName ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getTeacherAndAdmins() {
        $stmt = self::$_db->prepare("SELECT * FROM users WHERE role='Lehrer' OR role='Administrator' ORDER BY secondName ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getTeacherAssignments($id) {
        $stmt = self::$_db->prepare("SELECT * FROM assignments WHERE teacher=:teacher ORDER BY class ASC");
        $stmt->bindParam(":teacher", $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getFullNameViaUsername($username) {
        $stmt = self::$_db->prepare("SELECT firstName, secondName FROM users WHERE username=:username");
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->secondName . ', ' . $result->firstName;
    }

    function getFullNameViaID($id) {
        $stmt = self::$_db->prepare("SELECT firstName, secondName FROM users WHERE id=:id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->secondName . ', ' . $result->firstName;
    }

    function setAssignment($asignment_id) {
        $stmt = self::$_db->prepare("UPDATE ". $_SESSION['assignments_db'] . " SET access='1' WHERE id=:id");
        $stmt->bindParam(":id", $asignment_id);
        if($stmt->execute()) {
            return true;
        }
        else {
            return false;
        }
    }

    function removeAssignment($asignment_id) {
        $stmt = self::$_db->prepare("UPDATE ". $_SESSION['assignments_db'] . " SET access='0' WHERE id=:id");
        $stmt->bindParam(":id", $asignment_id);
        if($stmt->execute()) {
            return true;
        }
        else {
            return false;
        }
    }

    function getAllowedClassesForTeachers() {
        $stmt = self::$_db->prepare("SELECT * FROM assignments WHERE teacher=:teacher AND access='1' ORDER BY class ASC");
        $stmt->bindParam(":teacher", $_SESSION['user_id']);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // EMAIL

    function createEmail($subject, $sender, $to, $message, $class, $log_user) {
        $stmt = self::$_db->prepare("INSERT INTO emails (sender, receiver, subject, text, class, folder, viewer) VALUES(:sender, :receiver, :subject, :text, :class, :folder, :viewer)");
        $stmt->bindParam(":sender", $sender);
        $stmt->bindParam(":receiver", $to);
        $stmt->bindParam(":subject", $subject);
        $stmt->bindParam(":text", $message);
        $stmt->bindParam(":class", $class);
        $folder = 'inbox';
        $stmt->bindParam(":folder", $folder);
        $viewer = 'receiver';
        $stmt->bindParam(":viewer", $viewer);
        if($stmt->execute()) {
            $stmt1 = self::$_db->prepare("INSERT INTO emails (sender, receiver, subject, text, class, folder, opened, viewer) VALUES(:sender, :receiver, :subject, :text, :class, :folder, :opened, :viewer)");
            $stmt1->bindParam(":sender", $sender);
            $stmt1->bindParam(":receiver", $to);
            $stmt1->bindParam(":subject", $subject);
            $stmt1->bindParam(":text", $message);
            $stmt1->bindParam(":class", $class);
            $opened = 1;
            $stmt1->bindParam(":opened", $opened);
            $folder = 'sent';
            $stmt1->bindParam(":folder", $folder);
            $viewer = 'sender';
            $stmt1->bindParam(":viewer", $viewer);
            $stmt1->execute();



            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_message = $log_user .' hat eine neue Email mit dem Betreff "'. $subject .'" verschickt.';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();

            $notification = self::$_db->prepare("INSERT INTO notifications (user, heading, text) VALUES(:user, :heading, :text)");
            $heading = "Du hast eine neue Email! ✉";
            $notify_text = "Schau in deinem Email Postfach nach!";
            $notification->bindParam(":user", $to);
            $notification->bindParam(":heading", $heading);
            $notification->bindParam(":text", $notify_text);
            $notification->execute();

            return true;
        }

        else {
                $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
                $log_message = $log_user .' hat versucht eine Email mit dem Betreff "'. $subject .'" zu verschicken. Die Nachricht konnte jedoch nicht erstellt werden.';
                $log->bindParam(":log_entry", $log_message);
                $log->execute();
                return false;
        }
    }

    function getEmailsFromInbox() {
        $stmt = self::$_db->prepare("SELECT * FROM emails WHERE receiver=:receive_user AND folder='inbox' ORDER BY date DESC");
        $stmt->bindParam(":receive_user", $_SESSION['user_id']);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getSentEmail() {
        $stmt = self::$_db->prepare("SELECT * FROM emails WHERE sender=:sender AND folder='sent' ORDER BY date DESC");
        $stmt->bindParam(":sender", $_SESSION['user_id']);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function openEmail($id) {
        $stmt = self::$_db->prepare("UPDATE emails SET opened='1' WHERE id=:id");
        $stmt->bindParam(":id", $id);
        if($stmt->execute()) {
            return true;
        }
        else {
            return false;
        }
    }

    function deleteEmail($id) {
        $stmt = self::$_db->prepare("DELETE FROM emails WHERE id=:id");
        $stmt->bindParam(":id", $id);
        if($stmt->execute()) {
            return true;
        }
        else {
            return false;
        }
    }


    // FEATURE MANAGMENT

    function enableFeature($feature_id) {
        $stmt = self::$_db->prepare("UPDATE features SET feature_status='1' WHERE id=:id");
        $stmt->bindParam(":id", $feature_id);
        if($stmt->execute()) {
            return true;
        }
        else {
            return false;
        }
    }

    function disableFeature($feature_id) {
        $stmt = self::$_db->prepare("UPDATE features SET feature_status='0' WHERE id=:id");
        $stmt->bindParam(":id", $feature_id);
        if($stmt->execute()) {
            return true;
        }
        else {
            return false;
        }
    }
    
    function getAllSettings() {
        $stmt = self::$_db->prepare("SELECT * FROM settings");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getAllFeatures() {
        $stmt = self::$_db->prepare("SELECT * FROM features");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function isMessageFunktionEnabled() {
        $stmt = self::$_db->prepare("SELECT feature_status FROM features WHERE short=:short");
        $shortForm = "mit_funk";
        $stmt->bindParam(":short", $shortForm);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        if($result->feature_status == "1") {
            return true;
        }
        else {
            return false;
        }
    }

    function isEmailFunktionEnabled() {
        $stmt = self::$_db->prepare("SELECT feature_status FROM features WHERE short=:short");
        $shortForm = "mail_funk";
        $stmt->bindParam(":short", $shortForm);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        if($result->feature_status == "1") {
            return true;
        }
        else {
            return false;
        }
    }

    function isGameFunktionEnabled() {
        $stmt = self::$_db->prepare("SELECT feature_status FROM features WHERE short=:short");
        $shortForm = "game_funk";
        $stmt->bindParam(":short", $shortForm);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        if($result->feature_status == "1") {
            return true;
        }
        else {
            return false;
        }
    }


    function isTodoFunktionEnabled() {
        $stmt = self::$_db->prepare("SELECT feature_status FROM features WHERE short=:short");
        $shortForm = "todo_funk";
        $stmt->bindParam(":short", $shortForm);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        if($result->feature_status == "1") {
            return true;
        }
        else {
            return false;
        }
    }


    function isAssignmentFunktionEnabled() {
        $stmt = self::$_db->prepare("SELECT feature_status FROM features WHERE short=:short");
        $shortForm = "zuord_funk";
        $stmt->bindParam(":short", $shortForm);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        if($result->feature_status == "1") {
            return true;
        }
        else {
            return false;
        }
    }

    function isSubmissionFunktionEnabled() {
        $stmt = self::$_db->prepare("SELECT feature_status FROM features WHERE short=:short");
        $shortForm = "abru_funk";
        $stmt->bindParam(":short", $shortForm);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        if($result->feature_status == "1") {
            return true;
        }
        else {
            return false;
        }
    }

    function getUserSubmissions() {
        $stmt = self::$_db->prepare("SELECT * FROM submissions WHERE owner=:owner AND status='0'");
        $stmt->bindParam(":owner", $_SESSION['user_id']);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getUserSubmissions4Dashboard() {
        $stmt = self::$_db->prepare("SELECT * FROM submissions WHERE owner=:owner AND status='0' LIMIT 7");
        $stmt->bindParam(":owner", $_SESSION['user_id']);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getReviewedUserSubmissions() {
        $stmt = self::$_db->prepare("SELECT * FROM submissions WHERE owner=:owner AND status='1'");
        $stmt->bindParam(":owner", $_SESSION['user_id']);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getAllInboxSubmissions() {
        $stmt = self::$_db->prepare("SELECT * FROM submissions WHERE status='0'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getAllInboxSubmissionsFromClass($class) {
        //Anhand der NutzerID's der Submissions überprüfen, ob die Nutzer in der Klasse sind
        $stmt = self::$_db->prepare("SELECT * FROM submissions WHERE status='0'");
        $stmt->execute();
        $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $submissionsInClass = array();
        foreach($submissions as $submission) {
            $stmt = self::$_db->prepare("SELECT * FROM users WHERE id=:id");
            $stmt->bindParam(":id", $submission['owner']);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if($user['class'] == $class) {
                array_push($submissionsInClass, $submission);
            }
        }
        return $submissionsInClass;
    }

    function getAllInboxSubmissions4Dashboard() {
        $stmt = self::$_db->prepare("SELECT * FROM submissions WHERE status='0' LIMIT 7");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getAllReviewedSubmissions() {
        $stmt = self::$_db->prepare("SELECT * FROM submissions WHERE status='1'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getAllReviewedSubmissionsFromClass($class) {
       //Anhand der NutzerID's der Submissions überprüfen, ob die Nutzer in der Klasse sind
       $stmt = self::$_db->prepare("SELECT * FROM submissions WHERE status='1'");
       $stmt->execute();
       $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
       $submissionsInClass = array();
       foreach($submissions as $submission) {
           $stmt = self::$_db->prepare("SELECT * FROM users WHERE id=:id");
           $stmt->bindParam(":id", $submission['owner']);
           $stmt->execute();
           $user = $stmt->fetch(PDO::FETCH_ASSOC);
           if($user['class'] == $class) {
               array_push($submissionsInClass, $submission);
           }
       }
       return $submissionsInClass;
   }
    

    function getSubmission($submission_id) {
        $stmt = self::$_db->prepare("SELECT * FROM submissions WHERE id=:id");
        $stmt->bindParam(":id", $submission_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function getSubmissionOwner($submission_id) {
        $stmt = self::$_db->prepare("SELECT owner FROM submissions WHERE id=:id");
        $stmt->bindParam(":id", $submission_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->owner;
    }

    function returnSubmission($id, $return_comment, $return_grade) {
        $stmt = self::$_db->prepare("UPDATE submissions SET status='1', return_date=:return_date, return_from=:return_from, return_comment=:return_comment, return_grade=:return_grade WHERE id=:id");
        $stmt->bindParam(":id", $id);
        $return_date = new DateTime();
        $return_date = $return_date->format('Y-m-d H:i:s'); 
        $stmt->bindParam(":return_date", $return_date);
        $stmt->bindParam(":return_comment", $return_comment);
        $stmt->bindParam(":return_grade", $return_grade);
        $stmt->bindParam(":return_from", $_SESSION['user_id']);
        if($stmt->execute()) {

            $notification = self::$_db->prepare("INSERT INTO notifications (user, heading, text) VALUES(:user, :heading, :text)");
            $heading = "Eine deiner Abgaben wurde zurückgegeben! 📂";
            $notify_user = self::getSubmissionOwner($id);
            $notify_text = "Schau bei deinen abgegebenen Arbeiten nach!";
            $notification->bindParam(":user", $notify_user);
            $notification->bindParam(":heading", $heading);
            $notification->bindParam(":text", $notify_text);
            $notification->execute();

            return true;
        }
        else {
            return false;
        }
    }

    function deleteSubmission($id) {
        $stmt = self::$_db->prepare("DELETE FROM submissions WHERE id=:id");
        $stmt->bindParam(":id", $id);
        if($stmt->execute()) {
            return true;
        }
        else {
            return false;
        }
    }

    function createSubmission($submission_name, $filepath) {
        $stmt = self::$_db->prepare("INSERT INTO submissions (owner, name, path) VALUES(:owner, :name, :path)");
        $stmt->bindParam(":owner", $_SESSION['user_id']);
        $stmt->bindParam(":name", $submission_name);
        $stmt->bindParam(":path", $filepath);
        if($stmt->execute()) {
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_user = self::getLogUser();
            $log_message = $log_user .' hat eine neue Abgabe mit dem Titel "'. $submission_name .'" erstellt.';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();
            return true;
        }

        else {
                $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
                $log_message = $log_user .' hat versucht eine Abgabe mit dem Titel "'. $submission_name .'" zu erstellen. Die Abgabe konnte jedoch nicht erstellt werden.';
                $log->bindParam(":log_entry", $log_message);
                $log->execute();
                return false;
        }
    }

    function clearAllSubmissions() {
        $stmt = self::$_db->prepare("DELETE FROM submissions");
        if($stmt->execute()) {
            return true;
        }
        else {
            return false;
        }
    }



    function getLicenseTimeUntilEnd() {
        $stmt = self::$_db->prepare("SELECT license_expires FROM settings");
        $stmt->execute();
        if($result = $stmt->fetch()) {
            $now = new DateTime();
            $expires_at = new DateTime($result['license_expires']);
            $interval = date_diff($now, $expires_at);
            $interval = $interval->format('%a Tage');
            return $interval;
        } else {
            return false;
        }
    }

    function getUserNotifications() {
        $stmt = self::$_db->prepare("SELECT * FROM notifications WHERE user=:user ORDER BY date DESC");
        $stmt->bindParam(":user", $_SESSION['user_id']);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getUserNotifications4Dashboard() {
        $stmt = self::$_db->prepare("SELECT * FROM notifications WHERE user=:user AND status='0' LIMIT 7");
        $stmt->bindParam(":user", $_SESSION['user_id']);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function deleteAllNotifications() {
        $stmt = self::$_db->prepare("DELETE FROM notifications WHERE user=:id");
        $stmt->bindParam(":id", $_SESSION['user_id']);
        if($stmt->execute()) {
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:text)");
            $log_message = $log_user .' hat sein Benachrichtigungsprotokoll geleert.';
            $log->bindParam(":text", $log_message);
            $log->execute();
            return true;
        } else {
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:text)");
            $log_message = $log_user .' hat versucht sein Benachrichtungsprotokoll zu leeren. Der Vorgang konnte jedoch nicht abgeschlossen werden.';
            $log->bindParam(":text", $log_message);
            $log->execute();
            return false;
        }
    }

    function countNewUserNotifications(){
        $stmt = self::$_db->prepare("SELECT * FROM notifications WHERE user=:user AND status='0'");
        $stmt->bindParam(":user", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->rowCount();
        return $result;
    }

    function getNewUserNotifications() {
        $stmt = self::$_db->prepare("SELECT * FROM notifications WHERE user=:user AND status='0' LIMIT 7");
        $stmt->bindParam(":user", $_SESSION['user_id']);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function readAllNotifications() {
        $stmt = self::$_db->prepare("UPDATE notifications SET status='1' WHERE user=:user");
        $stmt->bindParam(":user", $_SESSION['user_id']);
        if($stmt->execute()) {
            return true;
        }
        else {
            return false;
        }
    }

    function allowLogin() {
        $stmt = self::$_db->prepare("UPDATE settings SET allow_login='1'");
        if($stmt->execute()) {
            return true;
        }
        else {
            return false;
        }
    }

    function denyLogin() {
        $stmt = self::$_db->prepare("UPDATE settings SET allow_login='0'");
        if($stmt->execute()) {
            return true;
        }
        else {
            return false;
        }
    }

    function getLicenseStartDate() {
        $stmt = self::$_db->prepare("SELECT license_start FROM settings");
        $stmt->execute();
        if($result = $stmt->fetch()) {
            return $result['license_start'];
        } else {
            return false;
        }
    }

    function getLicenseExpireDate() {
            $stmt = self::$_db->prepare("SELECT license_expires FROM settings");
            $stmt->execute();
            if($result = $stmt->fetch()) {
                return $result['license_expires'];
            } else {
                return false;
            }
    }

    function getAllCompilerLanguages() {
            $stmt = self::$_db->prepare("SELECT * FROM compiler_langs");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getActiveCompilerLanguages() {
        $stmt = self::$_db->prepare("SELECT * FROM compiler_langs WHERE status='1'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    function enableLanguage($language_id) {
        $stmt = self::$_db->prepare("UPDATE compiler_langs SET status='1' WHERE id=:id");
        $stmt->bindParam(":id", $language_id);
        if($stmt->execute()) {
            return true;
        }
        else {
            return false;
        }
    }

    function disableLanguage($language_id) {
        $stmt = self::$_db->prepare("UPDATE compiler_langs SET status='0' WHERE id=:id");
        $stmt->bindParam(":id", $language_id);
        if($stmt->execute()) {
            return true;
        }
        else {
            return false;
        }
    }

    function getUserAvatar($user_id) {
        $stmt = self::$_db->prepare("SELECT avatar FROM users WHERE id=:id");
        $stmt->bindParam(":id", $user_id);
        $stmt->execute();
        if($result = $stmt->fetch()) {
            return $result['avatar'];
        } else {
            return false;
        }
    }

    function changeAvatar($avatar_id, $user_id) {
        $stmt = self::$_db->prepare("UPDATE users SET avatar=:avatar WHERE id=:id");
        $stmt->bindParam(":id", $user_id);
        $stmt->bindParam(":avatar", $avatar_id);
        if($stmt->execute()) {
            return true;
        }
        else {
            return false;
        }
    }

    function getAllGames() {
        $stmt = self::$_db->prepare("SELECT * FROM games");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getAllEnabledGames() {
        $stmt = self::$_db->prepare("SELECT * FROM games WHERE status='1'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getGameNameFromID($game_id) {
        $stmt = self::$_db->prepare("SELECT name FROM games WHERE id=:id");
        $stmt->bindParam(":id", $game_id);
        $stmt->execute();
        if($result = $stmt->fetch()) {
            return $result['name'];
        } else {
            return false;
        }
    }

    function createNewGameSession($game_id, $game_token) {
        $creator = self::getLogUser();
        $created_at = date("Y-m-d H:i:s");
        $stmt = self::$_db->prepare("INSERT INTO game_sessions (game_id, token, session_name, created_at, creator) VALUES(:game_id, :token, :session_name, :created_at, :creator)");
        $stmt->bindParam(":game_id", $game_id);
        $stmt->bindParam(":token", $game_token);
        $stmt->bindParam(":created_at", $created_at);
        $stmt->bindParam(":creator", $creator);

        if($game_id == '1') {
            $game_name = 'CSS Dinner';
            $stmt->bindParam(":session_name", $game_name);
        }
        elseif($game_id == '2') {
            $game_name = 'Elevator Saga';
            $stmt->bindParam(":session_name", $game_name);
        }
        elseif($game_id == '3') {
            $game_name = 'Flexbox Froggy';
            $stmt->bindParam(":session_name", $game_name);
        }
        elseif($game_id == '4') {
            $game_name = 'SQL Murder Mysteries';
            $stmt->bindParam(":session_name", $game_name);
        }
        elseif($game_id == '5') {
            $game_name = 'The Aviator';
            $stmt->bindParam(":session_name", $game_name);
        }
        elseif($game_id == '6') {
            $game_name = 'Grid Garden';
            $stmt->bindParam(":session_name", $game_name);
        }
        elseif($game_id == '7') {
            $game_name = 'Bitburner';
            $stmt->bindParam(":session_name", $game_name);
        }

        if($stmt->execute()) {
            $game_name = self::getGameNameFromID($game_id);
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_user = self::getLogUser();
            $log_message = $log_user .' hat eine neue Sitzung im Spiel "'. $game_name .'" erstellt.';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();
            return true;
        }
        else {
                $game_name = self::getGameNameFromID($game_id);
                $log_user = self::getLogUser();
                $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
                $log_message = $log_user .' hat versucht eine Sitzung im Spiel "'. $game_name .'" zu erstellen. Die Sitzung konnte jedoch nicht erstellt werden.';
                $log->bindParam(":log_entry", $log_message);
                $log->execute();
                return false;
        }
    }        

    function getAllGameSessions() {
        $stmt = self::$_db->prepare("SELECT * FROM game_sessions WHERE creator=:creator");
        $creator = self::getLogUser();
        $stmt->bindParam(":creator", $creator);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function checkGameIDViaToken($game_token) {
        $stmt = self::$_db->prepare("SELECT game_id FROM game_sessions WHERE token=:token");
        $stmt->bindParam(":token", $game_token);
        $stmt->execute();
        if($result = $stmt->fetch()) {
            return $result['game_id'];
        } else {
            return false;
        }
    }

    function checkGameStatusViaToken($game_token) {
        $stmt = self::$_db->prepare("SELECT status FROM game_sessions WHERE token=:token");
        $stmt->bindParam(":token", $game_token);
        $stmt->execute();
        if($result = $stmt->fetch()) {
            return $result['status'];
        } else {
            return false;
        }
    }

    function addRequestNumber($game_token) {
        $stmt = self::$_db->prepare("UPDATE game_sessions SET requests=requests+1 WHERE token=:token");
        $stmt->bindParam(":token", $game_token);
        if($stmt->execute()) {
            return true;
        }
        else {
            return false;
        }
    }

    function stopSession($game_token) {
        $stmt = self::$_db->prepare("DELETE FROM game_sessions WHERE token=:token");
        $stmt->bindParam(":token", $game_token);
        $log_user = self::getLogUser();
        if($stmt->execute()) {
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_message = $log_user .' hat die Spielsitzung mit dem Token "'. $game_token .'" beendet.';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();
            return true;
        } else {
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_message = $log_user .' hat versucht die Spielsitzung mit dem Token "'. $game_token .'" zu beenden. Der Vorgang konnte aufgrund eines Datenbankfehlers nicht abgeschlossen werden. Bitte Support kontaktieren!';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();
            return false;
        }
    }

    function pauseSession($game_token) {
        $stmt = self::$_db->prepare("UPDATE game_sessions SET status='0' WHERE token=:token");
        $stmt->bindParam(":token", $game_token);
        $log_user = self::getLogUser();
        if($stmt->execute()) {
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_message = $log_user .' hat die Spielsitzung mit dem Token "'. $game_token .'" pausiert.';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();
            return true;
        } else {
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_message = $log_user .' hat versucht die Spielsitzung mit dem Token "'. $game_token .'" zu pausieren. Der Vorgang konnte aufgrund eines Datenbankfehlers nicht abgeschlossen werden. Bitte Support kontaktieren!';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();
            return false;
        }
    }

    function continueSession($game_token) {
        $stmt = self::$_db->prepare("UPDATE game_sessions SET status='1' WHERE token=:token");
        $stmt->bindParam(":token", $game_token);
        $log_user = self::getLogUser();
        if($stmt->execute()) {
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_message = $log_user .' hat die Spielsitzung mit dem Token "'. $game_token .'" freigegeben.';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();
            return true;
        } else {
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_message = $log_user .' hat versucht die Spielsitzung mit dem Token "'. $game_token .'" zu freizugeben. Der Vorgang konnte aufgrund eines Datenbankfehlers nicht abgeschlossen werden. Bitte Support kontaktieren!';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();
            return false;
        }
    }

    function renameSession($new_name, $game_token) {
        $stmt = self::$_db->prepare("UPDATE game_sessions SET session_name=:name WHERE token=:token");
        $stmt->bindParam(":name", $new_name);
        $stmt->bindParam(":token", $game_token);
        $log_user = self::getLogUser();
        if($stmt->execute()) {
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_message = $log_user .' hat eine Spielsitzung mit dem Token "'. $game_token .'" in ' . $new_name . 'umbenannt.';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();
            return true;
        } else {
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_message = $log_user .' hat versucht eine Spielsitzung mit dem Token "'. $game_token .'" in ' . $new_name . 'umzubenennen. Der Vorgang konnte aufgrund eines Datenbankfehlers nicht abgeschlossen werden. Bitte Support kontaktieren!';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();
            return false;
        }
    }

    function inviteUser($student_id, $game_token) {
        $stmt = self::$_db->prepare("INSERT INTO invitations (teacher, student, token) VALUES(:teacher, :student, :token)");
        $stmt->bindParam(":token", $game_token);
        $stmt->bindParam(":student", $student_id);
        $teacher = $_SESSION['user_id'];
        $stmt->bindParam(":teacher", $creator);

        if($stmt->execute()) {
            return true;
        }
        else {
            return false;
        }
    }

    function deleteInvtitation($token) {
        $stmt = self::$_db->prepare("DELETE FROM invitations WHERE token=:token");
        $stmt->bindParam(":token", $token);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }


    function checkAssignmentRights() {
        $stmt = self::$_db->prepare("SELECT access FROM assignments WHERE teacher=:current_teacher AND class=:current_class");
        $current_user = $_SESSION['user_id'];
        $current_class = $_SESSION['currentSessionClass'];
        $stmt->bindParam(":current_teacher", $current_user);
        $stmt->bindParam(":current_class", $current_class);
        $stmt->execute();
        if($result = $stmt->fetch()) {
            if($result['access'] == 0) {
                $_SESSION['currentSessionClass'] = "Alle Benutzer und Klassen";
            }
        }
    }

    function renameInstitution($new_name) {
        $stmt = self::$_db->prepare("UPDATE settings SET institution_name=:institution_name");
        $stmt->bindParam(":institution_name", $new_name);
        $log_user = self::getLogUser();
        if($stmt->execute()) {
            $students = self::getAllUsers();
            foreach ($students as $student) {
                $stmt = self::$_db->prepare("UPDATE users SET institution=:institution");
                $stmt->bindParam(":institution", $new_name);
                $stmt->execute();
            }

            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_message = $log_user .' hat die Institution in "'. $new_name .'" umbennant.';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();
            return true;
        } else {
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:log_entry)");
            $log_message = $log_user .' hat versucht die Institution in "'. $new_name .'" umzubenennen. Der Vorgang konnte aufgrund eines Datenbankfehlers nicht abgeschlossen werden. Bitte Support kontaktieren!';
            $log->bindParam(":log_entry", $log_message);
            $log->execute();
            return false;
        }
    }

    function getSubmissionNameViaPath($path) {
        $stmt = self::$_db->prepare("SELECT name FROM submissions WHERE path=:path");
        $stmt->bindParam(":path", $path);
        $stmt->execute();
        if($result = $stmt->fetch()) {
            return $result['name'];
        } else {
            return false;
        }
    }

    function getSubmissionOwnerViaPath($path) {
        $stmt = self::$_db->prepare("SELECT owner FROM submissions WHERE path=:path");
        $stmt->bindParam(":path", $path);
        $stmt->execute();
        if($result = $stmt->fetch()) {
            return $result['owner'];
        } else {
            return false;
        }
    }

    function getSubmissionDateViaPath($path) {
        $stmt = self::$_db->prepare("SELECT date FROM submissions WHERE path=:path");
        $stmt->bindParam(":path", $path);
        $stmt->execute();
        if($result = $stmt->fetch()) {
            return $result['date'];
        } else {
            return false;
        }
    }

    function getSubmissionIDViaPath($path) {
        $stmt = self::$_db->prepare("SELECT id FROM submissions WHERE path=:path");
        $stmt->bindParam(":path", $path);
        $stmt->execute();
        if($result = $stmt->fetch()) {
            return $result['id'];
        } else {
            return false;
        }
    }


    function addOTP($type, $otp_secret) {
        $stmt = self::$_db->prepare("INSERT INTO 2fa (status, type, owner, secret) VALUES(:status, :type, :owner, :secret)");
        $status = "1";
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":type", $type);
        $stmt->bindParam(":owner", $_SESSION['user_id']);
        $stmt->bindParam(":secret", $otp_secret);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function getMFAMethodsFromUser($user_id) {
        $stmt = self::$_db->prepare("SELECT * FROM 2fa WHERE owner=:owner");
        $stmt->bindParam(":owner", $user_id);
        $stmt->execute();
        if($result = $stmt->fetchAll()) {
            return $result;
        } else {
            return NULL;
        }
    }

    function removeMFA($mfa_method_id) {
        $stmt = self::$_db->prepare("DELETE FROM 2fa WHERE id=:id");
        $stmt->bindParam(":id", $mfa_method_id);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function getOTPsecretFromUser() {
        $stmt = self::$_db->prepare("SELECT secret FROM 2fa WHERE owner=:owner AND type='1'");
        $stmt->bindParam(":owner", $_SESSION['user_id']);
        $stmt->execute();
        if($result = $stmt->fetch()) {
            return $result['secret'];
        } else {
            return false;
        }
    }

    function getLoginActivity($user_id) {
        $stmt = self::$_db->prepare("SELECT * FROM last_logins WHERE user=:user ORDER BY login_time DESC LIMIT 5");
        $stmt->bindParam(":user", $user_id);
        $stmt->execute();
        if($result = $stmt->fetchAll()) {
            return $result;
        } else {
            return NULL;
        }
    }

    function getAllUsersWithOutSelf() {
        $stmt = self::$_db->prepare("SELECT * FROM users WHERE id!=:id");
        $stmt->bindParam(":id", $_SESSION['user_id']);
        $stmt->execute();
        if($result = $stmt->fetchAll()) {
            return $result;
        } else {
            return NULL;
        }
    }

    function clearAllTodos() {
        $stmt = self::$_db->prepare("DELETE FROM todos");
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function clearAllMessages() {
        $stmt = self::$_db->prepare("DELETE FROM messages");
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function clearAllNotifications() {
        $stmt = self::$_db->prepare("DELETE FROM notifications");
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function clearAllLogs() {
        $stmt = self::$_db->prepare("DELETE FROM logs");
        if($stmt->execute()) {
            $username = self::getUsernameViaID($_SESSION['user_id']);
            $log_user = self::getFullNameViaUsername($username);
            $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:text)");
            $log_message = $log_user .' hat das Systemprotokoll geleert.';
            $log->bindParam(":text", $log_message);
            $log->execute();
            return true;
        } else {
            return false;
        }
    }

    function clearAllMails() {
        $stmt = self::$_db->prepare("DELETE FROM emails");
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function reinitUser($id, $firstName, $secondName, $class, $role, $username, $password) {
        //Lösche alten Benutzer mit der ID
        $stmt = self::$_db->prepare("DELETE FROM users WHERE id=:id");
        $stmt->bindParam(":id", $id);
        $old_user_id = $id;
        if($stmt->execute()) {
            //Erstelle einen neuen Benutzer mit den übergebenen Daten, ermittle den Parameter für die Institution aus self::getCurrentInstitution()
            $institution = self::getCurrentInstitution();
            $stmt = self::$_db->prepare("INSERT INTO users (first_name, second_name, class, role, institution, avatar, username, password, institution) VALUES(:first_name, :second_name, :class, :role, :institution, :avatar, :username, :password, :institution)");
            $stmt->bindParam(":first_name", $firstName);
            $stmt->bindParam(":second_name", $secondName);
            $stmt->bindParam(":class", $class);
            $stmt->bindParam(":role", $role);
            $stmt->bindParam(":institution", $institution);
            $stmt->bindParam(":avatar", $avatar);
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":password", $password);
            $stmt->bindParam(":institution", $institution);
            if($stmt->execute()) {
                //Erstelle einen Logeintrag
                $log = self::$_db->prepare("INSERT INTO logs (text) VALUES(:text)");
                $log_user = self::getLogUser();
                $log_message = $log_user .' hat den Benutzer '.$firstName.' '.$secondName.' ('.$username.') neu inizialisiert.';
                $log->bindParam(":text", $log_message);
                $log->execute();

                $new_user_id = self::getUserIDViaUsername($username);
                //Ersetze die ID des alten Benutzers mit der ID des neuen Benutzers in allen Tabellen
                $stmt = self::$_db->prepare("UPDATE todos SET receiver=:new_user_id WHERE receiver=:old_user_id");
                $stmt1 = self::$_db->prepare("UPDATE todos SET sender=:new_user_id WHERE sender=:old_user_id");
                $stmt2 = self::$_db->prepare("UPDATE messages SET receiver=:new_user_id WHERE receiver=:old_user_id");
                $stmt3 = self::$_db->prepare("UPDATE messages SET sender=:new_user_id WHERE sender=:old_user_id");
                $stmt4 = self::$_db->prepare("UPDATE notifications SET user=:new_user_id WHERE user=:old_user_id");
                $stmt5 = self::$_db->prepare("UPDATE 2fa SET owner=:new_user_id WHERE owner=:old_user_id");
                $stmt6 = self::$_db->prepare("UPDATE last_logins SET user=:new_user_id WHERE user=:old_user_id");
                $stmt7 = self::$_db->prepare("UPDATE submissions SET owner=:new_user_id WHERE owner=:old_user_id");
                $stmt8 = self::$_db->prepare("UPDATE submissions SET return_from=:new_user_id WHERE return_from=:old_user_id");
                $stmt9 = self::$_db->prepare("UPDATE user_tokens SET user_id=:new_user_id WHERE user_id=:old_user_id");
                $stmt10 = self::$_db->prepare("UPDATE logs SET user=:new_user_id WHERE user=:old_user_id");

                //Funktion zum Erstellen von Benutzerverzeichnisse etc. fehlt noch

                return true;
            } else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    function getMyProjects() {
        $stmt = self::$_db->prepare("SELECT * FROM projects WHERE owner=:owner");
        $stmt->bindParam(":owner", $_SESSION['user_id']);
        $stmt->execute();
        if($result = $stmt->fetchAll()) {
            return $result;
        } else {
            return NULL;
        }
    }

    function getMyUnreviewedProjects() {
        $stmt = self::$_db->prepare("SELECT * FROM projects WHERE reviewed=0 AND owner=:owner AND submitted=1");
        $stmt->bindParam(":owner", $_SESSION['user_id']);
        $stmt->execute();
        if($result = $stmt->fetchAll()) {
            return $result;
        } else {
            return NULL;
        }
    }

    function getMyReviewedProjects() {
        $stmt = self::$_db->prepare("SELECT * FROM projects WHERE reviewed=1 AND owner=:owner AND submitted=1");
        $stmt->bindParam(":owner", $_SESSION['user_id']);
        $stmt->execute();
        if($result = $stmt->fetchAll()) {
            return $result;
        } else {
            return NULL;
        }
    }

       
    function getMyCompletedProjects() {
        $stmt = self::$_db->prepare("SELECT * FROM projects WHERE completed=1 AND owner=:owner");
        $stmt->bindParam(":owner", $_SESSION['user_id']);
        $stmt->execute();
        if($result = $stmt->fetchAll()) {
            return $result;
        } else {
            return NULL;
        }
    }

    function getMyInProgressProjects() {
        $stmt = self::$_db->prepare("SELECT * FROM projects WHERE completed=0 AND owner=:owner");
        $stmt->bindParam(":owner", $_SESSION['user_id']);
        $stmt->execute();
        if($result = $stmt->fetchAll()) {
            return $result;
        } else {
            return NULL;
        }
    }

    function getMySharedProjects() {
        $stmt = self::$_db->prepare("SELECT * FROM projects WHERE shared=1 ");
        $stmt->execute();
        if($result = $stmt->fetchAll()) {
            $momentan_vom_lehrer_ausgewaehlte_klasse = $_SESSION['currentSessionClass'];
            if($momentan_vom_lehrer_ausgewaehlte_klasse == "Alle Benutzer und Klassen") {
                return $result;
            } else {
                $result_array = array();
                foreach($result as $project) {
                    $owner = $project['owner'];
                    $stmt = self::$_db->prepare("SELECT class FROM users WHERE id=:owner");
                    $stmt->bindParam(":owner", $owner);
                    $stmt->execute();
                    $result_owner = $stmt->fetch();
                    //Entferne die Leerzeichen aus result_owner['class'] und momentan_vom_lehrer_ausgewaehlte_klasse
                    if($result_owner['class'] == $momentan_vom_lehrer_ausgewaehlte_klasse) {
                        
                        array_push($result_array, $project);
                    }
                }
                return $result_array;
            }
        } else {
            return NULL;
        }
    }

    function getUnreviewedProjects() {
        $stmt = self::$_db->prepare("SELECT * FROM projects WHERE reviewed=0 AND submitted=1");
        $stmt->execute();

        if($result = $stmt->fetchAll()) {
            $momentan_vom_lehrer_ausgewaehlte_klasse = $_SESSION['currentSessionClass'];
            if($momentan_vom_lehrer_ausgewaehlte_klasse == "Alle Benutzer und Klassen") {
                return $result;
            } else {
                $result_array = array();
                foreach($result as $project) {
                    $owner = $project['owner'];
                    $stmt = self::$_db->prepare("SELECT class FROM users WHERE id=:owner");
                    $stmt->bindParam(":owner", $owner);
                    $stmt->execute();
                    $result_owner = $stmt->fetch();
                    //Entferne die Leerzeichen aus result_owner['class'] und momentan_vom_lehrer_ausgewaehlte_klasse
                    if($result_owner['class'] == $momentan_vom_lehrer_ausgewaehlte_klasse) {
                        
                        array_push($result_array, $project);
                    }
                }
                return $result_array;
            }
        } else {
            return NULL;
        }
    }

    function getReviewedProjects() {
        $stmt = self::$_db->prepare("SELECT * FROM projects WHERE reviewed=1 AND submitted=1");
        $stmt->execute();
        if($result = $stmt->fetchAll()) {
            $momentan_vom_lehrer_ausgewaehlte_klasse = $_SESSION['currentSessionClass'];
            if($momentan_vom_lehrer_ausgewaehlte_klasse == "Alle Benutzer und Klassen") {
                return $result;
            } else {
                $result_array = array();
                foreach($result as $project) {
                    $owner = $project['owner'];
                    $stmt = self::$_db->prepare("SELECT class FROM users WHERE id=:owner");
                    $stmt->bindParam(":owner", $owner);
                    $stmt->execute();
                    $result_owner = $stmt->fetch();
                    //Entferne die Leerzeichen aus result_owner['class'] und momentan_vom_lehrer_ausgewaehlte_klasse
                    if($result_owner['class'] == $momentan_vom_lehrer_ausgewaehlte_klasse) {
                        
                        array_push($result_array, $project);
                    }
                }
                return $result_array;
            }
        } else {
            return NULL;
        }
    }

    function getProjectData($project_id) {
        $stmt = self::$_db->prepare("SELECT * FROM projects WHERE id=:project_id");
        $stmt->bindParam(":project_id", $project_id);
        $stmt->execute();
        if($result = $stmt->fetch()) {
            return $result;
        } else {
            return NULL;
        }
    }

    function returnProject($return_grade, $return_note, $project_id) {
        $stmt = self::$_db->prepare("UPDATE projects SET return_grade=:return_grade, return_note=:return_note, reviewed=1, return_from=:return_from, return_at=:return_at WHERE id=:project_id");
        $stmt->bindParam(":return_grade", $return_grade);
        $stmt->bindParam(":return_note", $return_note);
        $stmt->bindParam(":return_from", $_SESSION['user_id']);
        $return_at = date("Y-m-d H:i:s");
        $stmt->bindParam(":return_at", $return_at);
        $stmt->bindParam(":project_id", $project_id);
        if($stmt->execute()) {
            //Lese project_content aus, dekodiere es und ändere den wert von to_review auf true, dann encodiere es wieder und speichere es in der Datenbank
            $stmt = self::$_db->prepare("SELECT * FROM projects WHERE id=:project_id");
            $stmt->bindParam(":project_id", $project_id);
            $stmt->execute();
            $result = $stmt->fetch();
            $saved_encoded_project_content = $result['project_content'];
            $saved_decoded_project_content = json_decode(urldecode($saved_encoded_project_content), true);
            $saved_decoded_project_content['to_review'] = false;
            $saved_encoded_project_content = rawurlencode(json_encode($saved_decoded_project_content));
            //Speichere den neuen JSON Code in der Datenbank
            if(self::updateProjectCode($project_id, $saved_encoded_project_content)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function deleteProject($project_id) {
        $stmt = self::$_db->prepare("DELETE FROM projects WHERE id=:project_id");
        $stmt->bindParam(":project_id", $project_id);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function changeProjectVisibility($project_id) {
        $stmt = self::$_db->prepare("SELECT * FROM projects WHERE id=:project_id");
        $stmt->bindParam(":project_id", $project_id);
        $stmt->execute();
        if($result = $stmt->fetch()) {
            if($result['shared'] == 0) {
                $stmt = self::$_db->prepare("UPDATE projects SET shared=1 WHERE id=:project_id");
                $stmt->bindParam(":project_id", $project_id);
                if($stmt->execute()) {
                    return true;
                } else {
                    return false;
                }
            } else {
                $stmt = self::$_db->prepare("UPDATE projects SET shared=0 WHERE id=:project_id");
                $stmt->bindParam(":project_id", $project_id);
                if($stmt->execute()) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    function publishProject($project_id) {
        $stmt = self::$_db->prepare("UPDATE projects SET submitted=1, shared=0, submitted_at=:submitted_at WHERE id=:project_id");
        $stmt->bindParam(":project_id", $project_id);
        $submitted_at = date("Y-m-d H:i:s");
        $stmt->bindParam(":submitted_at", $submitted_at);
        if($stmt->execute()) {
            //Lese project_content aus, dekodiere es und ändere den wert von to_review auf true, dann encodiere es wieder und speichere es in der Datenbank
            $stmt = self::$_db->prepare("SELECT * FROM projects WHERE id=:project_id");
            $stmt->bindParam(":project_id", $project_id);
            $stmt->execute();
            $result = $stmt->fetch();
            $saved_encoded_project_content = $result['project_content'];
            $saved_decoded_project_content = json_decode(urldecode($saved_encoded_project_content), true);
            $saved_decoded_project_content['to_review'] = true;
            $saved_encoded_project_content = rawurlencode(json_encode($saved_decoded_project_content));
            //Speichere den neuen JSON Code in der Datenbank
            if(self::updateProjectCode($project_id, $saved_encoded_project_content)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function markProjectAsUncompleted($project_id) {
        $stmt = self::$_db->prepare("UPDATE projects SET completed=0 WHERE id=:project_id");
        $stmt->bindParam(":project_id", $project_id);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function markProjectAsCompleted($project_id) {
        $stmt = self::$_db->prepare("UPDATE projects SET completed=1 WHERE id=:project_id");
        $stmt->bindParam(":project_id", $project_id);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function createProject($project_name, $project_description, $project_visibility, $project_category, $project_content) {
        $stmt = self::$_db->prepare("INSERT INTO projects (name, description, owner, shared, category, created_at, submitted, reviewed, project_content) VALUES (:project_name, :project_description, :owner, :shared, :category, :created_at, :submitted, :reviewed, :project_content)");
        $stmt->bindParam(":project_name", $project_name);
        $stmt->bindParam(":project_description", $project_description);
        $stmt->bindParam(":owner", $_SESSION['user_id']);
        if($project_visibility == "public") {
            $shared = 1;
        } else {
            $shared = 0;
        }
        $stmt->bindParam(":shared", $shared);
        $stmt->bindParam(":category", $project_category);
        $created_at = date("Y-m-d H:i:s");
        $stmt->bindParam(":created_at", $created_at);
        $submitted = 0;
        $stmt->bindParam(":submitted", $submitted);
        $reviewed = 0;
        $stmt->bindParam(":reviewed", $reviewed);
        $stmt->bindParam(":project_content", $project_content);
        if($stmt->execute()) {
            //Ändere den JSON Code in project_content so, dass "identifier" nun den Wert der ID hat
            $stmt = self::$_db->prepare("SELECT * FROM projects WHERE name=:project_name AND description=:project_description AND owner=:owner AND shared=:shared AND category=:category AND project_content=:project_content");
            $stmt->bindParam(":project_name", $project_name);
            $stmt->bindParam(":project_description", $project_description);
            $stmt->bindParam(":owner", $_SESSION['user_id']);
            $stmt->bindParam(":shared", $shared);
            $stmt->bindParam(":category", $project_category);
            $stmt->bindParam(":project_content", $project_content);

            $stmt->execute();
            $result = $stmt->fetch();
            $saved_encoded_project_content = $result['project_content'];
            $saved_decoded_project_content = json_decode(urldecode($saved_encoded_project_content), true);
            if($saved_decoded_project_content['identifier'] == "new") {
                //ändere den JSON Code in project_contet so, dass "identifier" nun den Wert der ID hat
                $saved_decoded_project_content['identifier'] = $result['id'];
                $saved_decoded_project_content['name'] = $project_name;
                $saved_encoded_project_content = rawurlencode(json_encode($saved_decoded_project_content));
                //Speichere den neuen JSON Code in der Datenbank
                if(self::updateProjectCode($result['id'], $saved_encoded_project_content)) {
                    return true;
                } else {
                    return false;
                }   
            }
            else {
                return false;
            }
        } else {
            return false;
        }
    }

    function updateProjectCode($project_id, $project_content) {
        $stmt = self::$_db->prepare("UPDATE projects SET project_content=:project_content WHERE id=:project_id");
        $stmt->bindParam(":project_content", $project_content);
        $stmt->bindParam(":project_id", $project_id);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function createNewProjectThroughClicksave($project_content) {
        //Lege Project Parameter fest (lese aus $project_content im JSON sowohl den project_type, als auch den Namen aus und setzte visibility auf 1)
        $decoded_project_content = json_decode(urldecode($project_content), true);
        $project_name = $decoded_project_content['name'];
        $project_description = "Neues über Editor erstelltes Projekt.";
        $project_visibility = "private";
        $project_category = $decoded_project_content['project_type'];
        $project_code = $project_content;

        //Erstelle das Projekt in der Datenbank
        if(self::createProject($project_name, $project_description, $project_visibility, $project_category, $project_code)) {
            //Ändere den JSON Code in project_content so, dass "identifier" nun den Wert der ID hat
            $stmt = self::$_db->prepare("SELECT id FROM projects WHERE name=:project_name AND description=:project_description AND owner=:owner AND category=:category");
            $stmt->bindParam(":project_name", $project_name);
            $stmt->bindParam(":project_description", $project_description);
            $stmt->bindParam(":owner", $_SESSION['user_id']);
            $stmt->bindParam(":category", $project_category);
            if($stmt->execute()) {
                //Lese die ID des Projekts aus
                $result = $stmt->fetch();
                $project_id = $result['id'];
                return $project_id;
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    function editProjectDescription($project_id, $new_project_description) {
        $stmt = self::$_db->prepare("UPDATE projects SET description=:new_project_description WHERE id=:project_id");
        $stmt->bindParam(":new_project_description", $new_project_description);
        $stmt->bindParam(":project_id", $project_id);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function editProjectName($project_id, $new_name) {
        $stmt = self::$_db->prepare("SELECT project_content FROM projects WHERE id=:project_id");
        $stmt->bindParam(":project_id", $project_id);
        if($stmt->execute()) {
            $result = $stmt->fetch();
            $project_content = $result['project_content'];
            $decoded_project_content = json_decode(urldecode($project_content), true);
            $decoded_project_content['name'] = $new_name;
            $encoded_project_content = rawurlencode(json_encode($decoded_project_content));
            $stmt = self::$_db->prepare("UPDATE projects SET project_content=:project_content WHERE id=:project_id");
            $stmt->bindParam(":project_content", $encoded_project_content);
            $stmt->bindParam(":project_id", $project_id);
            if($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
        else {
            return false;
        }
    }


    function getAllUnReviewedProjectsOfMyAssignedClasses4Dashboard() {
        //Lese aus assignments alle Klassen aus, die dem Lehrer (der aktuellen nutzer id) zugeordnet sind
        //dann lese aus users alle Schüler aus, die in den Klassen sind
        //dann lese aus projects alle Projekte aus, die von den Schülern erstellt wurden
        //dann filtere die Projekte nach reviewed=0 und submitted=1
        //gebe die Anzahl der Projekte zurück
        $stmt = self::$_db->prepare("SELECT * FROM assignments WHERE teacher=:teacher");
        $stmt->bindParam(":teacher", $_SESSION['user_id']);
        $stmt->execute();
        if($result = $stmt->fetchAll()) {
            $result_array = array();
            foreach($result as $assignment) {
                $class = $assignment['class'];
                $stmt = self::$_db->prepare("SELECT * FROM users WHERE class=:class");
                $stmt->bindParam(":class", $class);
                $stmt->execute();
                if($result = $stmt->fetchAll()) {
                    foreach($result as $student) {
                        $student_id = $student['id'];
                        $stmt = self::$_db->prepare("SELECT * FROM projects WHERE owner=:owner AND reviewed=0 AND submitted=1");
                        $stmt->bindParam(":owner", $student_id);
                        $stmt->execute();
                        if($result = $stmt->fetchAll()) {
                            foreach($result as $project) {
                                array_push($result_array, $project);
                            }
                        }
                    }
                }
            }
            return $result_array;
        } else {
            return NULL;
        }
    }

    function getSubmittedProjectsFromUser($user_id) {
        $stmt = self::$_db->prepare("SELECT * FROM projects WHERE owner=:owner AND submitted=1");
        $stmt->bindParam(":owner", $user_id);
        $stmt->execute();
        if($result = $stmt->fetchAll()) {
            return $result;
        } else {
            return NULL;
        }
    }


    // EXAM

    function shouldBeStudentInExam() {
        $stmt = self::$_db->prepare("SELECT exam_redirect FROM users WHERE id=:student_id");
        $stmt->bindParam(":student_id", $_SESSION['user_id']);
        $stmt->execute();
        if($result = $stmt->fetch()) {
            if($result['exam_redirect'] == 1) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function getExamIdFromStudent($student_id) {
        $stmt = self::$_db->prepare("SELECT exam_id FROM users WHERE id=:student_id");
        $stmt->bindParam(":student_id", $student_id);
        $stmt->execute();
        if($result = $stmt->fetch()) {
            return $result['exam_id'];
        } else {
            return NULL;
        }
    }

    function getAllExams() {
        $stmt = self::$_db->prepare("SELECT * FROM exams");
        $stmt->execute();
        if($result = $stmt->fetchAll()) {
            return $result;
        } else {
            return NULL;
        }
    }

    function getExamContent($exam_content_id) {
        $stmt = self::$_db->prepare("SELECT * FROM exam_content WHERE id=:exam_content_id");
        $stmt->bindParam(":exam_content_id", $exam_content_id);
        $stmt->execute();
        if($result = $stmt->fetch()) {
            return $result;
        } else {
            return NULL;
        }
    }

    function getAllExamTemplates() {
        $stmt = self::$_db->prepare("SELECT * FROM exam_content");
        $stmt->execute();
        if($result = $stmt->fetchAll()) {
            return $result;
        } else {
            return NULL;
        }
    }

    function createExamTemplate($examJson, $exam_title, $exam_comment) {
        $stmt = self::$_db->prepare("INSERT INTO exam_content (json_content, title, comment, created_by) VALUES (:json_content, :title, :comment, :created_by)");
        $stmt->bindParam(":json_content", $examJson);
        $stmt->bindParam(":title", $exam_title);
        $stmt->bindParam(":comment", $exam_comment);
        $stmt->bindParam(":created_by", $_SESSION['user_id']);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }


    function planExam($exam_title, $exam_template, $exam_class, $exam_start) {
        $stmt = self::$_db->prepare("INSERT INTO exams (title, exam_content_id, class, planned_for, finished_at, reviewed, token, status, planned_by) VALUES (:title, :exam_content_id, :class, :planned_for, :finished_at, :reviewed, :token, :status, :planned_by)");
        $stmt->bindParam(":title", $exam_title);
        $stmt->bindParam(":exam_content_id", $exam_template);
        $stmt->bindParam(":class", $exam_class);
        $stmt->bindParam(":planned_for", $exam_start);

        //Rufe den Json Code aus der DB mit der template id ab und extrahiere die time aus dem array settings. dann nimm die start zeit und addiere die time dazu. das ist finished_at
        $stmt2 = self::$_db->prepare("SELECT json_content FROM exam_content WHERE id=:exam_content_id");
        $stmt2->bindParam(":exam_content_id", $exam_template);
        $stmt2->execute();
        if ($result = $stmt2->fetch()) {
            $json = json_decode($result['json_content'], true);
            $time = $json['settings']['time'];
            $timeInSeconds = $time * 60; // Umrechnung der Zeitdauer in Sekunden
            $finished_at = date("Y-m-d H:i:s", strtotime($exam_start) + $timeInSeconds);
        } else {
            return NULL;
        }

        $stmt->bindParam(":finished_at", $finished_at);
        $planned_by = $_SESSION['user_id'];
        $stmt->bindParam(":planned_by", $planned_by);
        $reviewed = 0;
        $stmt->bindParam(":reviewed", $reviewed);
        //Token mit maximal 30 zeichen
        $token = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 30);  
        $stmt->bindParam(":token", $token);
        $status = "0";
        $stmt->bindParam(":status", $status);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function updateExamTemplate($examJson, $exam_title, $exam_comment, $exam_id) {
        $stmt = self::$_db->prepare("UPDATE exam_content SET json_content=:json_content, title=:title, comment=:comment, created_by=:created_by WHERE id=:id");
        $stmt->bindParam(":json_content", $examJson);
        $stmt->bindParam(":title", $exam_title);
        $stmt->bindParam(":comment", $exam_comment);
        $stmt->bindParam(":created_by", $_SESSION['user_id']);
        $stmt->bindParam(":id", $exam_id);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function deleteExamTemplate($template_id) {
        $stmt = self::$_db->prepare("DELETE FROM exam_content WHERE id=:id");
        $stmt->bindParam(":id", $template_id);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
   
    function getExamDataByToken($exam_token) {
        $stmt = self::$_db->prepare("SELECT * FROM exams WHERE token=:token");
        $stmt->bindParam(":token", $exam_token);
        $stmt->execute();
        if($result = $stmt->fetch()) {
            return $result;
        } else {
            return NULL;
        }
    }

    function startExam($exam_id) {
        $stmt = self::$_db->prepare("UPDATE exams SET status=:status, started_by=:started_by, countdown_started_at=:countdown_stardet_at, started_at=:started_at WHERE id=:id");
        $status = 1;
        $stmt->bindParam(":status", $status);
        $started_by = $_SESSION['user_id'];
        $stmt->bindParam(":started_by", $started_by);
        $countdown_started_at = date("Y-m-d H:i:s");
        $stmt->bindParam(":countdown_stardet_at", $countdown_started_at);
        $started_at = date("Y-m-d H:i:s");
        $stmt->bindParam(":started_at", $started_at);
        $stmt->bindParam(":id", $exam_id);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function endExam($exam_id) {
        $stmt = self::$_db->prepare("UPDATE exams SET status=:status, finished_at=:finished_at WHERE id=:id");
        $status = 2;
        $stmt->bindParam(":status", $status);
        $finished_at = date("Y-m-d H:i:s");
        $stmt->bindParam(":finished_at", $finished_at);
        $stmt->bindParam(":id", $exam_id);
        if($stmt->execute()) {
            $stmt2 = self::$_db->prepare("SELECT exam_redirect FROM exams WHERE id=:id");
            $stmt2->bindParam(":id", $exam_id);
            $stmt2->execute();
            if($result2 = $stmt2->fetch()) {
                $exam_redirect = $result2['exam_redirect'];
                if($exam_redirect == 1) {
                self::changeExamLobbyState($exam_id);
                }
            return true;
        } else {
            return false;
        }
        }
    }

    function saveExamAnswer($exam_id, $jsonString) {
        $stmt = self::$_db->prepare("UPDATE exams SET answer_json=:answer_json WHERE id=:id");
        $stmt->bindParam(":answer_json", $jsonString);
        $stmt->bindParam(":id", $exam_id);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function isStudentInThisExam($student_id, $exam_id) {
        //nimm den wert von in_exam aus der user tabelle, ist diese 1, mache weiterk, asonsten geben false zurück. dann nimm die spalte exam_id und vergleiche sie mit dem parameter
        $stmt = self::$_db->prepare("SELECT in_exam, exam_id FROM users WHERE id=:id");
        $stmt->bindParam(":id", $student_id);
        $stmt->execute();
        if($result = $stmt->fetch()) {
            if($result['in_exam'] == 1 && $result['exam_id'] == $exam_id) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    function completeExam($exam_id) {
        //Ändere in_exam auf 0 und exam_id auf NULL in der User Tabelle beim aktuellen User
        //Füge in die Tabelle exam_activities einen Eintrag mit der exam_id, dem typ = 1, user=aktueller user, time = timestamp jetzt, title = "[Ermittle vollen Namen via ID] hat die Prüfung beendet.", message = "Der Benutzer [voller Name via ID] hat die Prüfung abgegeben und kann jetzt die Prüfungslobby verlassen. "
        $stmt = self::$_db->prepare("UPDATE users SET in_exam=:in_exam, exam_id=:exam_id, exam_redirect=:exam_redirect, exam_join_timestamp=:exam_join_timestamp WHERE id=:id");
        $in_exam = 0;
        $stmt->bindParam(":in_exam", $in_exam);
        $exam_id = 0;
        $stmt->bindParam(":exam_id", $exam_id);
        $exam_redirect = 0;
        $stmt->bindParam(":exam_redirect", $exam_redirect);
        $exam_join_timestamp = "0000-00-00 00:00:00";
        $stmt->bindParam(":exam_join_timestamp", $exam_join_timestamp);
        $stmt->bindParam(":id", $_SESSION['user_id']);
        if($stmt->execute()) {
            $stmt2 = self::$_db->prepare("INSERT INTO exam_activities (exam, typ, user, time, title, message) VALUES (:exam, :typ, :user, :time, :title, :message)");
            $exam_id = $exam_id;
            $stmt2->bindParam(":exam", $exam_id);
            $type = 1;
            $stmt2->bindParam(":typ", $type);
            $user = $_SESSION['user_id'];
            $stmt2->bindParam(":user", $user);
            $time = date("Y-m-d H:i:s");
            $stmt2->bindParam(":time", $time);
            $title = $this->getFullNameViaID($_SESSION['user_id']) . " hat die Prüfung beendet.";
            $stmt2->bindParam(":title", $title);
            $message = "Der Benutzer " . $this->getFullNameViaID($_SESSION['user_id']) . " hat die Prüfung abgegeben und kann jetzt die Prüfungslobby verlassen.";
            $stmt2->bindParam(":message", $message);
            if($stmt2->execute()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }

    }

    function hasStudentSubmittedExam($student_id, $exam_id) {
        $stmt = self::$_db->prepare("SELECT answer_json FROM exams WHERE id=:id");
        $stmt->bindParam(":id", $exam_id);
        $stmt->execute();
    
        // Holen des Ergebnisses der Abfrage
        if ($result = $stmt->fetch()) {
            $jsonString = $result['answer_json'];
            $jsonArray = json_decode($jsonString, true);
    
            // Überprüfen, ob der Student die Aufgaben abgeschlossen hat
            if (isset($jsonArray[$student_id]['details']['tasks_completed']) &&
                $jsonArray[$student_id]['details']['tasks_completed'] == self::getNumberOfTasksInExam($exam_id)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    function getNumberOfTasksInExam($exam_id) {
        $stmt = self::$_db->prepare("SELECT exam_content_id FROM exams WHERE id=:id");
        $stmt->bindParam(":id", $exam_id);
        $stmt->execute();
    
        if ($result = $stmt->fetch()) {
            $exam_content_id = $result['exam_content_id'];
    
            $stmt2 = self::$_db->prepare("SELECT json_content FROM exam_content WHERE id=:id");
            $stmt2->bindParam(":id", $exam_content_id);
            $stmt2->execute();
    
            if ($result2 = $stmt2->fetch()) {
                $jsonString = $result2['json_content'];
                $jsonArray = json_decode($jsonString, true);
    
                if (isset($jsonArray['tasks']) && is_array($jsonArray['tasks'])) {
                    return count($jsonArray['tasks']);
                } else {
                    return 0;
                }
            }
        }
        return 0;
    }
    
    function joinExam($exam_id) {
        //Setze in_exam auf 1 und exam_id auf die exam_id in der User Tabelle beim aktuellen User
        //Füge in die Tabelle exam_activities einen Eintrag mit der exam_id, dem typ = 1, user=aktueller user, time = timestamp jetzt, title = "[Ermittle vollen Namen via ID] hat die Prüfung betreten.", message = "Der Benutzer [voller Name via ID] hat die Prüfung betreten. "
        $stmt = self::$_db->prepare("UPDATE users SET in_exam=:in_exam, exam_id=:exam_id, exam_join_timestamp=:exam_join_timestamp WHERE id=:id");
        $in_exam = 1;
        $stmt->bindParam(":in_exam", $in_exam);
        $stmt->bindParam(":exam_id", $exam_id);
        $time = date("Y-m-d H:i:s");
        $stmt->bindParam(":exam_join_timestamp", $time);
        $stmt->bindParam(":id", $_SESSION['user_id']);
        if($stmt->execute()) {
            $stmt2 = self::$_db->prepare("INSERT INTO exam_activities (exam, typ, user, time, title, message) VALUES (:exam, :typ, :user, :time, :title, :message)");
            $exam_id = $exam_id;
            $stmt2->bindParam(":exam", $exam_id);
            $type = 1;
            $stmt2->bindParam(":typ", $type);
            $user = $_SESSION['user_id'];
            $stmt2->bindParam(":user", $user);
            $time = date("Y-m-d H:i:s");
            $stmt2->bindParam(":time", $time);
            $title = $this->getFullNameViaID($_SESSION['user_id']) . " hat die Prüfung betreten.";
            $stmt2->bindParam(":title", $title);
            $message = "Der Benutzer " . $this->getFullNameViaID($_SESSION['user_id']) . " hat die Prüfung betreten.";
            $stmt2->bindParam(":message", $message);
            if($stmt2->execute()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function getExamTokenViaID($exam_id) {
        $stmt = self::$_db->prepare("SELECT token FROM exams WHERE id=:id");
        $stmt->bindParam(":id", $exam_id);
        $stmt->execute();
        if($result = $stmt->fetch()) {
            return $result['token'];
        } else {
            return false;
        }
    }

    function changeExamLobbyState($exam_id) {
        //Entnimmt der Tabelle exams den Wert exam_redirect und setzt diesen auf den entgegengesetzten Wert (wenn 0 dann eins, wenn 1 dann null)
        //Suche dir den die klassen_id class bei der die exam_id = exam_id ist
        //Finde mithilfe von getClassNameViaID($class_id) den Namen der Klasse heraus
        //Suche in der users Tabelle alle Einträge mit der Klasse = Klassenname und Rolle = Schüler und ändere exam_redirect auf 1 und exam_id auf die entsprechende exam_id

        $stmt = self::$_db->prepare("SELECT exam_redirect FROM exams WHERE id=:id");
        $stmt->bindParam(":id", $exam_id);
        $stmt->execute();
        if($result = $stmt->fetch()) {
            $exam_redirect = $result['exam_redirect'];
            if($exam_redirect == 0) {
                $exam_redirect = 1;
                $exam_redirect_id = $exam_id;
            } else {
                $exam_redirect = 0;
                $exam_redirect_id = 0;
            }
            $stmt2 = self::$_db->prepare("UPDATE exams SET exam_redirect=:exam_redirect WHERE id=:id");
            $stmt2->bindParam(":exam_redirect", $exam_redirect);
            $stmt2->bindParam(":id", $exam_id);
            if($stmt2->execute()) {
                $stmt3 = self::$_db->prepare("SELECT class FROM exams WHERE id=:id");
                $stmt3->bindParam(":id", $exam_id);
                $stmt3->execute();
                if($result3 = $stmt3->fetch()) {
                    $class = $result3['class'];
                    $class_name = $this->getClassNameViaID($class);
                    //Für jeden Nutzer dieser Klasse mit der Rolle Schüler
                    //Setze exam_redirect auf 1 und exam_id auf die exam_id
                    $users_in_class = self::getAllUsersFromClass($class_name);
                    foreach($users_in_class as $user) {
                        $stmt4 = self::$_db->prepare("UPDATE users SET exam_redirect=:exam_redirect, exam_id=:exam_id WHERE id=:id AND role='Schüler'");
                        $stmt4->bindParam(":exam_redirect", $exam_redirect);
                        $stmt4->bindParam(":exam_id", $exam_redirect_id);
                        $stmt4->bindParam(":id", $user['id']);
                        $stmt4->execute();
                    }
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }


    function renameClass($id, $new_name) {
        $stmt = self::$_db->prepare("UPDATE classes SET name=:new_name WHERE id=:id");
        $stmt->bindParam(":new_name", $new_name);
        $stmt->bindParam(":id", $id);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function editClassDescription($id, $new_description) {
        $stmt = self::$_db->prepare("UPDATE classes SET description=:new_description WHERE id=:id");
        $stmt->bindParam(":new_description", $new_description);
        $stmt->bindParam(":id", $id);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }



}