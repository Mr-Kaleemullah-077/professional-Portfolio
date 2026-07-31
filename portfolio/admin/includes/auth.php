<?php
// ============================================================
//  Admin authentication guard
// ============================================================
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login(): void
{
    if (empty($_SESSION['admin_id'])) {
        redirect('index.php');
    }
}

function current_admin(): ?array
{
    if (empty($_SESSION['admin_id'])) {
        return null;
    }
    return fetch_one('SELECT id, username, email FROM admin_users WHERE id = ?', 'i', [(int)$_SESSION['admin_id']]);
}
