<?php
// ==============================================================================
// CampusXP - LogisticsModel (Campus Equipment Requisition & Approvals)
// ==============================================================================

/**
 * Fetch logistics requests for a specific user
 */
function getLogisticsRequestsByUser($conn, $userId) {
    $sql = "SELECT l.*, l.purpose as event_name, l.item_name as item_type,
                   l.date_needed as needed_date, l.date_needed as return_date,
                   l.admin_notes as notes
            FROM logistics_requests l
            WHERE l.user_id = ?
            ORDER BY l.requested_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $requests = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $requests;
}

/**
 * Fetch all logistics requests (for admin view)
 */
function getAllLogisticsRequests($conn) {
    $sql = "SELECT l.*, l.purpose as event_name, l.item_name as item_type,
                   l.date_needed as needed_date, l.date_needed as return_date,
                   l.admin_notes as notes,
                   u.full_name as requester_name, u.role as requester_role_name
            FROM logistics_requests l
            LEFT JOIN users u ON l.user_id = u.id
            ORDER BY l.requested_at DESC";
    $result = mysqli_query($conn, $sql);
    $requests = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    return $requests;
}

/**
 * Create a new logistics equipment request
 */
function createLogisticsRequest($conn, $userId, $eventName, $itemType, $qty, $neededDate, $returnDate = '', $notes = '') {
    // Find user role and club
    $user_res = mysqli_query($conn, "SELECT role, full_name FROM users WHERE id = " . intval($userId));
    $user_row = mysqli_fetch_assoc($user_res);
    $role = $user_row['role'] ?? 'club_exec';
    $club_or_org = ($role === 'recruiter') ? 'Corporate Recruiter' : 'AIUB Student Club';

    $sql = "INSERT INTO logistics_requests (user_id, requester_role, club_or_org, item_name, quantity, purpose, date_needed, dispatch_status, admin_notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Requested', ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "isssisss", $userId, $role, $club_or_org, $itemType, $qty, $eventName, $neededDate, $notes);
    $res = mysqli_stmt_execute($stmt);
    $newId = $res ? mysqli_insert_id($conn) : false;
    mysqli_stmt_close($stmt);
    return $newId;
}

/**
 * Update dispatch status (for admin)
 */
function updateLogisticsDispatchStatus($conn, $reqId, $newStatus, $adminNotes = '') {
    $allowed = ['Requested', 'Approved', 'Dispatched', 'Returned', 'Rejected'];
    if (!in_array($newStatus, $allowed, true)) return false;

    $sql = "UPDATE logistics_requests SET dispatch_status = ?, admin_notes = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $newStatus, $adminNotes, $reqId);
    $res = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $res;
}
