<?php
// ==============================================================================
// CampusXP - StudentController (MVC Controller for Student Role)
// ==============================================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/StudentModel.php';
require_once __DIR__ . '/../models/DriveModel.php';
require_once __DIR__ . '/../models/ApplicationModel.php';
require_once __DIR__ . '/../models/RecruiterModel.php';

class StudentController {

    private static function checkStudentAuth() {
        $auth_user = get_auth_user();
        if (!$auth_user) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        if ($auth_user['role'] !== 'student') {
            set_flash('error', 'Access denied. Student portal only.');
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        return $auth_user;
    }

    public static function dashboard($conn) {
        $auth_user = self::checkStudentAuth();
        $flash = get_flash();

        $profile = getStudentProfileByUserId($conn, $auth_user['id']);
        if (!$profile) {
            // Auto initialize profile if none exists
            $new_profile_id = saveStudentProfile($conn, $auth_user['id'], '22-' . rand(10000, 99999) . '-1', 'CSE', 0.00, '', '', 'I am a motivated AIUB undergraduate looking to apply my academic foundation, build practical projects, and collaborate with inspiring teams.');
            $profile = getStudentProfileByUserId($conn, $auth_user['id']);
        }

        $club_drives = getActiveClubDrives($conn);
        $corp_drives = getActiveCorpDrives($conn);
        foreach ($corp_drives as &$cd) {
            $cd['custom_fields'] = getCustomFormFieldsByDrive($conn, $cd['id']);
        }
        unset($cd);

        $alerts = getDirectAlertsByStudentId($conn, $auth_user['id']);

        $club_apps = getClubApplicationsByStudent($conn, $auth_user['id']);
        $corp_apps = getCorpApplicationsByStudent($conn, $auth_user['id']);
        $total_apps = count($club_apps) + count($corp_apps);

        $applied_club_drive_ids = !empty($club_apps) ? array_column($club_apps, 'drive_id') : [];
        $applied_corp_drive_ids = !empty($corp_apps) ? array_column($corp_apps, 'corporate_drive_id') : [];

        $bookmarked_map = getStudentBookmarks($conn, $auth_user['id']);
        $saved_count = count($bookmarked_map);

        require_once __DIR__ . '/../views/student/dashboard.view.php';
    }

    public static function profile($conn) {
        $auth_user = self::checkStudentAuth();
        $flash = get_flash();

        $user = getUserById($conn, $auth_user['id']);
        $profile = getStudentProfileByUserId($conn, $auth_user['id']);
        if (!$profile) {
            saveStudentProfile($conn, $auth_user['id'], '22-' . rand(10000, 99999) . '-1', 'CSE', 0.00, '', '', 'I am a motivated AIUB undergraduate looking to apply my academic foundation, build practical projects, and collaborate with inspiring teams.');
            $profile = getStudentProfileByUserId($conn, $auth_user['id']);
        }

        require_once __DIR__ . '/../views/student/profile.view.php';
    }

    public static function updateProfile($conn) {
        $auth_user = self::checkStudentAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=student&action=profile');
            exit;
        }

        $existingProfile = getStudentProfileByUserId($conn, $auth_user['id']);
        // Keep existing permanent AIUB ID and Department (cannot be modified)
        $aiub_id = $existingProfile['aiub_id'] ?? '';
        $department = $existingProfile['department'] ?? 'CSE';
        $cgpa = floatval($_POST['cgpa'] ?? 0);
        $skills = cleanInput($_POST['skills'] ?? '');
        $cv_url = cleanInput($_POST['cv_url'] ?? '');
        $default_sop = cleanInput($_POST['default_sop'] ?? '');

        if ($cgpa < 0 || $cgpa > 4.00) {
            set_flash('error', 'CGPA must be between 0.00 and 4.00.');
            header('Location: index.php?controller=student&action=profile');
            exit;
        }

        if (!empty($cv_url) && !filter_var($cv_url, FILTER_VALIDATE_URL)) {
            set_flash('error', 'Please provide a valid resume/CV cloud URL (Google Drive, GitHub, etc.).');
            header('Location: index.php?controller=student&action=profile');
            exit;
        }

        if (empty($default_sop)) {
            $default_sop = 'I am a motivated AIUB undergraduate looking to apply my academic foundation, build practical projects, and collaborate with inspiring teams.';
        }

        // Save
        $saved = saveStudentProfile($conn, $auth_user['id'], $aiub_id, $department, $cgpa, $skills, $cv_url, $default_sop);

        if ($saved) {
            $_SESSION['user']['aiub_id'] = $aiub_id;
            $_SESSION['user']['cgpa'] = $cgpa;
            $_SESSION['user']['cv_url'] = $cv_url;
            $_SESSION['user']['default_sop'] = $default_sop;
            set_flash('success', 'Profile & SOP Vault updated successfully!');
        } else {
            set_flash('error', 'Database error while saving profile.');
        }

        header('Location: index.php?controller=student&action=profile');
        exit;
    }

    public static function applications($conn) {
        $auth_user = self::checkStudentAuth();
        $flash = get_flash();

        $club_apps = getClubApplicationsByStudent($conn, $auth_user['id']);
        $corp_apps = getCorpApplicationsByStudent($conn, $auth_user['id']);

        require_once __DIR__ . '/../views/student/applications.view.php';
    }

    public static function apply($conn) {
        $auth_user = self::checkStudentAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=student&action=dashboard');
            exit;
        }

        $drive_id = intval($_POST['drive_id'] ?? 0);
        $drive_type = cleanInput($_POST['drive_type'] ?? 'club');
        $applied_role = cleanInput($_POST['applied_role'] ?? 'General Member');
        $custom_sop = cleanInput($_POST['custom_sop'] ?? $_POST['statement_of_purpose'] ?? '');
        $cv_url = cleanInput($_POST['cv_url'] ?? $_POST['cv_link'] ?? '');

