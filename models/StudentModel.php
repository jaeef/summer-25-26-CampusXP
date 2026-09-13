<?php
// ==============================================================================
// CampusXP - StudentModel (Data operations for Student Profile, Bookmarks, Apps)
// ==============================================================================

/**
 * Fetch a student's profile by user ID
 */
function getStudentProfileByUserId($conn, $userId) {
    $sql = "SELECT * FROM student_profiles WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $profile = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $profile;
}

/**
 * Check if AIUB ID is already taken by another user
 */
function isAiubIdTaken($conn, $aiubId, $excludeUserId = 0) {
    if ($excludeUserId > 0) {
        $sql = "SELECT id FROM student_profiles WHERE aiub_id = ? AND user_id != ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $aiubId, $excludeUserId);
    } else {
        $sql = "SELECT id FROM student_profiles WHERE aiub_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $aiubId);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $exists = mysqli_fetch_assoc($result) ? true : false;
    mysqli_stmt_close($stmt);
    return $exists;
}

/**
 * Create or update student profile vault
 */
function saveStudentProfile($conn, $userId, $aiubId, $dept, $cgpa, $skills, $cvUrl, $defaultSop) {
    $existing = getStudentProfileByUserId($conn, $userId);
    if ($existing) {
        $sql = "UPDATE student_profiles SET aiub_id = ?, department = ?, cgpa = ?, skills = ?, cv_url = ?, default_sop = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssdsssi", $aiubId, $dept, $cgpa, $skills, $cvUrl, $defaultSop, $userId);
        $res = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $existing['id'];
    } else {
        $sql = "INSERT INTO student_profiles (user_id, aiub_id, department, cgpa, skills, cv_url, default_sop) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "issdsss", $userId, $aiubId, $dept, $cgpa, $skills, $cvUrl, $defaultSop);
        $res = mysqli_stmt_execute($stmt);
        $newId = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        return $newId;
    }
}

/**
 * Get total application count for student (club + corporate)
 */
function getStudentTotalAppsCount($conn, $studentId) {
    $sql = "SELECT (
        (SELECT COUNT(*) FROM club_applications WHERE student_id = ?) +
        (SELECT COUNT(*) FROM corporate_applications WHERE student_id = ?)
    ) AS total_apps";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $studentId, $studentId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $count = mysqli_fetch_assoc($result)['total_apps'] ?? 0;
    mysqli_stmt_close($stmt);
    return intval($count);
}

/**
 * Fetch saved bookmarks map
 */
function getStudentBookmarks($conn, $studentId) {
    $sql = "SELECT item_type, item_id FROM opportunity_bookmarks WHERE student_id = ? OR student_id IN (SELECT id FROM student_profiles WHERE user_id = ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $studentId, $studentId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    
    $map = [];
    foreach ($rows as $r) {
        $map[$r['item_type'] . '_' . $r['item_id']] = true;
    }
    return $map;
}

/**
 * Toggle bookmark asynchronously
 */
function toggleBookmark($conn, $studentId, $itemType, $itemId) {
    $sql = "SELECT id FROM opportunity_bookmarks WHERE student_id = ? AND item_type = ? AND item_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "isi", $studentId, $itemType, $itemId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $existing = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($existing) {
        $sql = "DELETE FROM opportunity_bookmarks WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $existing['id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $isBookmarked = false;
    } else {
        $sql = "INSERT INTO opportunity_bookmarks (student_id, item_type, item_id) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isi", $studentId, $itemType, $itemId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $isBookmarked = true;
    }

    // Get updated total count
    $sql = "SELECT COUNT(*) as total FROM opportunity_bookmarks WHERE student_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $studentId);
    mysqli_stmt_execute($stmt);
    $total = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'] ?? 0;
    mysqli_stmt_close($stmt);

    return ['success' => true, 'bookmarked' => $isBookmarked, 'total_saved' => intval($total), 'message' => $isBookmarked ? 'Saved to bookmarks' : 'Removed from bookmarks'];
}

function toggleStudentBookmark($conn, $userId, $itemType, $itemId) {
    $profile = getStudentProfileByUserId($conn, $userId);
    if (!$profile) {
        return ['success' => false, 'message' => 'Student profile not found'];
    }
    return toggleBookmark($conn, $profile['id'], $itemType, $itemId);
}

/**
 * Fetch corporate outreach alerts for a student
 */
function getStudentOutreachAlerts($conn, $studentId) {
    $sql = "SELECT o.*, s.bucket_name, u.full_name as recruiter_name
            FROM recruiter_outreach_logs o
            JOIN recruiter_shortlists s ON o.shortlist_id = s.id
            JOIN users u ON s.recruiter_user_id = u.id
            WHERE o.student_id = ? ORDER BY o.sent_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $studentId);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $rows;
}

