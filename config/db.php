<?php
// ==============================================================================
// CampusXP - Database Connection (mysqli procedural, course style)
// ==============================================================================

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "campusxp_aiub";

$conn = @mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    // Fallback if legacy unipulse_aiub database is used
    $conn = @mysqli_connect($host, $user, $pass, "unipulse_aiub");
}

if (!$conn) {
    die("Database Connection failed: " . mysqli_connect_error());
}

// Set the connection charset to handle full Unicode (including emoji)
mysqli_set_charset($conn, "utf8mb4");

/**
 * Clean user input before printing or storing (course pattern)
 */
function cleanInput($data) {
    return htmlspecialchars(stripslashes(trim($data ?? "")));
}

// Kept for older pages that still call sanitize(); new code uses cleanInput()
function sanitize($data) {
    return cleanInput($data);
}

/**
 * Set flash message in session
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get flash message and clear it
 */
function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Get current logged in user details
 */
function get_auth_user() {
    return $_SESSION['user'] ?? null;
}
