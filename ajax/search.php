<?php
// ajax/search.php — Asynchronous Live Search & Filter Endpoint
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/DriveModel.php';
require_once __DIR__ . '/../models/RecruiterModel.php';

$type = cleanInput($_GET['type'] ?? 'opportunities');
$query = cleanInput($_GET['q'] ?? '');

if ($type === 'talent') {
    $dept = cleanInput($_GET['dept'] ?? '');
    $minCgpa = floatval($_GET['min_cgpa'] ?? 0.00);
    $skill = cleanInput($_GET['skill'] ?? $query);

    $results = searchTalentPool($conn, $dept, $minCgpa, $skill);
    echo json_encode(['success' => true, 'count' => count($results), 'data' => $results]);
    exit;
} else {
    // Search opportunities (Club and Corporate drives)
    $clubDrives = getActiveClubDrives($conn);
    $corpDrives = getActiveCorpDrives($conn);

    $matched = [];
    $qLower = strtolower($query);

    foreach ($clubDrives as $cd) {
        if (empty($qLower) || strpos(strtolower($cd['title']), $qLower) !== false || strpos(strtolower($cd['club_name']), $qLower) !== false || strpos(strtolower($cd['requirements']), $qLower) !== false) {
            $matched[] = [
                'id' => $cd['id'],
                'type' => 'club',
                'title' => $cd['title'],
                'organizer' => $cd['club_name'],
                'category' => $cd['category'],
                'deadline' => $cd['deadline'],
                'location' => $cd['location']
            ];
        }
    }

    foreach ($corpDrives as $cd) {
        if (empty($qLower) || strpos(strtolower($cd['job_title']), $qLower) !== false || strpos(strtolower($cd['company_name']), $qLower) !== false || strpos(strtolower($cd['requirements']), $qLower) !== false) {
            $matched[] = [
                'id' => $cd['id'],
                'type' => 'corporate',
                'title' => $cd['job_title'],
                'organizer' => $cd['company_name'],
                'category' => $cd['job_type'],
                'deadline' => $cd['deadline'],
                'min_cgpa' => $cd['min_cgpa']
            ];
        }
    }

    echo json_encode(['success' => true, 'count' => count($matched), 'data' => $matched]);
    exit;
}
