
<?php
session_start();
require_once '../../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Get all settings keys
    $stmt = $pdo->query("SELECT setting_key FROM settings");
    $settings_keys = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Update each setting
    foreach ($settings_keys as $key) {
        if (isset($_POST[$key])) {
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?");
            $stmt->execute([sanitizeInput($_POST[$key]), $key]);
        }
    }
    
    echo json_encode(['success' => true, 'message' => 'Settings updated successfully']);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
