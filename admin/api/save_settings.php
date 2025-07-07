
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
    
    $updated_count = 0;
    
    // Update each setting
    foreach ($settings_keys as $key) {
        if (isset($_POST[$key])) {
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?");
            $stmt->execute([sanitizeInput($_POST[$key]), $key]);
            $updated_count++;
        }
    }
    
    // Update quiz questions
    $stmt = $pdo->query("SELECT question_id FROM quiz_questions WHERE is_active = 1");
    $question_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($question_ids as $question_id) {
        $title_key = "question_title_" . $question_id;
        $icon_key = "question_icon_" . $question_id;
        
        if (isset($_POST[$title_key])) {
            $stmt = $pdo->prepare("UPDATE quiz_questions SET title = ?, updated_at = NOW() WHERE question_id = ?");
            $stmt->execute([sanitizeInput($_POST[$title_key]), $question_id]);
            $updated_count++;
        }
        
        if (isset($_POST[$icon_key])) {
            $stmt = $pdo->prepare("UPDATE quiz_questions SET icon = ?, updated_at = NOW() WHERE question_id = ?");
            $stmt->execute([sanitizeInput($_POST[$icon_key]), $question_id]);
            $updated_count++;
        }
    }
    
    // Update quiz options
    $stmt = $pdo->query("SELECT id FROM quiz_options WHERE is_active = 1");
    $option_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($option_ids as $option_id) {
        $value_key = "option_value_" . $option_id;
        $label_key = "option_label_" . $option_id;
        
        if (isset($_POST[$value_key])) {
            $stmt = $pdo->prepare("UPDATE quiz_options SET option_value = ? WHERE id = ?");
            $stmt->execute([sanitizeInput($_POST[$value_key]), $option_id]);
            $updated_count++;
        }
        
        if (isset($_POST[$label_key])) {
            $stmt = $pdo->prepare("UPDATE quiz_options SET option_label = ? WHERE id = ?");
            $stmt->execute([sanitizeInput($_POST[$label_key]), $option_id]);
            $updated_count++;
        }
    }
    
    echo json_encode([
        'success' => true, 
        'message' => "Settings updated successfully. Updated $updated_count fields.",
        'updated_count' => $updated_count
    ]);
    
} catch (Exception $e) {
    error_log("Settings save error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
