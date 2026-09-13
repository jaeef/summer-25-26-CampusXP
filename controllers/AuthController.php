<?php
// ==============================================================================
// CampusXP - AuthController (MVC Controller for Authentication & Session Guards)
// ==============================================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/StudentModel.php';

class AuthController {

    public static function showLogin($conn) {
        $auth_user = get_auth_user();
        if ($auth_user) {
            self::redirectRole($auth_user['role']);
        }
        $flash = get_flash();
        $remembered_email = $_COOKIE['campusxp_remember_email'] ?? '';
        require_once __DIR__ . '/../views/auth/login.view.php';
    }

    public static function login($conn) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        // 1. Check for Fast Demo Testing Login
        if (isset($_POST['demo_role'])) {
            $demo_role = cleanInput($_POST['demo_role']);
            $user = getUserByRole($conn, $demo_role);

            if ($user) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'full_name' => $user['full_name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ];

                if ($user['role'] === 'student') {
                    $profile = getStudentProfileByUserId($conn, $user['id']);
                    if ($profile) {
                        $_SESSION['user']['student_profile_id'] = $profile['id'];
                        $_SESSION['user']['aiub_id'] = $profile['aiub_id'];
                        $_SESSION['user']['cgpa'] = $profile['cgpa'];
                        $_SESSION['user']['cv_url'] = $profile['cv_url'];
                        $_SESSION['user']['default_sop'] = $profile['default_sop'];
                    }
                }

                set_flash('success', "Signed in as {$user['full_name']} ({$user['role']}).");
                self::redirectRole($user['role']);
            }
        }

        // 2. Standard Form Login
        $email = cleanInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember_me = !empty($_POST['remember_me']);

        if (empty($email) || empty($password)) {
            set_flash('error', 'Please enter your email address and password.');
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $user = getUserByEmail($conn, $email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            set_flash('error', 'Invalid email address or password combination.');
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        // Handle persistent cookie
        if ($remember_me) {
            setcookie('campusxp_remember_email', $email, time() + (86400 * 30), '/', '', false, true);
        } else {
            if (isset($_COOKIE['campusxp_remember_email'])) {
                setcookie('campusxp_remember_email', '', time() - 3600, '/');
            }
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        if ($user['role'] === 'student') {
            $profile = getStudentProfileByUserId($conn, $user['id']);
            if ($profile) {
                $_SESSION['user']['student_profile_id'] = $profile['id'];
                $_SESSION['user']['aiub_id'] = $profile['aiub_id'];
                $_SESSION['user']['cgpa'] = $profile['cgpa'];
                $_SESSION['user']['cv_url'] = $profile['cv_url'];
                $_SESSION['user']['default_sop'] = $profile['default_sop'];
            }
        }

        set_flash('success', "Welcome back, {$user['full_name']}!");
        self::redirectRole($user['role']);
    }

    public static function showRegister($conn) {
        $auth_user = get_auth_user();
        if ($auth_user) {
            self::redirectRole($auth_user['role']);
        }
        $flash = get_flash();
        require_once __DIR__ . '/../views/auth/register.view.php';
    }

    public static function register($conn) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=auth&action=register');
            exit;
        }

        $role = cleanInput($_POST['role'] ?? 'student');
        $full_name = cleanInput($_POST['full_name'] ?? '');
        $email = cleanInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $allowed_roles = ['student', 'club_exec', 'recruiter'];
        if (!in_array($role, $allowed_roles, true)) {
            set_flash('error', 'Administrator accounts cannot be created via public registration.');
            header('Location: index.php?controller=auth&action=register');
            exit;
        }

        if (empty($full_name) || empty($email) || empty($password)) {
            set_flash('error', 'All required fields must be completed.');
            header('Location: index.php?controller=auth&action=register');
            exit;
        }

        // Full name validation: only letters and spaces (no numbers)
        if (strlen($full_name) < 3 || strlen($full_name) > 60 || !preg_match("/^[a-zA-Z\s\.\-']+$/", $full_name)) {
            set_flash('error', 'Full Name must contain only letters and spaces (no numbers allowed, 3-60 characters).');
            header('Location: index.php?controller=auth&action=register');
            exit;
        }

        // Email validation using PHP native filter_var
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            set_flash('error', 'Please provide a valid email address format.');
            header('Location: index.php?controller=auth&action=register');
            exit;
        }

        // Password length
        if (strlen($password) < 6) {
            set_flash('error', 'Password must be at least 6 characters in length.');
            header('Location: index.php?controller=auth&action=register');
            exit;
        }

        // Check for duplicate Email Address
        $existing = getUserByEmail($conn, $email);
        if ($existing) {
            set_flash('error', 'An account with this email address already exists. Please sign in or use another email.');
            header('Location: index.php?controller=auth&action=register');
            exit;
        }

        // If student role, perform student validations & duplicate AIUB ID check
        $aiub_id = '';
        $dept = 'CSE';
        if ($role === 'student') {
            $aiub_id = cleanInput($_POST['aiub_id'] ?? '');
            $dept = cleanInput($_POST['department'] ?? 'CSE');

            if (empty($aiub_id)) {
                set_flash('error', 'AIUB Student ID is required.');
                header('Location: index.php?controller=auth&action=register');
                exit;
            }

            if (!preg_match('/^\d{2}-\d{5}-\d{1}$/', $aiub_id)) {
                set_flash('error', 'Invalid AIUB Student ID format. Must strictly follow numeric format: XX-XXXXX-X (e.g. 22-22222-2, containing 2 digits, hyphen, 5 digits, hyphen, 1 digit; no strings or letters).');
                header('Location: index.php?controller=auth&action=register');
                exit;
            }

            // Check for duplicate AIUB Student ID
            if (isAiubIdTaken($conn, $aiub_id)) {
                set_flash('error', "The AIUB Student ID '{$aiub_id}' is already registered in the system. Duplicate Student IDs are not allowed.");
                header('Location: index.php?controller=auth&action=register');
                exit;
            }
        }

        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        try {
            mysqli_begin_transaction($conn);

            $user_id = createUser($conn, $full_name, $email, $password_hash, $role);
            if (!$user_id) throw new Exception("Could not insert user record.");

            $student_profile_id = null;
            if ($role === 'student') {
                $student_profile_id = saveStudentProfile($conn, $user_id, $aiub_id, $dept, 0.00, '', 'https://drive.google.com/file/d/cv/view', 'AIUB student profile initialized. Update your Statement of Purpose in Profile Vault.');
            }

            mysqli_commit($conn);

            $_SESSION['user'] = [
                'id' => $user_id,
                'full_name' => $full_name,
                'email' => $email,
                'role' => $role
            ];

            if ($student_profile_id) {
                $_SESSION['user']['student_profile_id'] = $student_profile_id;
                $_SESSION['user']['aiub_id'] = $aiub_id;
                $_SESSION['user']['cgpa'] = 0.00;
                $_SESSION['user']['cv_url'] = 'https://drive.google.com/file/d/cv/view';
                $_SESSION['user']['default_sop'] = 'AIUB student profile initialized. Update your Statement of Purpose in Profile Vault.';
            }

            set_flash('success', "Registration successful! Welcome to CampusXP, {$full_name}.");
            self::redirectRole($role);
        } catch (Exception $e) {
            mysqli_rollback($conn);
            set_flash('error', 'Registration error: ' . $e->getMessage());
            header('Location: index.php?controller=auth&action=register');
            exit;
        }
    }

    public static function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header('Location: index.php?controller=auth&action=login');
        exit;
    }

    public static function redirectRole($role) {
        switch ($role) {
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
        exit;
    }
}
