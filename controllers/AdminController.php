<?php
// ==============================================================================
// CampusXP - AdminController (MVC Controller for OSA Administrator)
// ==============================================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/AdminModel.php';
require_once __DIR__ . '/../models/LogisticsModel.php';

class AdminController {

    private static function checkAdminAuth() {
        $auth_user = get_auth_user();
        if (!$auth_user) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        if ($auth_user['role'] !== 'admin') {
            set_flash('error', 'Access denied. Administrator portal only.');
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        return $auth_user;
    }

    public static function dashboard($conn) {
        $auth_user = self::checkAdminAuth();
        $flash = get_flash();

        $metrics = getSystemDashboardMetrics($conn);
        $all_moderations = getAllModerationRequests($conn);
        $recent_moderations = array_slice($all_moderations, 0, 5);
        $all_logistics = getAllLogisticsRequests($conn);
        $recent_logistics = array_slice($all_logistics, 0, 5);
        $all_booths = getAllBoothAllocations($conn);
        $recent_booths = array_slice($all_booths, 0, 5);
        $all_users = getAllUsers($conn);

        require_once __DIR__ . '/../views/admin/dashboard.view.php';
    }

    public static function moderation($conn) {
        $auth_user = self::checkAdminAuth();
        $flash = get_flash();

        $requests = getAllModerationRequests($conn);

        require_once __DIR__ . '/../views/admin/moderation.view.php';
    }

    public static function moderateAction($conn) {
        $auth_user = self::checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=admin&action=moderation');
            exit;
        }

        $req_id = intval($_POST['request_id'] ?? 0);
        $mod_action = cleanInput($_POST['mod_action'] ?? $_POST['decision'] ?? '');
        $comments = cleanInput($_POST['admin_comments'] ?? '');

        if (!$req_id || !in_array($mod_action, ['Approved', 'Rejected'], true)) {
            set_flash('error', 'Invalid moderation parameters.');
        } else {
            $res = moderateRequest($conn, $req_id, $mod_action, $comments);
            if ($res) {
                set_flash('success', "Campaign marked as '{$mod_action}' successfully.");
            } else {
                set_flash('error', 'Database error updating campaign moderation.');
            }
        }

        header('Location: index.php?controller=admin&action=moderation');
        exit;
    }

    public static function logistics($conn) {
        $auth_user = self::checkAdminAuth();
        $flash = get_flash();

        $requests = getAllLogisticsRequests($conn);

        require_once __DIR__ . '/../views/admin/logistics.view.php';
    }

    public static function logisticsAction($conn) {
        $auth_user = self::checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=admin&action=logistics');
            exit;
        }

        $req_id = intval($_POST['request_id'] ?? 0);
        $new_status = cleanInput($_POST['dispatch_status'] ?? '');
        $admin_notes = cleanInput($_POST['admin_notes'] ?? '');

        if (!$req_id || empty($new_status)) {
            set_flash('error', 'Invalid dispatch parameters.');
        } else {
            $res = updateLogisticsDispatchStatus($conn, $req_id, $new_status, $admin_notes);
            if ($res) {
                set_flash('success', "Requisition status updated to '{$new_status}'.");
            } else {
                set_flash('error', 'Failed to update logistics dispatch.');
            }
        }

        header('Location: index.php?controller=admin&action=logistics');
        exit;
    }

    public static function booths($conn) {
        $auth_user = self::checkAdminAuth();
        $flash = get_flash();

        $booths = getAllBoothAllocations($conn);

        require_once __DIR__ . '/../views/admin/booths.view.php';
    }

    public static function boothAction($conn) {
        $auth_user = self::checkAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=admin&action=booths');
            exit;
        }

        $booth_number = cleanInput($_POST['booth_number'] ?? '');
        $campus_location = cleanInput($_POST['campus_location'] ?? 'Building D Ground Floor');
        $assigned_club = cleanInput($_POST['assigned_club_name'] ?? '');
        $notes = cleanInput($_POST['notes'] ?? '');

        if (empty($booth_number) || empty($assigned_club)) {
            set_flash('error', 'Booth Number and Assigned Club / Recruiter are required.');
        } else {
            $res = saveBoothAllocation($conn, $booth_number, $campus_location, $assigned_club, $notes);
            if ($res) {
                set_flash('success', "Booth '{$booth_number}' allocated to '{$assigned_club}'.");
            } else {
                set_flash('error', 'Failed to allocate booth.');
            }
        }
        header('Location: index.php?controller=admin&action=booths');
        exit;
    }

    public static function allocateBooth($conn) {
        self::boothAction($conn);
    }

    public static function updateLogisticsStatus($conn) {
        self::logisticsAction($conn);
    }
}
