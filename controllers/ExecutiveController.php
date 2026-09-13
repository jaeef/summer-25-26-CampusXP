<?php
// ==============================================================================
// CampusXP - ExecutiveController (MVC Controller for Club Executive Role)
// ==============================================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/DriveModel.php';
require_once __DIR__ . '/../models/ApplicationModel.php';
require_once __DIR__ . '/../models/InterviewModel.php';
require_once __DIR__ . '/../models/LogisticsModel.php';

class ExecutiveController {

    private static function checkExecAuth() {
        $auth_user = get_auth_user();
        if (!$auth_user) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        if ($auth_user['role'] !== 'club_exec') {
            set_flash('error', 'Access denied. Club Executive portal only.');
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        return $auth_user;
    }

    public static function dashboard($conn) {
        $auth_user = self::checkExecAuth();
        $flash = get_flash();

        $stats = getExecutiveAppStats($conn, $auth_user['id']);
        $drives = getClubDrivesByExecutive($conn, $auth_user['id']);
        $recent_apps = getClubApplicantsForAnalytics($conn, 0);

        require_once __DIR__ . '/../views/executive/dashboard.view.php';
    }

    public static function createDrive($conn) {
        $auth_user = self::checkExecAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=executive&action=dashboard');
            exit;
        }

        $club_name = cleanInput($_POST['club_name'] ?? '');
        $title = cleanInput($_POST['title'] ?? '');
        $category = cleanInput($_POST['category'] ?? 'recruitment');
        $requirements = cleanInput($_POST['requirements'] ?? '');
        $deadline = cleanInput($_POST['deadline'] ?? '');
        $location = cleanInput($_POST['location'] ?? 'AIUB Campus');

        if (empty($club_name) || empty($title) || empty($requirements) || empty($deadline)) {
            set_flash('error', 'Please fill in all required fields for the club drive.');
            header('Location: index.php?controller=executive&action=dashboard');
            exit;
        }

        $driveId = createClubDrive($conn, $auth_user['id'], $club_name, $title, $category, $requirements, $deadline, $location);

        if ($driveId) {
            set_flash('success', "Recruitment drive '{$title}' created and submitted to OSA Admin for approval!");
        } else {
            set_flash('error', 'Failed to create recruitment drive. Please try again.');
        }

        header('Location: index.php?controller=executive&action=dashboard');
        exit;
    }

    public static function kanban($conn) {
        $auth_user = self::checkExecAuth();
        $flash = get_flash();

        $all_drives = getActiveClubDrives($conn);
        if (empty($all_drives)) {
            $all_drives = getClubDrivesByExecutive($conn, $auth_user['id']);
        }

        $selected_drive_id = intval($_GET['drive_id'] ?? ($all_drives[0]['id'] ?? 0));
        $pipeline = $selected_drive_id ? getClubApplicantsForKanban($conn, $selected_drive_id) : [
            'Applied' => [], 'Under Review' => [], 'Interview' => [], 'Selected' => [], 'Rejected' => []
        ];

        require_once __DIR__ . '/../views/executive/kanban.view.php';
    }

    public static function updateStatus($conn) {
        $auth_user = self::checkExecAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=executive&action=kanban');
            exit;
        }

        $app_id = intval($_POST['app_id'] ?? 0);
        $new_status = cleanInput($_POST['status'] ?? $_POST['new_status'] ?? '');
        $drive_id = intval($_POST['drive_id'] ?? 0);

        if ($app_id && !empty($new_status)) {
            $res = updateApplicationStatus($conn, $app_id, $new_status);
            if ($res) {
                set_flash('success', "Candidate status updated to '{$new_status}'.");
            } else {
                set_flash('error', 'Could not update applicant status.');
            }
        }

