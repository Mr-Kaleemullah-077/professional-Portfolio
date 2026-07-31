<?php
// ============================================================
//  Database configuration
//  XAMPP default: host localhost, user root, no password
//  Adjust these values if your MySQL setup is different.
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'portfolio_db');

define('SITE_URL', '/portfolio');
define('UPLOAD_DIR', __DIR__ . '/../admin/uploads/');

function db(): mysqli
{
    static $conn = null;
    if ($conn === null) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            $conn->set_charset('utf8mb4');
        } catch (mysqli_sql_exception $e) {
            http_response_code(500);
            die('Database connection failed. Please import database.sql first. Error: ' . htmlspecialchars($e->getMessage()));
        }
    }
    return $conn;
}
