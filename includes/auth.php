<?php
// includes/auth.php
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/User.php';

function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login.php');
        exit;
    }
}

function getCurrentUser() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    static $user = null;
    if ($user === null) {
        $userModel = new User($GLOBALS['pdo']);
        $user = $userModel->find($_SESSION['user_id']);
    }
    return $user;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function login($userId) {
    $_SESSION['user_id'] = $userId;
    $userModel = new User($GLOBALS['pdo']);
    $userModel->updateLastLogin($userId);
}

function logout() {
    session_destroy();
    header('Location: /login.php');
    exit;
}

