
<?php
// submit.php - Handle form submission and store leads in database

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
$host = 'localhost';
$dbname = 'real_estate_leads';
$username = 'your_db_username';
$password = 'your_db_password';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get JSON data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (!$data) {
        throw new Exception('Invalid JSON data');
    }
    
    // Validate required fields
    $required_fields = ['name', 'email', 'phone', 'address'];
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    // Prepare SQL statement
    $sql = "INSERT INTO leads (
        name, email, phone, address, coordinates_lat, coordinates_lng,
        property_type, bedrooms, bathrooms, price_range, timeline, financing,
        created_at
    ) VALUES (
        :name, :email, :phone, :address, :coordinates_lat, :coordinates_lng,
        :property_type, :bedrooms, :bathrooms, :price_range, :timeline, :financing,
        NOW()
    )";
    
    $stmt = $pdo->prepare($sql);
    
    // Bind parameters
    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':phone', $data['phone']);
    $stmt->bindParam(':address', $data['address']);
    $stmt->bindParam(':coordinates_lat', $data['coordinates']['lat'] ?? null);
    $stmt->bindParam(':coordinates_lng', $data['coordinates']['lng'] ?? null);
    $stmt->bindParam(':property_type', $data['propertyType'] ?? null);
    $stmt->bindParam(':bedrooms', $data['bedrooms'] ?? null);
    $stmt->bindParam(':bathrooms', $data['bathrooms'] ?? null);
    $stmt->bindParam(':price_range', $data['priceRange'] ?? null);
    $stmt->bindParam(':timeline', $data['timeline'] ?? null);
    $stmt->bindParam(':financing', $data['financing'] ?? null);
    
    // Execute statement
    $stmt->execute();
    
    // Get the inserted ID
    $leadId = $pdo->lastInsertId();
    
    // Send email notification (optional)
    sendEmailNotification($data);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Lead captured successfully',
        'leadId' => $leadId
    ]);
    
} catch (Exception $e) {
    // Return error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function sendEmailNotification($data) {
    // Configure your email settings here
    $to = 'admin@yourdomain.com'; // Your email address
    $subject = 'New Real Estate Lead Captured';
    
    $message = "New lead captured:\n\n";
    $message .= "Name: " . $data['name'] . "\n";
    $message .= "Email: " . $data['email'] . "\n";
    $message .= "Phone: " . $data['phone'] . "\n";
    $message .= "Address: " . $data['address'] . "\n";
    $message .= "Property Type: " . ($data['propertyType'] ?? 'Not specified') . "\n";
    $message .= "Bedrooms: " . ($data['bedrooms'] ?? 'Not specified') . "\n";
    $message .= "Bathrooms: " . ($data['bathrooms'] ?? 'Not specified') . "\n";
    $message .= "Price Range: " . ($data['priceRange'] ?? 'Not specified') . "\n";
    $message .= "Timeline: " . ($data['timeline'] ?? 'Not specified') . "\n";
    $message .= "Financing: " . ($data['financing'] ?? 'Not specified') . "\n";
    
    $headers = 'From: noreply@yourdomain.com' . "\r\n" .
               'Reply-To: noreply@yourdomain.com' . "\r\n" .
               'X-Mailer: PHP/' . phpversion();
    
    // Send email
    mail($to, $subject, $message, $headers);
}
?>
