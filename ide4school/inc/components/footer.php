<?php
if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }
if(!$db->isUserLoggedIn()) {
    header("Location: not_authorized");
}
if(!isset($_SESSION['login_state']) && $db->getMFAMethodsFromUser($_SESSION['user_id']) != NULL) {
    header("Location: 2fa");
}
?>

<!-- BEGIN: Footer-->
<footer class="footer footer-static footer-light">
        <p class="clearfix mb-0"><span class="float-md-start d-block d-md-inline-block mt-25"> &copy; 2023<a class="ms-25" href="https://em-cloud-solutions.de" target="_blank">em CLOUDsolutions</a><span class="d-none d-sm-inline-block">, Alle Rechte vorbehalten. </span>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <!--<span class="d-none d-sm-inline-block"><a href="https://ide4school.com/privacy" target="_blank"> Hilfe </a></span>
        <span class="d-none d-sm-inline-block"><a href="https://ide4school.com/documentation" target="_blank"> Spenden </a></span>-->
        </span>
    <span class="float-md-end d-none d-md-block">ide4school Productive Version <?php echo file_get_contents('version'); ?></span></p>
    </footer>
    <button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
    <!-- END: Footer-->