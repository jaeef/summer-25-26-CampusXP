<?php
// ==============================================================================
// CampusXP - InterviewModel (Auto-Scheduler & Venue Slot Management)
// ==============================================================================

/**
 * Fetch interview slots for a drive (or all drives if 0)
 */
function getInterviewSlotsByDrive($conn, $driveId = 0) {
    if ($driveId > 0) {
        $sql = "SELECT s.*, s.slot_datetime as slot_time, s.room_no as venue_room,
                       a.applied_role, u.full_name as candidate_name, sp.aiub_id, cd.title as drive_title
                FROM interview_slots s
                JOIN club_drives cd ON s.drive_id = cd.id
                LEFT JOIN club_applications a ON s.assigned_application_id = a.id
                LEFT JOIN student_profiles sp ON a.student_id = sp.id
                LEFT JOIN users u ON sp.user_id = u.id
                WHERE s.drive_id = ?
                ORDER BY s.slot_datetime ASC";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $driveId);
    } else {
        $sql = "SELECT s.*, s.slot_datetime as slot_time, s.room_no as venue_room,
                       a.applied_role, u.full_name as candidate_name, sp.aiub_id, cd.title as drive_title
                FROM interview_slots s
                JOIN club_drives cd ON s.drive_id = cd.id
                LEFT JOIN club_applications a ON s.assigned_application_id = a.id
                LEFT JOIN student_profiles sp ON a.student_id = sp.id
                LEFT JOIN users u ON sp.user_id = u.id
                ORDER BY s.slot_datetime ASC";
        $stmt = mysqli_prepare($conn, $sql);
    }
    mysqli_stmt_execute($stmt);
    $slots = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $slots;
}

/**
 * Fetch candidates eligible for interview (Status = Interview, Under Review, Applied)
 */
function getCandidatesForInterview($conn, $driveId = 0) {
    if ($driveId > 0) {
        $sql = "SELECT a.id, a.drive_id, a.applied_role, a.status, u.full_name, sp.aiub_id, cd.title as drive_title
                FROM club_applications a
                JOIN club_drives cd ON a.drive_id = cd.id
                JOIN student_profiles sp ON a.student_id = sp.id
                JOIN users u ON sp.user_id = u.id
                WHERE a.drive_id = ? AND a.status IN ('Interview', 'Under Review', 'Applied')
                ORDER BY (a.status = 'Interview') DESC, u.full_name ASC";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $driveId);
    } else {
        $sql = "SELECT a.id, a.drive_id, a.applied_role, a.status, u.full_name, sp.aiub_id, cd.title as drive_title
                FROM club_applications a
                JOIN club_drives cd ON a.drive_id = cd.id
                JOIN student_profiles sp ON a.student_id = sp.id
                JOIN users u ON sp.user_id = u.id
                WHERE a.status IN ('Interview', 'Under Review', 'Applied')
                ORDER BY (a.status = 'Interview') DESC, u.full_name ASC";
        $stmt = mysqli_prepare($conn, $sql);
    }
    mysqli_stmt_execute($stmt);
    $candidates = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $candidates;
}

/**
 * Create a new interview slot
 */
function createInterviewSlot($conn, $driveId, $venue, $slotTime) {
    $sql = "INSERT INTO interview_slots (drive_id, room_no, slot_datetime, is_booked) VALUES (?, ?, ?, 0)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $driveId, $venue, $slotTime);
    $res = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $res;
}

/**
 * Assign candidate to slot
 */
function assignCandidateToSlot($conn, $slotId, $appId) {
    $sql = "UPDATE interview_slots SET assigned_application_id = ?, is_booked = 1 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $appId, $slotId);
    $res = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Update application status to Interview
    $sql_app = "UPDATE club_applications SET status = 'Interview' WHERE id = ?";
    $stmt_app = mysqli_prepare($conn, $sql_app);
    mysqli_stmt_bind_param($stmt_app, "i", $appId);
    mysqli_stmt_execute($stmt_app);
    mysqli_stmt_close($stmt_app);

    return $res;
}

/**
 * Release / Clear interview slot
 */
function releaseInterviewSlot($conn, $slotId) {
    $sql = "UPDATE interview_slots SET assigned_application_id = NULL, is_booked = 0 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $slotId);
    $res = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $res;
}
