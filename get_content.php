
<?php
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $pdo = getDBConnection();
    
    // Get all settings
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    $settings_result = $stmt->fetchAll();
    
    $settings = [];
    foreach ($settings_result as $setting) {
        $settings[$setting['setting_key']] = $setting['setting_value'];
    }
    
    // Get quiz questions
    $stmt = $pdo->query("SELECT * FROM quiz_questions WHERE is_active = 1 ORDER BY question_order");
    $questions_result = $stmt->fetchAll();
    
    $questions = [];
    foreach ($questions_result as $question) {
        // Get options for this question
        $stmt = $pdo->prepare("SELECT option_value, option_label FROM quiz_options WHERE question_id = ? AND is_active = 1 ORDER BY option_order");
        $stmt->execute([$question['question_id']]);
        $options = $stmt->fetchAll();
        
        $questions[] = [
            'id' => $question['question_id'],
            'title' => $question['title'],
            'icon' => $question['icon'],
            'options' => $options
        ];
    }
    
    echo json_encode([
        'success' => true,
        'settings' => $settings,
        'questions' => $questions
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
