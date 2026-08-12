<?php
// Call session_start() BEFORE including this file.

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

function require_admin() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: alogin.php");
        exit;
    }
}

function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin_logged_in() {
    return isset($_SESSION['admin_id']);
}
