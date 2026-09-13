<?php
// ==============================================================================
// CampusXP - ApplicationModel (Kanban pipeline & candidate evaluations)
// ==============================================================================

/**
 * Fetch applicants for a club drive (grouped for Kanban pipeline)
 */
function getClubApplicantsForKanban($conn, $driveId = 0) {
    if ($driveId > 0) {
        $sql = "SELECT a.*, 
                       cd.title AS drive_title, cd.club_name,
                       COALESCE(u.full_name, u2.full_name, 'AIUB Candidate') AS full_name,
                       COALESCE(u.email, u2.email, '') AS email,
                       COALESCE(sp.aiub_id, sp2.aiub_id, 'Pending') AS aiub_id,
                       COALESCE(sp.department, sp2.department, 'CSE') AS department,
                       COALESCE(sp.cgpa, sp2.cgpa, 0.00) AS cgpa,
                       COALESCE(sp.skills, sp2.skills, '') AS skills
                FROM club_applications a
                JOIN club_drives cd ON a.drive_id = cd.id
                LEFT JOIN student_profiles sp ON a.student_id = sp.id
                LEFT JOIN users u ON sp.user_id = u.id
                LEFT JOIN student_profiles sp2 ON a.student_id = sp2.user_id
                LEFT JOIN users u2 ON a.student_id = u2.id
                WHERE a.drive_id = ?
                ORDER BY a.applied_at DESC";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $driveId);
    } else {
        $sql = "SELECT a.*, 
                       cd.title AS drive_title, cd.club_name,
                       COALESCE(u.full_name, u2.full_name, 'AIUB Candidate') AS full_name,
                       COALESCE(u.email, u2.email, '') AS email,
                       COALESCE(sp.aiub_id, sp2.aiub_id, 'Pending') AS aiub_id,
                       COALESCE(sp.department, sp2.department, 'CSE') AS department,
                       COALESCE(sp.cgpa, sp2.cgpa, 0.00) AS cgpa,
                       COALESCE(sp.skills, sp2.skills, '') AS skills
                FROM club_applications a
                JOIN club_drives cd ON a.drive_id = cd.id
                LEFT JOIN student_profiles sp ON a.student_id = sp.id
                LEFT JOIN users u ON sp.user_id = u.id
                LEFT JOIN student_profiles sp2 ON a.student_id = sp2.user_id
                LEFT JOIN users u2 ON a.student_id = u2.id
                ORDER BY a.applied_at DESC";
        $stmt = mysqli_prepare($conn, $sql);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $apps = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);

    $pipeline = [
        'Applied' => [],
        'Under Review' => [],
        'Interview' => [],
        'Selected' => [],
        'Rejected' => []
    ];

    foreach ($apps as $app) {
        $st = $app['status'];
        if (isset($pipeline[$st])) {
            $pipeline[$st][] = $app;
        } else {
            $pipeline['Applied'][] = $app;
        }
    }

    return $pipeline;
}

/**
 * Update candidate Kanban application status
 */
function updateApplicationStatus($conn, $appId, $newStatus) {
    $allowed = ['Applied', 'Under Review', 'Interview', 'Selected', 'Rejected'];
    if (!in_array($newStatus, $allowed, true)) {
        return false;
    }
    $sql = "UPDATE club_applications SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $newStatus, $appId);
    $res = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $res;
}

/**
 * Fetch executive dashboard application overview metrics
 */
function getExecutiveAppStats($conn, $execUserId = 0) {
    $sql = "SELECT
        COUNT(*) as total_apps,
        SUM(CASE WHEN status = 'Under Review' THEN 1 ELSE 0 END) as review_apps,
        SUM(CASE WHEN status = 'Interview' THEN 1 ELSE 0 END) as interview_apps,
        SUM(CASE WHEN status = 'Selected' THEN 1 ELSE 0 END) as selected_apps
        FROM club_applications";
    $result = mysqli_query($conn, $sql);
    $stats = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return $stats;
}

/**
 * Fetch applicants for demographic analytics
 */
function getClubApplicantsForAnalytics($conn, $driveId = 0) {
    if ($driveId > 0) {
        $sql = "SELECT a.*, sp.department, sp.cgpa, sp.skills, u.full_name
                FROM club_applications a
                JOIN student_profiles sp ON a.student_id = sp.id
                JOIN users u ON sp.user_id = u.id
                WHERE a.drive_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $driveId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $apps = mysqli_fetch_all($res, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
    } else {
        $sql = "SELECT a.*, sp.department, sp.cgpa, sp.skills, u.full_name
                FROM club_applications a
                JOIN student_profiles sp ON a.student_id = sp.id
                JOIN users u ON sp.user_id = u.id";
        $result = mysqli_query($conn, $sql);
        $apps = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_free_result($result);
    }
    return $apps;
}