/**
 * Apply to club drive
 */
function applyToClubDrive($conn, $driveId, $studentId, $role, $cvLink, $sop) {
    $sql = "SELECT id FROM club_applications WHERE drive_id = ? AND student_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $driveId, $studentId);
    mysqli_stmt_execute($stmt);
    $already = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if ($already) return ['success' => false, 'message' => 'You have already applied to this club drive.'];

    $sql = "INSERT INTO club_applications (drive_id, student_id, applied_role, cv_link, statement_of_purpose, status) VALUES (?, ?, ?, ?, ?, 'Applied')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iisss", $driveId, $studentId, $role, $cvLink, $sop);
    $res = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return ['success' => $res, 'message' => 'Club application submitted successfully!'];
}

/**
 * Apply to corporate drive
 */
function applyToCorporateDrive($conn, $driveId, $studentId, $role, $cvLink, $sop, $customAnswers = '') {
    $sql = "SELECT id FROM corporate_applications WHERE corporate_drive_id = ? AND student_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $driveId, $studentId);
    mysqli_stmt_execute($stmt);
    $already = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if ($already) return ['success' => false, 'message' => 'You have already applied to this corporate drive.'];

    $sql = "INSERT INTO corporate_applications (corporate_drive_id, student_id, applied_role, cv_link, statement_of_purpose, custom_answers, status) VALUES (?, ?, ?, ?, ?, ?, 'Applied')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iissss", $driveId, $studentId, $role, $cvLink, $sop, $customAnswers);
    $res = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return ['success' => $res, 'message' => 'Corporate application submitted successfully!'];
}

function applyToCorpDrive($conn, $driveId, $studentId, $role, $cvLink, $sop, $customAnswers = '') {
    return applyToCorporateDrive($conn, $driveId, $studentId, $role, $cvLink, $sop, $customAnswers);
}

/**
 * Fetch all applications for a student
 */
function getStudentApplicationHistory($conn, $studentId) {
    // Club applications
    $sql = "SELECT a.*, d.title as drive_title, d.club_name, d.location, d.deadline
            FROM club_applications a
            JOIN club_drives d ON a.drive_id = d.id
            WHERE a.student_id = ?
            ORDER BY a.applied_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $studentId);
    mysqli_stmt_execute($stmt);
    $club_apps = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);

    // Corporate applications
    $sql = "SELECT ca.*, cd.job_title as drive_title, cd.company_name, cd.job_type, cd.deadline
            FROM corporate_applications ca
            JOIN corporate_drives cd ON ca.corporate_drive_id = cd.id
            WHERE ca.student_id = ?
            ORDER BY ca.applied_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $studentId);
    mysqli_stmt_execute($stmt);
    $corp_apps = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);

    return ['club' => $club_apps, 'corporate' => $corp_apps];
}

/**
 * Get club applications by user ID
 */
function getClubApplicationsByStudent($conn, $userId) {
    $profile = getStudentProfileByUserId($conn, $userId);
    if (!$profile) return [];
    $history = getStudentApplicationHistory($conn, $profile['id']);
    return $history['club'] ?? [];
}

/**
 * Get corporate applications by user ID
 */
function getCorpApplicationsByStudent($conn, $userId) {
    $profile = getStudentProfileByUserId($conn, $userId);
    if (!$profile) return [];
    $history = getStudentApplicationHistory($conn, $profile['id']);
    return $history['corporate'] ?? [];
}

/**
 * Check if student applied to club drive
 */
function hasStudentAppliedClub($conn, $driveId, $userId) {
    $profile = getStudentProfileByUserId($conn, $userId);
    if (!$profile) return false;
    $sql = "SELECT id FROM club_applications WHERE drive_id = ? AND student_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $driveId, $profile['id']);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $exists = mysqli_fetch_assoc($res) ? true : false;
    mysqli_stmt_close($stmt);
    return $exists;
}

/**
 * Check if student applied to corporate drive
 */
function hasStudentAppliedCorp($conn, $driveId, $userId) {
    $profile = getStudentProfileByUserId($conn, $userId);
    if (!$profile) return false;
    $sql = "SELECT id FROM corporate_applications WHERE corporate_drive_id = ? AND student_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $driveId, $profile['id']);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $exists = mysqli_fetch_assoc($res) ? true : false;
    mysqli_stmt_close($stmt);
    return $exists;
}

/**
 * Get direct alerts by student user ID
 */
function getDirectAlertsByStudentId($conn, $userId) {
    $profile = getStudentProfileByUserId($conn, $userId);
    if (!$profile) return [];
    return getStudentOutreachAlerts($conn, $profile['id']);
}