        header("Location: index.php?controller=executive&action=kanban&drive_id={$drive_id}");
        exit;
    }

    public static function scheduler($conn) {
        $auth_user = self::checkExecAuth();
        $flash = get_flash();

        $all_drives = getActiveClubDrives($conn);
        if (empty($all_drives)) {
            $all_drives = getClubDrivesByExecutive($conn, $auth_user['id']);
        }

        $selected_drive_id = intval($_GET['drive_id'] ?? ($all_drives[0]['id'] ?? 0));
        $slots = getInterviewSlotsByDrive($conn, $selected_drive_id);
        $candidates = getCandidatesForInterview($conn, $selected_drive_id);
        $unassigned_cands = $candidates;

        require_once __DIR__ . '/../views/executive/scheduler.view.php';
    }

    public static function createSlot($conn) {
        $auth_user = self::checkExecAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=executive&action=scheduler');
            exit;
        }

        $drive_id = intval($_POST['drive_id'] ?? 0);
        $venue_room = cleanInput($_POST['venue_room'] ?? '');
        $slot_time = cleanInput($_POST['slot_time'] ?? '');

        if (!$drive_id || empty($venue_room) || empty($slot_time)) {
            set_flash('error', 'Please fill in all slot details.');
        } else {
            $res = createInterviewSlot($conn, $drive_id, $venue_room, $slot_time);
            if ($res) {
                set_flash('success', 'Interview slot generated successfully.');
            } else {
                set_flash('error', 'Failed to generate interview slot.');
            }
        }

        header("Location: index.php?controller=executive&action=scheduler&drive_id={$drive_id}");
        exit;
    }

    public static function assignSlot($conn) {
        $auth_user = self::checkExecAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=executive&action=scheduler');
            exit;
        }

        $slot_id = intval($_POST['slot_id'] ?? 0);
        $app_id = intval($_POST['app_id'] ?? 0);
        $drive_id = intval($_POST['drive_id'] ?? 0);

        if ($slot_id && $app_id) {
            $res = assignCandidateToSlot($conn, $slot_id, $app_id);
            if ($res) {
                set_flash('success', 'Candidate allocated to interview slot.');
            } else {
                set_flash('error', 'Failed to assign candidate.');
            }
        }

        header("Location: index.php?controller=executive&action=scheduler&drive_id={$drive_id}");
        exit;
    }

    public static function clearSlot($conn) {
        $auth_user = self::checkExecAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=executive&action=scheduler');
            exit;
        }

        $slot_id = intval($_POST['slot_id'] ?? 0);
        $drive_id = intval($_POST['drive_id'] ?? 0);

        if ($slot_id) {
            $res = releaseInterviewSlot($conn, $slot_id);
            if ($res) {
                set_flash('success', 'Interview slot cleared and released.');
            } else {
                set_flash('error', 'Failed to clear interview slot.');
            }
        }

        header("Location: index.php?controller=executive&action=scheduler&drive_id={$drive_id}");
        exit;
    }

    public static function logistics($conn) {
        $auth_user = self::checkExecAuth();
        $flash = get_flash();

        $requests = getLogisticsRequestsByUser($conn, $auth_user['id']);

        require_once __DIR__ . '/../views/executive/logistics.view.php';
    }

    public static function requestLogistics($conn) {
        $auth_user = self::checkExecAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=executive&action=logistics');
            exit;
        }

        $event_name = cleanInput($_POST['event_name'] ?? '');
        $item_type = cleanInput($_POST['item_type'] ?? '');
        $quantity = intval($_POST['quantity'] ?? 1);
        $needed_date = cleanInput($_POST['needed_date'] ?? '');
        $return_date = cleanInput($_POST['return_date'] ?? '');
        $notes = cleanInput($_POST['notes'] ?? '');

        if (empty($event_name) || empty($item_type) || empty($needed_date) || empty($return_date)) {
            set_flash('error', 'Please fill in all required equipment request fields.');
        } else {
            $res = createLogisticsRequest($conn, $auth_user['id'], $event_name, $item_type, $quantity, $needed_date, $return_date, $notes);
            if ($res) {
                set_flash('success', 'Logistics requisition submitted to OSA Admin!');
            } else {
                set_flash('error', 'Failed to submit requisition.');
            }
        }

        header('Location: index.php?controller=executive&action=logistics');
        exit;
    }

    public static function analytics($conn) {
        $auth_user = self::checkExecAuth();
        $flash = get_flash();

        $all_drives = getActiveClubDrives($conn);
        if (empty($all_drives)) {
            $all_drives = getClubDrivesByExecutive($conn, $auth_user['id']);
        }

        $selected_drive_id = intval($_GET['drive_id'] ?? 0);
        $applicants = getClubApplicantsForAnalytics($conn, $selected_drive_id);

        require_once __DIR__ . '/../views/executive/analytics.view.php';
    }
}
