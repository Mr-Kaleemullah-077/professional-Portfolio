<?php
// ============================================================
//  Helper functions shared by the portfolio and admin panel
// ============================================================

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function get_setting(string $key, string $default = ''): string
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        try {
            $result = db()->query('SELECT setting_key, setting_value FROM settings');
            while ($row = $result->fetch_assoc()) {
                $cache[$row['setting_key']] = (string)$row['setting_value'];
            }
        } catch (Exception $ex) {
            $cache = [];
        }
    }
    return array_key_exists($key, $cache) && $cache[$key] !== '' ? $cache[$key] : $default;
}

function fetch_all(string $sql, string $types = '', array $params = []): array
{
    $stmt = db()->prepare($sql);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function fetch_one(string $sql, string $types = '', array $params = []): ?array
{
    $rows = fetch_all($sql, $types, $params);
    return $rows[0] ?? null;
}

function execute_query(string $sql, string $types = '', array $params = []): bool
{
    $stmt = db()->prepare($sql);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    return $stmt->execute();
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        $_SESSION[$key] = $value;
        return null;
    }
    if (isset($_SESSION[$key])) {
        $msg = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $msg;
    }
    return null;
}

function format_date(?string $date): string
{
    if (!$date) {
        return '';
    }
    return date('d M Y', strtotime($date));
}

// Handles uploaded image files, returns stored filename or null
function handle_upload(array $file, string $targetDir): ?string
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed with code ' . $file['error']);
    }
    if ($file['size'] > 3 * 1024 * 1024) {
        throw new RuntimeException('File too large (max 3MB).');
    }

    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        throw new RuntimeException('Invalid file type. Allowed: ' . implode(', ', $allowed));
    }

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $name = 'img_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $targetDir . $name)) {
        throw new RuntimeException('Could not save the uploaded file.');
    }
    return $name;
}
