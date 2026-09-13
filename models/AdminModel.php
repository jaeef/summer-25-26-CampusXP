<?php
// ==============================================================================
// CampusXP - AdminModel (Moderation Queue, Venue Booths & Campus Metrics)
// ==============================================================================

/**
 * Fetch all moderation requests
 */
function getAllModerationRequests($conn) {
    $sql = "SELECT * FROM approval_requests ORDER BY submitted_at DESC";
    $result = mysqli_query($conn, $sql);
    $requests = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    return $requests;
}

/**
 * Moderate a drive (Approve or Reject with feedback comments)
 */
function moderateRequest($conn, $reqId, $action, $adminComments) {
    // 1. Update Approval Request
    $sql = "UPDATE approval_requests SET status = ?, admin_comments = ?, reviewed_at = NOW() WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $action, $adminComments, $reqId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // 2. Fetch target
    $sql = "SELECT target_type, target_id FROM approval_requests WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $reqId);
    mysqli_stmt_execute($stmt);
    $req = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if ($req) {
        $driveStatus = ($action === 'Approved') ? 'active' : 'closed';
        if ($req['target_type'] === 'club_drive') {
            $sql_d = "UPDATE club_drives SET status = ? WHERE id = ?";
            $stmt_d = mysqli_prepare($conn, $sql_d);
            mysqli_stmt_bind_param($stmt_d, "si", $driveStatus, $req['target_id']);
            mysqli_stmt_execute($stmt_d);
            mysqli_stmt_close($stmt_d);
        } elseif ($req['target_type'] === 'corporate_drive') {
            $sql_d = "UPDATE corporate_drives SET status = ? WHERE id = ?";
            $stmt_d = mysqli_prepare($conn, $sql_d);
            mysqli_stmt_bind_param($stmt_d, "si", $driveStatus, $req['target_id']);
            mysqli_stmt_execute($stmt_d);
            mysqli_stmt_close($stmt_d);
        }
    }
    return true;
}

/**
 * Fetch all venue booth allocations
 */
function getAllBoothAllocations($conn) {
    $sql = "SELECT * FROM venue_booth_allocations ORDER BY booth_number ASC";
    $result = mysqli_query($conn, $sql);
    $booths = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    return $booths;
}

/**
 * Allocate or update a booth
 */
function saveBoothAllocation($conn, $boothNumber, $campusLocation, $clubName, $notes = '') {
    $today = date('Y-m-d');
    $sql = "INSERT INTO venue_booth_allocations (booth_number, campus_location, assigned_club_name, allocated_date, is_occupied)
            VALUES (?, ?, ?, ?, 1)
            ON DUPLICATE KEY UPDATE campus_location = VALUES(campus_location), assigned_club_name = VALUES(assigned_club_name), is_occupied = 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $boothNumber, $campusLocation, $clubName, $today);
    $res = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $res;
}

/**
 * Fetch overall system KPIs for admin dashboard
 */
function getSystemDashboardMetrics($conn) {
    $metrics = [];

    // Total Users
    $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM users");
    $metrics['total_users'] = mysqli_fetch_assoc($res)['cnt'] ?? 0;

    // Students
    $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM users WHERE role = 'student'");
    $metrics['student_count'] = mysqli_fetch_assoc($res)['cnt'] ?? 0;

    // Active Drives
    $res = mysqli_query($conn, "SELECT (
        (SELECT COUNT(*) FROM club_drives WHERE status = 'active') +
        (SELECT COUNT(*) FROM corporate_drives WHERE status = 'active')
    ) as cnt");
    $metrics['active_drives'] = mysqli_fetch_assoc($res)['cnt'] ?? 0;

    // Pending Approvals
    $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM approval_requests WHERE status = 'Pending'");
    $metrics['pending_moderations'] = mysqli_fetch_assoc($res)['cnt'] ?? 0;

    return $metrics;
}
