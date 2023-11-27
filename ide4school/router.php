<?php if (!defined('IN_SITE')) { echo "Zugriff verweigert!"; die(); }
switch($page)
{
    case "dashboard":
        include("inc/apps/dashboard.php");
        break;

    case "classes":
        include("inc/apps/classlist.php");
        break;

    case "login":
        include("inc/components/login.php");
        break;

    case "logout":
        include("inc/components/logout.php");
        break;

    case "not_authorized":
        include("inc/components/not_authorized.php");
        break;

    case "maintenance":
        include("inc/components/maintenance.php");
        break;
    
    case "login_deactivated":
        include("inc/components/login_deactivated.php");
        break;

    case "focus_lobby":
        include("inc/components/focus_lobby.php");
        break;

    case "email":
        include("inc/apps/email.php");
        break;

    case "messages":
        include("inc/apps/messages.php");
        break;
        
    case "todo":
        include("inc/apps/todo.php");
        break;

    case "users":
        include("inc/apps/userlist.php");
        break;

    case "user":
        include("inc/apps/userview.php");
        break;

    case "disk":
        include("inc/apps/files.php");
        break;

    case "assignments":
        include("inc/apps/assignments.php");
        break;

    case "settings":
        include("inc/apps/settings.php");
        break;

    case "feature_not_active":
        include("inc/components/feature_not_active.php");
        break;

    case "submissions":
        include("inc/apps/submissionlist.php");
        break;

    case "submission":
        include("inc/apps/submissionview.php");
        break;

    case "logs":
        include("inc/apps/logs.php");
        break;

    case "notifications":
        include("inc/apps/notifications.php");
        break;

    case "ide":
        include("inc/apps/ide.php");
        break;

    case "clicksaverefresh":
        include("inc/components/clicksave_refresh.php");
        break;

    case "first-login":
        include("inc/apps/firstLogin.php");
        break;

    case "change-avatar":
        include("inc/apps/changeAvatar.php");
        break;

    case "games":
        include("inc/apps/game_overview.php");
        break;

    case "game_manager":
        include("inc/apps/game_manager.php");
        break;

    case "game":
        include("inc/apps/game.php");
        break;

    case "fileshare":
        include("inc/apps/file_share.php");
        break;

    case "struktogrammeditor":
        include("core/struktogrammeditor/index.php");
        break;

    case "securitycenter":
        include("inc/apps/securitycenter.php");
        break;

    case "create2fa":
        include("inc/components/create_2fa.php");
        break;

    case "2fa":
        include("inc/components/2fa.php");
        break;

    case "housekeeping":
        include("inc/apps/housekeeping.php");
        break;

    case "projects":
        include("inc/apps/projectlist.php");
        break;

    case "project":
        include("inc/apps/projectview.php");
        break;

    case "preview":
        include("inc/apps/projectpreview.php");
        break;

    case "exams":
        include("inc/apps/examlist.php");
        break;

    case "exam":
        include("inc/apps/examview.php");
        break;

    case "exam_creation_assistant":
        include("inc/apps/exam_creation_assistant.php");
        break;

    case "exam_library":
        include("inc/apps/exam_library.php");
        break;

    default:
        include("inc/components/not_found.php");
        break;
}
?>