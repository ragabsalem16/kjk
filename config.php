<?php
/**
 * Configuration File
 * Database and System Configuration
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'university_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site Configuration
define('SITE_NAME', 'University Management System');
define('SITE_URL', 'http://localhost/php_app');
define('ADMIN_EMAIL', 'admin@university.edu');

// Session Configuration
session_start();

// Timezone
date_default_timezone_set('Africa/Cairo');

// Error Reporting (Disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Functions
function redirect($url)
{
    header("Location: $url");
    exit();
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function getUserType()
{
    return $_SESSION['user_type'] ?? null;
}

function getUserName()
{
    return $_SESSION['user_name'] ?? null;
}

function getUserId()
{
    return $_SESSION['user_id'] ?? null;
}

function logout()
{
    session_destroy();
    redirect('login.php');
}

// Format date
function formatDate($date)
{
    return date('Y-m-d', strtotime($date));
}

// Format datetime
function formatDateTime($datetime)
{
    return date('Y-m-d H:i', strtotime($datetime));
}
?>