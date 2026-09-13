<?php
// ==============================================================================
// CampusXP - DriveModel (Club & Corporate Drives CRUD & Search)
// ==============================================================================

/**
 * Fetch all active club drives
 */
function getActiveClubDrives($conn) {
    $sql = "SELECT * FROM club_drives WHERE status = 'active' ORDER BY deadline ASC";
    $result = mysqli_query($conn, $sql);
    $drives = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    return $drives;
}

/**
 * Fetch all active corporate drives
 */
function getActiveCorporateDrives($conn) {
    $sql = "SELECT * FROM corporate_drives WHERE status = 'active' ORDER BY deadline ASC";
    $result = mysqli_query($conn, $sql);
    $drives = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    return $drives;
}

function getActiveCorpDrives($conn) {
    return getActiveCorporateDrives($conn);
}

/**
 * Fetch club drives by executive user ID
 */
function getClubDrivesByExecutive($conn, $execUserId) {
    $sql = "SELECT d.*, COUNT(a.id) as app_count,
                   ar.status as approval_status, ar.admin_comments
            FROM club_drives d
            LEFT JOIN club_applications a ON d.id = a.drive_id
            LEFT JOIN approval_requests ar ON (ar.target_type = 'club_drive' AND ar.target_id = d.id)
            WHERE d.exec_user_id = ?
            GROUP BY d.id ORDER BY d.created_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $execUserId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $drives = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $drives;
}

/**
 * Create a new club drive (queues for admin approval)
 */
function createClubDrive($conn, $execUserId, $clubName, $title, $category, $requirements, $deadline, $location) {
    $sql = "INSERT INTO club_drives (exec_user_id, club_name, title, category, requirements, deadline, location, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending_approval')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "issssss", $execUserId, $clubName, $title, $category, $requirements, $deadline, $location);
    mysqli_stmt_execute($stmt);
    $driveId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // Create moderation request
    $sql_mod = "INSERT INTO approval_requests (target_type, target_id, title, submitted_by, status) VALUES ('club_drive', ?, ?, ?, 'Pending')";
    $stmt_mod = mysqli_prepare($conn, $sql_mod);
    mysqli_stmt_bind_param($stmt_mod, "iss", $driveId, $title, $clubName);
    mysqli_stmt_execute($stmt_mod);
    mysqli_stmt_close($stmt_mod);

    return $driveId;
}

/**
 * Fetch corporate drives for a recruiter
 */
function getCorporateDrivesByRecruiter($conn, $recruiterUserId) {
    $sql = "SELECT d.*, COUNT(f.id) as custom_field_count,
                   ar.status as approval_status, ar.admin_comments, ar.reviewed_at
            FROM corporate_drives d
            LEFT JOIN custom_form_fields f ON d.id = f.corporate_drive_id
            LEFT JOIN approval_requests ar ON (ar.target_type = 'corporate_drive' AND ar.target_id = d.id)
            WHERE d.recruiter_user_id = ?
            GROUP BY d.id ORDER BY d.created_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $recruiterUserId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $drives = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $drives;
}

/**
 * Create corporate drive with custom fields
 */
function createCorporateDrive($conn, $recruiterUserId, $companyName, $jobTitle, $jobType, $minCgpa, $deadline, $requirements, $customLabels = [], $customTypes = []) {
    $sql = "INSERT INTO corporate_drives (recruiter_user_id, company_name, job_title, job_type, requirements, min_cgpa, deadline, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending_approval')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "issssds", $recruiterUserId, $companyName, $jobTitle, $jobType, $requirements, $minCgpa, $deadline);
    mysqli_stmt_execute($stmt);
    $driveId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // Custom fields
    if (!empty($customLabels)) {
        $sql_f = "INSERT INTO custom_form_fields (corporate_drive_id, field_label, field_type, is_required) VALUES (?, ?, ?, 1)";
        for ($i = 0; $i < count($customLabels); $i++) {
            $label = cleanInput($customLabels[$i]);
            $type = cleanInput($customTypes[$i] ?? 'text');
            if (!empty($label)) {
                $stmt_f = mysqli_prepare($conn, $sql_f);
                mysqli_stmt_bind_param($stmt_f, "iss", $driveId, $label, $type);
                mysqli_stmt_execute($stmt_f);
                mysqli_stmt_close($stmt_f);
            }
        }
    }

    // Approval request
    $sql_mod = "INSERT INTO approval_requests (target_type, target_id, title, submitted_by, status) VALUES ('corporate_drive', ?, ?, ?, 'Pending')";
    $stmt_mod = mysqli_prepare($conn, $sql_mod);
    mysqli_stmt_bind_param($stmt_mod, "iss", $driveId, $jobTitle, $companyName);
    mysqli_stmt_execute($stmt_mod);
    mysqli_stmt_close($stmt_mod);

    return $driveId;
}

/**
 * Fetch custom form fields attached to a corporate drive
 */
function getCustomFormFieldsByDrive($conn, $driveId) {
    $sql = "SELECT * FROM custom_form_fields WHERE corporate_drive_id = ? ORDER BY id ASC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $driveId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $fields = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $fields;
}
