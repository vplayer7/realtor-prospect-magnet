
<?php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    exit('Access denied');
}

try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM leads ORDER BY created_at DESC");
    $leads = $stmt->fetchAll();
    
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="leads_export_' . date('Y-m-d_H-i-s') . '.csv"');
    
    // Create output stream
    $output = fopen('php://output', 'w');
    
    // Write CSV headers
    $headers = [
        'ID', 'Name', 'Email', 'Phone', 'Address', 'Latitude', 'Longitude',
        'Property Type', 'Bedrooms', 'Bathrooms', 'Price Range', 'Timeline', 
        'Financing', 'Created At', 'Updated At'
    ];
    fputcsv($output, $headers);
    
    // Write data rows
    foreach ($leads as $lead) {
        $row = [
            $lead['id'],
            $lead['name'],
            $lead['email'],
            $lead['phone'],
            $lead['address'],
            $lead['coordinates_lat'],
            $lead['coordinates_lng'],
            $lead['property_type'],
            $lead['bedrooms'],
            $lead['bathrooms'],
            $lead['price_range'],
            $lead['timeline'],
            $lead['financing'],
            $lead['created_at'],
            $lead['updated_at']
        ];
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;
    
} catch (Exception $e) {
    http_response_code(500);
    exit('Export failed: ' . $e->getMessage());
}
?>
