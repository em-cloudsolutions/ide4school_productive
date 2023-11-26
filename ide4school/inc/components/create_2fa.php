<?php
use OTPHP\TOTP;
require_once 'src/2fa/otphp/vendor/autoload.php';
if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }
if(!$db->isUserLoggedIn()) {
    header("Location: not_authorized");
}
if(!isset($_SESSION['login_state']) && $db->getMFAMethodsFromUser($_SESSION['user_id']) != NULL) {
    header("Location: 2fa");
}
if(isset($_POST['createOTP'])) {
    $otp_secret = $_POST['otp_secret'];
    $otp_code = $_POST['otp_code'];


    $otp = TOTP::createFromSecret($otp_secret); // create TOTP object from the secret.
   
    if($otp->verify($otp_code)) {
        $type = '1';
        if($db->addOTP($type, $otp_secret)) {
            header("Location: securitycenter");
        }
        else {
            echo'
            <div class="alert alert-danger" role="alert">
                                                <h4 class="alert-heading">Fehler</h4>
                                                <div class="alert-body">
                                                2FA konnte nicht eingerichtet werden. Datenbankfehler!
                                                </div>
                                            </div>';
        }
    }
    else {
        echo'
        <div class="alert alert-danger" role="alert">
                                            <h4 class="alert-heading">Fehler</h4>
                                            <div class="alert-body">
                                            2FA konnte nicht eingerichtet werden. OTP Code ist nicht gütlig!
                                            </div>
                                        </div>';
    }
    

}



?>