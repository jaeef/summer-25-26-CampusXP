<?php
// ==============================================================================
// CampusXP - UserModel (MySQLi Procedural Data Operations)
// ==============================================================================

/**
 * Fetch a single user by their email address
 */
function getUserByEmail($conn, $email) {
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $user;
}

/**
 * Fetch a single user by primary ID
 */
function getUserById($conn, $id) {
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $user;
}

/**
 * Fetch user by role (used for 1-click fast demo testing logins)
 */
function getUserByRole($conn, $role) {
    $sql = "SELECT * FROM users WHERE role = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $role);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $user;
}

/**
 * Create a new user record
 */
function createUser($conn, $fullName, $email, $passwordHash, $role) {
    $sql = "INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $fullName, $email, $passwordHash, $role);
    $executed = mysqli_stmt_execute($stmt);
    $newId = $executed ? mysqli_insert_id($conn) : false;
    mysqli_stmt_close($stmt);
    return $newId;
}

/**
 * Fetch all registered users
 */
function getAllUsers($conn) {
    $sql = "SELECT id, full_name, email, role, created_at FROM users ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    return $users;
}