        if (!$drive_id) {
            set_flash('error', 'Invalid opportunity drive selection.');
            header('Location: index.php?controller=student&action=dashboard');
            exit;
        }

        $profile = getStudentProfileByUserId($conn, $auth_user['id']);
        if (!$profile) {
            saveStudentProfile($conn, $auth_user['id'], '22-' . rand(10000, 99999) . '-1', 'CSE', 0.00, '', 'https://drive.google.com/file/d/cv/view', 'AIUB student profile initialized.');
            $profile = getStudentProfileByUserId($conn, $auth_user['id']);
        }

        if (empty($cv_url)) {
            $cv_url = $profile['cv_url'] ?? 'https://drive.google.com/file/d/cv/view';
        }
        if (empty($custom_sop)) {
            $custom_sop = $profile['default_sop'] ?? 'Applying with AIUB Profile Vault credentials.';
        }

        if (empty($applied_role)) {
            $applied_role = ($drive_type === 'corporate') ? 'Software Trainee' : 'Candidate Member';
        }

        if ($drive_type === 'club') {
            if (hasStudentAppliedClub($conn, $drive_id, $auth_user['id'])) {
                set_flash('error', 'You have already applied to this club opportunity.');
                header('Location: index.php?controller=student&action=dashboard');
                exit;
            }
            $res = applyToClubDrive($conn, $drive_id, $profile['id'], $applied_role, $cv_url, $custom_sop);
        } else {
            if (hasStudentAppliedCorp($conn, $drive_id, $auth_user['id'])) {
                set_flash('error', 'You have already applied to this corporate job drive.');
                header('Location: index.php?controller=student&action=dashboard');
                exit;
            }

            // Server-side CGPA requirement validation
            $corp_stmt = mysqli_prepare($conn, "SELECT job_title, min_cgpa, company_name FROM corporate_drives WHERE id = ?");
            mysqli_stmt_bind_param($corp_stmt, "i", $drive_id);
            mysqli_stmt_execute($corp_stmt);
            $corp_res = mysqli_stmt_get_result($corp_stmt);
            $corp_drive_data = mysqli_fetch_assoc($corp_res);
            mysqli_stmt_close($corp_stmt);

            if ($corp_drive_data && floatval($corp_drive_data['min_cgpa']) > 0) {
                $required_cgpa = floatval($corp_drive_data['min_cgpa']);
                $student_cgpa = floatval($profile['cgpa'] ?? 0.00);
                if ($student_cgpa < $required_cgpa) {
                    set_flash('error', "CGPA Requirement Unmet: '{$corp_drive_data['job_title']}' requires a minimum CGPA of " . number_format($required_cgpa, 2) . " (Your recorded CGPA is " . number_format($student_cgpa, 2) . "). You cannot apply for this position.");
                    header('Location: index.php?controller=student&action=dashboard');
                    exit;
                }
            }

            // Dynamic Form Questions validation
            $custom_fields = getCustomFormFieldsByDrive($conn, $drive_id);
            $raw_answers = $_POST['custom_answers'] ?? [];
            $validated_answers = [];

            if (!empty($custom_fields)) {
                foreach ($custom_fields as $fld) {
                    $lbl = $fld['field_label'];
                    $fld_id = $fld['id'] ?? null;
                    $typ = $fld['field_type'] ?? 'text';

                    $val = '';
                    if ($fld_id !== null && isset($raw_answers[$fld_id])) {
                        $val = trim($raw_answers[$fld_id]);
                    } elseif (isset($raw_answers[$lbl])) {
                        $val = trim($raw_answers[$lbl]);
                    } else {
                        $sanitized_key = str_replace([' ', '.', '/', '-', "'", '"'], '_', $lbl);
                        if (isset($raw_answers[$sanitized_key])) {
                            $val = trim($raw_answers[$sanitized_key]);
                        }
                    }

                    if ($typ === 'url') {
                        if (empty($val) || !filter_var($val, FILTER_VALIDATE_URL)) {
                            set_flash('error', "Please provide a valid web URL link (e.g. https://github.com/...) for question: '{$lbl}'.");
                            header('Location: index.php?controller=student&action=dashboard');
                            exit;
                        }
                    } elseif ($typ === 'textarea' || $typ === 'text') {
                        if (empty($val)) {
                            set_flash('error', "Please complete the required screening question: '{$lbl}'.");
                            header('Location: index.php?controller=student&action=dashboard');
                            exit;
                        }
                    }
                    $validated_answers[$lbl] = cleanInput($val);
                }
            }

            $custom_answers_json = !empty($validated_answers) ? json_encode($validated_answers) : '';
            $res = applyToCorporateDrive($conn, $drive_id, $profile['id'], $applied_role, $cv_url, $custom_sop, $custom_answers_json);
        }

        if (!empty($res['success'])) {
            set_flash('success', "Application successfully submitted for '{$applied_role}'!");
            header('Location: index.php?controller=student&action=applications');
        } else {
            set_flash('error', $res['message'] ?? 'Failed to submit application. Please try again.');
            header('Location: index.php?controller=student&action=dashboard');
        }
        exit;
    }

    public static function ajaxBookmark($conn) {
        $auth_user = get_auth_user();
        header('Content-Type: application/json');

        if (!$auth_user || $auth_user['role'] !== 'student') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $item_type = cleanInput($input['item_type'] ?? '');
        $item_id = intval($input['item_id'] ?? 0);

        if (empty($item_type) || !$item_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            exit;
        }

        $result = toggleStudentBookmark($conn, $auth_user['id'], $item_type, $item_id);
        echo json_encode($result);
        exit;
    }
}
