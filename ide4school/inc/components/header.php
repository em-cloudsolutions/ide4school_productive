<?php
if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }
if(!$db->isUserLoggedIn()) {
    header("Location: not_authorized");
}
if(!isset($_SESSION['login_state']) && $db->getMFAMethodsFromUser($_SESSION['user_id']) != NULL) {
    header("Location: 2fa");
}

$user_informations = $db->getCurrentUserInformations();
$newNotificationsCount = $db->countNewUserNotifications();
$newNotifications = $db->getNewUserNotifications();

?>

<!-- BEGIN: Header-->
<nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow container-xxl">
        <div class="navbar-container d-flex content">
            <div class="bookmark-wrapper d-flex align-items-center">
                <ul class="nav navbar-nav d-xl-none">
                    <li class="nav-item"><a class="nav-link menu-toggle" href="#"><i class="ficon" data-feather="menu"></i></a></li>
                </ul>
                <ul class="nav navbar-nav bookmark-icons">
                <li class="nav-item d-none d-lg-block"><a class="nav-link" href="dashboard" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Dashboard"><i class="ficon" data-feather="home"></i></a></li>
                    <?php
                                    if($db->isEmailFunktionEnabled()) {
                                        echo '<li class="nav-item d-none d-lg-block"><a class="nav-link" href="email" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Email"><i class="ficon" data-feather="mail"></i></a></li>';
                                    }
                                    if($db->isMessageFunktionEnabled()) {
                                        echo ' <li class="nav-item d-none d-lg-block"><a class="nav-link" href="messages" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Mitteilungen"><i class="ficon" data-feather="message-square"></i></a></li>';
                                    }
                                    if($db->isTodoFunktionEnabled()) {
                                        echo '<li class="nav-item d-none d-lg-block"><a class="nav-link" href="todo" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Todo"><i class="ficon" data-feather="check-square"></i></a></li>';
                                    }
                                    ?>
                                                        <li class="nav-item d-none d-lg-block"><a class="nav-link" href="projects" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Projekte"><i class="ficon" data-feather="layers"></i></a></li>
                    </ul>
            </div>
            <ul class="nav navbar-nav align-items-center ms-auto">
                
                <li class="nav-item dropdown dropdown-notification me-25"><a class="nav-link" href="#" data-bs-toggle="dropdown"><i class="ficon" data-feather="bell"></i><span class="badge rounded-pill bg-danger badge-up"><?=$newNotificationsCount?></span></a>
                    <ul class="dropdown-menu dropdown-menu-media dropdown-menu-end">
                        <li class="dropdown-menu-header">
                            <div class="dropdown-header d-flex">
                                <h4 class="notification-title mb-0 me-auto">Benachrichtigungen</h4>
                                <div class="badge rounded-pill badge-light-primary"><?=$newNotificationsCount?> Neue</div>
                            </div>
                        </li>
                        <li class="scrollable-container media-list">
                        <?php
                        foreach($newNotifications as $newNotification) {
                            echo '<a class="d-flex" href="notifications">
                            <div class="list-item d-flex align-items-start">
                                <div class="me-1">
                                    <div class="avatar"><img src="app-assets/images/avatars/'.$db->getUserAvatar($_SESSION['user_id']).'.png" alt="avatar" width="32" height="32"></div>
                                </div>
                                <div class="list-item-body flex-grow-1">
                                    <p class="media-heading"><span class="fw-bolder">' . $newNotification['heading'] . '</span></p><small class="notification-text">' . $newNotification['text'] . '</small>
                                </div>
                            </div>
                        </a>';
                        }    
                        ?>
                        </li><a>
                        <li class="dropdown-menu-footer"><a class="btn btn-primary w-100" href="notifications">Alle Benachrichtigungen ansehen</a></li>
                        
                    </ul>

                </li>
                <li class="nav-item dropdown dropdown-user"><a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="user-nav d-sm-flex d-none"><span class="user-name fw-bolder"><?php echo $user_informations['firstName'] . " " . $user_informations['secondName'];?></span><span class="user-status"><?=$user_informations['class']?></span></div><span class="avatar"><img class="round" src="app-assets/images/avatars/<?php echo($db->getUserAvatar($_SESSION['user_id']))?>.png" alt="avatar" height="40" width="40"><span class="avatar-status-online"></span></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
                        <a class="dropdown-item" href="securitycenter"><i class="me-50" data-feather="lock"></i> Sicherheit</a>
                        <a class="dropdown-item" href="change-avatar"><i class="me-50" data-feather="eye"></i> Avatar ändern</a>
                        <?php
                        
                        if($db->isEmailFunktionEnabled()) {
                    echo '<a class="dropdown-item" href="email"><i class="me-50" data-feather="mail"></i> Email</a>';
                }

                if($db->isMessageFunktionEnabled()) {
                    echo'<a class="dropdown-item" href="messages"><i class="me-50" data-feather="message-square"></i> Mitteilungen</a>';
                }

                if($db->isTodoFunktionEnabled()) {
                    echo ' <a class="dropdown-item" href="todo"><i class="me-50" data-feather="check-square"></i> Todo</a>';
                }
                ?>
                        
                       
                        
                        <div class="dropdown-divider"></div><a class="dropdown-item" href="logout"><i class="me-50" data-feather="power"></i> Abmelden</a>
                    </div>
          
                </li>
                
            </ul>
        </div>
    </nav>
    
    <!-- END: Header-->