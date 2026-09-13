<?php
// ==============================================================================
// CampusXP - Main MVC Front Controller (Single Entry Point)
// ==============================================================================

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/StudentController.php';
require_once __DIR__ . '/controllers/ExecutiveController.php';
require_once __DIR__ . '/controllers/RecruiterController.php';
require_once __DIR__ . '/controllers/AdminController.php';

$controller = cleanInput($_GET['controller'] ?? '');
$action = cleanInput($_GET['action'] ?? '');
$auth_user = get_auth_user();

if (empty($controller)) {
    if ($auth_user) {
        switch ($auth_user['role']) {
            case 'student':
                header('Location: index.php?controller=student&action=dashboard');
                break;
            case 'club_exec':
                header('Location: index.php?controller=executive&action=dashboard');
                break;
            case 'recruiter':
                header('Location: index.php?controller=recruiter&action=dashboard');
                break;
            case 'admin':
                header('Location: index.php?controller=admin&action=dashboard');
                break;
            default:
                header('Location: index.php?controller=auth&action=login');
                break;
        }
    } else {
        header('Location: index.php?controller=auth&action=login');
    }
    exit;
}

// Router Switcher
switch ($controller) {
    case 'auth':
        switch ($action) {
            case 'login':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    AuthController::login($conn);
                } else {
                    AuthController::showLogin($conn);
                }
                break;
            case 'register':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    AuthController::register($conn);
                } else {
                    AuthController::showRegister($conn);
                }
                break;
            case 'logout':
                AuthController::logout($conn);
                break;
            default:
                AuthController::showLogin($conn);
                break;
        }
        break;

    case 'student':
        switch ($action) {
            case 'dashboard':
                StudentController::dashboard($conn);
                break;
            case 'profile':
                StudentController::profile($conn);
                break;
            case 'update_profile':
                StudentController::updateProfile($conn);
                break;
            case 'applications':
                StudentController::applications($conn);
                break;
            case 'apply':
                StudentController::apply($conn);
                break;
            case 'bookmark':
                StudentController::bookmark($conn);
                break;
            default:
                StudentController::dashboard($conn);
                break;
        }
        break;

    case 'executive':
        switch ($action) {
            case 'dashboard':
                ExecutiveController::dashboard($conn);
                break;
            case 'create_drive':
                ExecutiveController::createDrive($conn);
                break;
            case 'kanban':
                ExecutiveController::kanban($conn);
                break;
            case 'update_status':
                ExecutiveController::updateStatus($conn);
                break;
            case 'scheduler':
                ExecutiveController::scheduler($conn);
                break;
            case 'generate_slots':
                ExecutiveController::generateSlots($conn);
                break;
            case 'analytics':
                ExecutiveController::analytics($conn);
                break;
            case 'evaluate':
                ExecutiveController::evaluate($conn);
                break;
            case 'logistics':
                ExecutiveController::logistics($conn);
                break;
            case 'request_logistics':
                ExecutiveController::requestLogistics($conn);
                break;
            default:
                ExecutiveController::dashboard($conn);
                break;
        }
        break;

    case 'recruiter':
        switch ($action) {
            case 'dashboard':
                RecruiterController::dashboard($conn);
                break;
            case 'create_drive':
                RecruiterController::createDrive($conn);
                break;
            case 'save_drive':
            case 'store_drive':
                RecruiterController::storeDrive($conn);
                break;
            case 'talent_search':
                RecruiterController::talentSearch($conn);
                break;
            case 'create_bucket':
            case 'create_shortlist':
                RecruiterController::createShortlist($conn);
                break;
            case 'shortlists':
                RecruiterController::shortlists($conn);
                break;
            case 'outreach':
                RecruiterController::outreach($conn);
                break;
            case 'send_outreach':
                RecruiterController::sendOutreach($conn);
                break;
            case 'kanban':
            case 'applicants':
                RecruiterController::kanban($conn);
                break;
            case 'update_status':
                RecruiterController::updateStatus($conn);
                break;
            case 'logistics':
                RecruiterController::logistics($conn);
                break;
            case 'request_logistics':
                RecruiterController::requestLogistics($conn);
                break;
            default:
                RecruiterController::dashboard($conn);
                break;
        }
        break;

    case 'admin':
        switch ($action) {
            case 'dashboard':
                AdminController::dashboard($conn);
                break;
            case 'moderation':
                AdminController::moderation($conn);
                break;
            case 'moderate_action':
                AdminController::moderateAction($conn);
                break;
            case 'booths':
                AdminController::booths($conn);
                break;
            case 'booth_action':
            case 'allocate_booth':
                AdminController::allocateBooth($conn);
                break;
            case 'logistics':
                AdminController::logistics($conn);
                break;
            case 'logistics_action':
            case 'update_logistics_status':
                AdminController::updateLogisticsStatus($conn);
                break;
            default:
                AdminController::dashboard($conn);
                break;
        }
        break;

    default:
        header('Location: index.php?controller=auth&action=login');
        exit;
}
