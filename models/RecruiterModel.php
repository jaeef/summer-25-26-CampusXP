<?php
// ==============================================================================
// CampusXP - RecruiterModel (Talent Search, Shortlisting & Candidate Outreach)
// ==============================================================================

/**
 * Filter and search verified AIUB student talent pool
 */
function searchTalentPool($conn, $dept = '', $minCgpa = 0.00, $skillKeyword = '') {
    $sql = "SELECT sp.*, u.full_name, u.email
            FROM student_profiles sp
            JOIN users u ON sp.user_id = u.id
            WHERE 1=1";
    $params = [];
    $types = "";

    if (!empty($dept)) {
        $sql .= " AND sp.department = ?";
        $params[] = $dept;
        $types .= "s";
    }

    if ($minCgpa > 0) {
        $sql .= " AND sp.cgpa >= ?";
        $params[] = $minCgpa;
        $types .= "d";
    }

    if (!empty($skillKeyword)) {
        $sql .= " AND sp.skills LIKE ?";
        $params[] = "%" . $skillKeyword . "%";
        $types .= "s";
    }

    $sql .= " ORDER BY sp.cgpa DESC";

    $stmt = mysqli_prepare($conn, $sql);
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $results = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $results;
}

/**
 * Fetch shortlists created by recruiter
 */
function getRecruiterShortlists($conn, $recruiterUserId) {
    $sql = "SELECT * FROM recruiter_shortlists WHERE recruiter_user_id = ? ORDER BY created_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $recruiterUserId);
    mysqli_stmt_execute($stmt);
    $lists = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $lists;
}

/**
 * Create a new shortlist bucket
 */
function createShortlistBucket($conn, $recruiterUserId, $bucketName, $minCgpa, $dept) {
    $sql = "INSERT INTO recruiter_shortlists (recruiter_user_id, bucket_name, filter_cgpa_min, filter_dept) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "isds", $recruiterUserId, $bucketName, $minCgpa, $dept);
    $res = mysqli_stmt_execute($stmt);
    $newId = $res ? mysqli_insert_id($conn) : false;
    mysqli_stmt_close($stmt);
    return $newId;
}

/**
 * Log candidate outreach message
 */
function logCandidateOutreach($conn, $shortlistId, $studentId, $message) {
    $sql = "INSERT INTO recruiter_outreach_logs (shortlist_id, student_id, message, response_status) VALUES (?, ?, ?, 'Sent')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iis", $shortlistId, $studentId, $message);
    $res = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $res;
}

/**
 * Fetch outreach logs for recruiter
 */
function getRecruiterOutreachLogs($conn, $recruiterUserId) {
    $sql = "SELECT o.*, s.bucket_name, sp.aiub_id, sp.department, sp.cgpa, u.full_name as student_name, u.email as student_email
            FROM recruiter_outreach_logs o
            JOIN recruiter_shortlists s ON o.shortlist_id = s.id
            JOIN student_profiles sp ON o.student_id = sp.id
            JOIN users u ON sp.user_id = u.id
            WHERE s.recruiter_user_id = ?
            ORDER BY o.sent_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $recruiterUserId);
    mysqli_stmt_execute($stmt);
    $logs = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $logs;
}

/**
 * Fetch applicants for corporate drives (grouped for Kanban pipeline)
 */
function getCorporateApplicantsForKanban($conn, $recruiterUserId, $driveId = 0) {
    if ($driveId > 0) {
        $sql = "SELECT ca.*, cd.job_title AS drive_title, cd.company_name,
                       COALESCE(u.full_name, u2.full_name, 'AIUB Applicant') AS full_name,
                       COALESCE(u.email, u2.email, '') AS email,
                       COALESCE(sp.aiub_id, sp2.aiub_id, 'Pending') AS aiub_id,
                       COALESCE(sp.department, sp2.department, 'CSE') AS department,
                       COALESCE(sp.cgpa, sp2.cgpa, 0.00) AS cgpa,
                       COALESCE(sp.skills, sp2.skills, '') AS skills
                FROM corporate_applications ca
                JOIN corporate_drives cd ON ca.corporate_drive_id = cd.id
                LEFT JOIN student_profiles sp ON ca.student_id = sp.id
                LEFT JOIN users u ON sp.user_id = u.id
                LEFT JOIN student_profiles sp2 ON ca.student_id = sp2.user_id
                LEFT JOIN users u2 ON ca.student_id = u2.id
                WHERE cd.recruiter_user_id = ? AND ca.corporate_drive_id = ?
                ORDER BY ca.applied_at DESC";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $recruiterUserId, $driveId);
    } else {
        $sql = "SELECT ca.*, cd.job_title AS drive_title, cd.company_name,
                       COALESCE(u.full_name, u2.full_name, 'AIUB Applicant') AS full_name,
                       COALESCE(u.email, u2.email, '') AS email,
                       COALESCE(sp.aiub_id, sp2.aiub_id, 'Pending') AS aiub_id,
                       COALESCE(sp.department, sp2.department, 'CSE') AS department,
                       COALESCE(sp.cgpa, sp2.cgpa, 0.00) AS cgpa,
                       COALESCE(sp.skills, sp2.skills, '') AS skills
                FROM corporate_applications ca
                JOIN corporate_drives cd ON ca.corporate_drive_id = cd.id
                LEFT JOIN student_profiles sp ON ca.student_id = sp.id
                LEFT JOIN users u ON sp.user_id = u.id
                LEFT JOIN student_profiles sp2 ON ca.student_id = sp2.user_id
                LEFT JOIN users u2 ON ca.student_id = u2.id
                WHERE cd.recruiter_user_id = ?
                ORDER BY ca.applied_at DESC";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $recruiterUserId);
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
 * Update candidate corporate application status
 */
function updateCorporateApplicationStatus($conn, $appId, $newStatus) {
    $allowed = ['Applied', 'Under Review', 'Interview', 'Selected', 'Rejected'];
    if (!in_array($newStatus, $allowed, true)) {
        return false;
    }
    $sql = "UPDATE corporate_applications SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $newStatus, $appId);
    $res = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $res;
}
