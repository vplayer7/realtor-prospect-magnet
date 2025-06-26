
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
    $input = json_decode(file_get_contents('php://input'), true);
    $lead_id = isset($input['id']) ? intval($input['id']) : 0;
    
    if ($lead_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid lead ID']);
        exit;
    }
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("DELETE FROM leads WHERE id = ?");
    $result = $stmt->execute([$lead_id]);
    
    if ($result && $stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Lead deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lead not found or already deleted']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
