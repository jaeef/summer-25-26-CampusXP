<?php
// ==============================================================================
// CampusXP - RecruiterController (MVC Controller for Corporate Recruiter)
// ==============================================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/DriveModel.php';
require_once __DIR__ . '/../models/RecruiterModel.php';
require_once __DIR__ . '/../models/LogisticsModel.php';

class RecruiterController {

    private static function checkRecruiterAuth() {
        $auth_user = get_auth_user();
        if (!$auth_user) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        if ($auth_user['role'] !== 'recruiter') {
            set_flash('error', 'Access denied. Corporate Recruiter portal only.');
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        return $auth_user;
    }

    public static function dashboard($conn) {
        $auth_user = self::checkRecruiterAuth();
        $flash = get_flash();

        $drives = getCorporateDrivesByRecruiter($conn, $auth_user['id']);
        $shortlists = getRecruiterShortlists($conn, $auth_user['id']);
        $outreach_logs = getRecruiterOutreachLogs($conn, $auth_user['id']);

        $active_drives_count = 0;
        foreach ($drives as $d) {
            if ($d['status'] === 'active') $active_drives_count++;
        }
        $outreach_count = count($outreach_logs);

        require_once __DIR__ . '/../views/recruiter/dashboard.view.php';
    }

    public static function createDrive($conn) {
        self::showCreateDrive($conn);
    }

    public static function showCreateDrive($conn) {
        $auth_user = self::checkRecruiterAuth();
        $flash = get_flash();

        require_once __DIR__ . '/../views/recruiter/create_drive.view.php';
    }

    public static function storeDrive($conn) {
        self::saveDrive($conn);
    }

    public static function saveDrive($conn) {
        $auth_user = self::checkRecruiterAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=recruiter&action=create_drive');
            exit;
        }

        $company_name = cleanInput($_POST['company_name'] ?? '');
        $job_title = cleanInput($_POST['job_title'] ?? '');
        $job_type = cleanInput($_POST['job_type'] ?? 'Full-Time');
        $min_cgpa = floatval($_POST['min_cgpa'] ?? 0.00);
        $deadline = cleanInput($_POST['deadline'] ?? '');
        $requirements = cleanInput($_POST['requirements'] ?? '');

        $raw_labels = $_POST['custom_labels'] ?? $_POST['custom_label'] ?? [];
        $raw_types = $_POST['custom_types'] ?? $_POST['custom_type'] ?? [];

        if (empty($company_name) || empty($job_title) || empty($deadline) || empty($requirements)) {
            set_flash('error', 'Please complete all required drive fields.');
            header('Location: index.php?controller=recruiter&action=create_drive');
            exit;
        }

        // Position Title validation: Must be a string type with only alphabetic letters and spaces (a-z, A-Z)
        if (!preg_match("/^[a-zA-Z\s\-]{2,100}$/", $job_title)) {
            set_flash('error', 'Position Title must be a string containing only alphabetic characters and spaces (a-z, A-Z).');
            header('Location: index.php?controller=recruiter&action=create_drive');
            exit;
        }

        // Filter and sanitize dynamic form questions
        $custom_labels = [];
        $custom_types = [];
        $allowed_types = ['url', 'text', 'textarea'];

        if (is_array($raw_labels) && is_array($raw_types)) {
            for ($i = 0; $i < count($raw_labels); $i++) {
                $lbl = trim(cleanInput($raw_labels[$i] ?? ''));
                $typ = cleanInput($raw_types[$i] ?? 'text');
                if (!empty($lbl)) {
                    $custom_labels[] = $lbl;
                    $custom_types[] = in_array($typ, $allowed_types, true) ? $typ : 'text';
                }
            }
        }

        $driveId = createCorporateDrive($conn, $auth_user['id'], $company_name, $job_title, $job_type, $min_cgpa, $deadline, $requirements, $custom_labels, $custom_types);

        if ($driveId) {
            set_flash('success', "Corporate Drive '{$job_title}' created with dynamic form fields and submitted for OSA Admin approval!");
            header('Location: index.php?controller=recruiter&action=dashboard');
        } else {
            set_flash('error', 'Failed to publish corporate drive. Please try again.');
            header('Location: index.php?controller=recruiter&action=create_drive');
        }
        exit;
    }

    public static function talentSearch($conn) {
        $auth_user = self::checkRecruiterAuth();
        $flash = get_flash();

        $filter_dept = cleanInput($_GET['dept'] ?? '');
        $filter_cgpa = floatval($_GET['min_cgpa'] ?? 0.00);
        $filter_skill = cleanInput($_GET['skill'] ?? '');

        $candidates = searchTalentPool($conn, $filter_dept, $filter_cgpa, $filter_skill);
        $students = $candidates;
        $shortlists = getRecruiterShortlists($conn, $auth_user['id']);

        require_once __DIR__ . '/../views/recruiter/talent_search.view.php';
    }

    public static function shortlists($conn) {
        $auth_user = self::checkRecruiterAuth();
        $flash = get_flash();

        $shortlists = getRecruiterShortlists($conn, $auth_user['id']);

        require_once __DIR__ . '/../views/recruiter/shortlists.view.php';
    }

