
<?php
session_start();
require_once '../../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Test database connection
    $pdo->query("SELECT 1");
    
    // Log the POST data for debugging
    error_log("POST data: " . json_encode($_POST));
    
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
    
    // Process all POST data for quiz questions and options
    foreach ($_POST as $key => $value) {
        // Update quiz questions
        if (strpos($key, 'question_title_') === 0) {
            $question_id = str_replace('question_title_', '', $key);
            $stmt = $pdo->prepare("UPDATE quiz_questions SET title = ?, updated_at = NOW() WHERE question_id = ?");
            $stmt->execute([sanitizeInput($value), $question_id]);
            $updated_count++;
        }
        
        if (strpos($key, 'question_icon_') === 0) {
            $question_id = str_replace('question_icon_', '', $key);
            $stmt = $pdo->prepare("UPDATE quiz_questions SET icon = ?, updated_at = NOW() WHERE question_id = ?");
            $stmt->execute([sanitizeInput($value), $question_id]);
            $updated_count++;
        }
        
        // Update quiz options
        if (strpos($key, 'option_value_') === 0) {
            $option_id = str_replace('option_value_', '', $key);
            $stmt = $pdo->prepare("UPDATE quiz_options SET option_value = ? WHERE id = ?");
            $stmt->execute([sanitizeInput($value), $option_id]);
            $updated_count++;
        }
        
        if (strpos($key, 'option_label_') === 0) {
            $option_id = str_replace('option_label_', '', $key);
            $stmt = $pdo->prepare("UPDATE quiz_options SET option_label = ? WHERE id = ?");
            $stmt->execute([sanitizeInput($value), $option_id]);
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
