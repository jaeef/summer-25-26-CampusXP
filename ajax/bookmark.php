<?php
// ajax/bookmark.php — JSON Endpoint for Instant Asynchronous Opportunity Bookmarking
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

$user = get_auth_user();

if (!$user || $user['role'] !== 'student') {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized. Student login required.'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. POST required.'
    ]);
    exit;
}

$item_type = cleanInput($_POST['item_type'] ?? '');
$item_id = intval($_POST['item_id'] ?? 0);

if (empty($item_type) || $item_id <= 0) {
    $raw = json_decode(file_get_contents('php://input'), true);
    if (is_array($raw)) {
        $item_type = cleanInput($raw['item_type'] ?? $item_type);
        $item_id = intval($raw['item_id'] ?? $item_id);
    }
}

if ($item_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid opportunity item ID.'
    ]);
    exit;
}

// Allowed item types
$allowed_types = ['club_drive', 'corporate_drive', 'event'];
if (!in_array($item_type, $allowed_types)) {
    $item_type = 'club_drive';
}

// 1. Get or create student profile
$sql = "SELECT id FROM student_profiles WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user['id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student_profile = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$student_profile) {
    $default_aiub_id = '22-' . rand(10000, 99999) . '-1';
    $default_dept = 'CSE';
    $default_cgpa = 3.75;
    $default_cv = 'https://drive.google.com/file/d/cv/view';
    $default_sop = 'AIUB student enthusiastic about participating in campus events and opportunities.';
    
    $sql = "INSERT INTO student_profiles (user_id, aiub_id, department, cgpa, cv_url, default_sop) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "issdss", $user['id'], $default_aiub_id, $default_dept, $default_cgpa, $default_cv, $default_sop);
    mysqli_stmt_execute($stmt);
    $student_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
} else {
    $student_id = $student_profile['id'];
}

// 2. Check if already bookmarked
$sql = "SELECT id FROM opportunity_bookmarks WHERE student_id = ? AND item_type = ? AND item_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "isi", $student_id, $item_type, $item_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$existing = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

$is_bookmarked = false;
$action_taken = '';
$feedback = '';

if ($existing) {
    // Remove bookmark
    $sql = "DELETE FROM opportunity_bookmarks WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $existing['id']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    $is_bookmarked = false;
    $action_taken = 'removed';
    $feedback = 'Removed opportunity from your Saved Bookmarks.';
} else {
    // Add bookmark
    $sql = "INSERT INTO opportunity_bookmarks (student_id, item_type, item_id) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "isi", $student_id, $item_type, $item_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    $is_bookmarked = true;
    $action_taken = 'added';
    $feedback = 'Saved opportunity to your Bookmarks list!';
}

// 3. Get updated count of bookmarks for this student
$sql = "SELECT COUNT(*) as total_saved FROM opportunity_bookmarks WHERE student_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$count_row = mysqli_fetch_assoc($result);
$total_saved = $count_row['total_saved'] ?? 0;
mysqli_stmt_close($stmt);

echo json_encode([
    'success' => true,
    'action' => $action_taken,
    'bookmarked' => $is_bookmarked,
    'item_type' => $item_type,
    'item_id' => $item_id,
    'total_saved' => $total_saved,
    'message' => $feedback
]);
exit;