    public static function createShortlist($conn) {
        self::createBucket($conn);
    }

    public static function createBucket($conn) {
        $auth_user = self::checkRecruiterAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=recruiter&action=talent_search');
            exit;
        }

        $bucket_name = cleanInput($_POST['bucket_name'] ?? '');
        $min_cgpa = floatval($_POST['min_cgpa'] ?? $_POST['filter_cgpa_min'] ?? 0.00);
        $dept = cleanInput($_POST['filter_dept'] ?? 'All');

        if (empty($bucket_name)) {
            set_flash('error', 'Bucket name is required.');
        } else {
            $newId = createShortlistBucket($conn, $auth_user['id'], $bucket_name, $min_cgpa, $dept);
            if ($newId) {
                set_flash('success', "Shortlist bucket '{$bucket_name}' created successfully!");
            } else {
                set_flash('error', 'Failed to create shortlist bucket.');
            }
        }

        header('Location: index.php?controller=recruiter&action=talent_search');
        exit;
    }

    public static function outreach($conn) {
        $auth_user = self::checkRecruiterAuth();
        $flash = get_flash();

        $logs = getRecruiterOutreachLogs($conn, $auth_user['id']);

        require_once __DIR__ . '/../views/recruiter/outreach.view.php';
    }

    public static function sendOutreach($conn) {
        $auth_user = self::checkRecruiterAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=recruiter&action=talent_search');
            exit;
        }

        $shortlist_id = intval($_POST['shortlist_id'] ?? 0);
        $student_id = intval($_POST['student_id'] ?? 0);
        $message = cleanInput($_POST['message'] ?? '');

        if (!$student_id || empty($message)) {
            set_flash('error', 'Please select a candidate and write an invitation message.');
        } else {
            if ($shortlist_id <= 0) {
                $existing = getRecruiterShortlists($conn, $auth_user['id']);
                if (empty($existing)) {
                    $shortlist_id = createShortlistBucket($conn, $auth_user['id'], 'General Verified Talent Pool', 0.00, 'All');
                } else {
                    $shortlist_id = $existing[0]['id'];
                }
            }

            $res = logCandidateOutreach($conn, $shortlist_id, $student_id, $message);
            if ($res) {
                set_flash('success', 'Personalized opportunity invitation dispatched to student dashboard!');
            } else {
                set_flash('error', 'Failed to dispatch candidate outreach.');
            }
        }

        header('Location: index.php?controller=recruiter&action=talent_search');
        exit;
    }

    public static function logistics($conn) {
        $auth_user = self::checkRecruiterAuth();
        $flash = get_flash();

        $requests = getLogisticsRequestsByUser($conn, $auth_user['id']);

        require_once __DIR__ . '/../views/recruiter/logistics.view.php';
    }

    public static function requestLogistics($conn) {
        $auth_user = self::checkRecruiterAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=recruiter&action=logistics');
            exit;
        }

        $event_name = cleanInput($_POST['event_name'] ?? '');
        $item_type = cleanInput($_POST['item_type'] ?? '');
        $quantity = intval($_POST['quantity'] ?? 1);
        $needed_date = cleanInput($_POST['needed_date'] ?? '');
        $return_date = cleanInput($_POST['return_date'] ?? '');
        $notes = cleanInput($_POST['notes'] ?? '');

        if (empty($event_name) || empty($item_type) || empty($needed_date) || empty($return_date)) {
            set_flash('error', 'Please fill in all required campus requisition fields.');
        } else {
            $res = createLogisticsRequest($conn, $auth_user['id'], $event_name, $item_type, $quantity, $needed_date, $return_date, $notes);
            if ($res) {
                set_flash('success', 'Corporate campus booth logistics requisition submitted to OSA Admin!');
            } else {
                set_flash('error', 'Failed to submit requisition.');
            }
        }

        header('Location: index.php?controller=recruiter&action=logistics');
        exit;
    }

    public static function applicants($conn) {
        self::kanban($conn);
    }

    public static function kanban($conn) {
        $auth_user = self::checkRecruiterAuth();
        $flash = get_flash();

        $drives = getCorporateDrivesByRecruiter($conn, $auth_user['id']);
        $selected_drive_id = intval($_GET['drive_id'] ?? 0);

        $pipeline = getCorporateApplicantsForKanban($conn, $auth_user['id'], $selected_drive_id);

        require_once __DIR__ . '/../views/recruiter/kanban.view.php';
    }

    public static function updateStatus($conn) {
        $auth_user = self::checkRecruiterAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=recruiter&action=kanban');
            exit;
        }

        $app_id = intval($_POST['app_id'] ?? 0);
        $new_status = cleanInput($_POST['status'] ?? $_POST['new_status'] ?? '');
        $drive_id = intval($_POST['drive_id'] ?? 0);

        if ($app_id && !empty($new_status)) {
            $res = updateCorporateApplicationStatus($conn, $app_id, $new_status);
            if ($res) {
                set_flash('success', "Candidate status moved to '{$new_status}'.");
            } else {
                set_flash('error', 'Could not update applicant status.');
            }
        }

        header("Location: index.php?controller=recruiter&action=kanban&drive_id={$drive_id}");
        exit;
    }
}
