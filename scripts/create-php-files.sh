
#!/bin/bash

echo "🐘 Setting up PHP backend..."

# Copy existing PHP files if they exist
if [ -f "../config.php" ]; then
    cp ../config.php .
    echo "✅ Copied config.php"
fi

if [ -f "../submit.php" ]; then
    cp ../submit.php .
    echo "✅ Copied submit.php"
fi

if [ -f "../get_content.php" ]; then
    cp ../get_content.php .
    echo "✅ Copied get_content.php"
fi

# Copy admin directory if it exists
if [ -d "../admin" ]; then
    cp -r ../admin .
    echo "✅ Copied admin directory"
else
    # Create admin directory structure
    mkdir -p admin/api
fi

# Copy install directory if it exists
if [ -d "../install" ]; then
    cp -r ../install .
    echo "✅ Copied install directory"
else
    # Create install directory
    mkdir -p install
fi

# Copy database.sql if it exists
if [ -f "../database.sql" ]; then
    cp ../database.sql .
    echo "✅ Copied database.sql"
fi

# Copy .htaccess if it exists
if [ -f "../.htaccess" ]; then
    cp ../.htaccess .
    echo "✅ Copied .htaccess"
fi

# Create essential PHP files if they don't exist
if [ ! -f "config.php" ]; then
    echo "📝 Creating config.php..."
    cat > config.php << 'EOF'
<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');

// Security Settings
define('ADMIN_SESSION_TIMEOUT', 3600); // 1 hour
define('CSRF_TOKEN_NAME', 'csrf_token');

// Google Maps API (Optional)
define('GOOGLE_MAPS_API_KEY', '');

// Installation Status
define('INSTALLATION_COMPLETE', false);

// Email Settings (Optional)
define('SMTP_HOST', '');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('FROM_EMAIL', 'noreply@yourdomain.com');
define('FROM_NAME', 'Real Estate Leads');

// Error Reporting (Set to false in production)
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>
EOF
fi

if [ ! -f "submit.php" ]; then
    echo "📝 Creating submit.php..."
    cat > submit.php << 'EOF'
<?php
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON data');
    }
    
    // Validate required fields
    $required_fields = ['name', 'email', 'phone', 'address'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            throw new Exception("Field '{$field}' is required");
        }
    }
    
    // Sanitize input
    $data = [
        'name' => filter_var($input['name'], FILTER_SANITIZE_STRING),
        'email' => filter_var($input['email'], FILTER_SANITIZE_EMAIL),
        'phone' => filter_var($input['phone'], FILTER_SANITIZE_STRING),
        'address' => filter_var($input['address'], FILTER_SANITIZE_STRING),
        'property_type' => filter_var($input['propertyType'] ?? '', FILTER_SANITIZE_STRING),
        'bedrooms' => filter_var($input['bedrooms'] ?? '', FILTER_SANITIZE_STRING),
        'bathrooms' => filter_var($input['bathrooms'] ?? '', FILTER_SANITIZE_STRING),
        'price_range' => filter_var($input['priceRange'] ?? '', FILTER_SANITIZE_STRING),
        'timeline' => filter_var($input['timeline'] ?? '', FILTER_SANITIZE_STRING),
        'financing' => filter_var($input['financing'] ?? '', FILTER_SANITIZE_STRING),
        'coordinates' => json_encode($input['coordinates'] ?? []),
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    // Validate email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address');
    }
    
    // Database connection
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    // Insert lead
    $sql = "INSERT INTO leads (name, email, phone, address, property_type, bedrooms, bathrooms, price_range, timeline, financing, coordinates, created_at) 
            VALUES (:name, :email, :phone, :address, :property_type, :bedrooms, :bathrooms, :price_range, :timeline, :financing, :coordinates, :created_at)";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($data);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Lead captured successfully']);
    } else {
        throw new Exception('Failed to save lead');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
EOF
fi

# Create .htaccess for cPanel compatibility
if [ ! -f ".htaccess" ]; then
    echo "📝 Creating .htaccess..."
    cat > .htaccess << 'EOF'
# Real Estate Lead Capture - cPanel Configuration

# Enable mod_rewrite
RewriteEngine On

# Handle React Router (for single page application)
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.html [L]

# Protect sensitive files
<Files "config.php">
    Order Allow,Deny
    Deny from all
</Files>

<Files "database.sql">
    Order Allow,Deny
    Deny from all
</Files>

# Set proper MIME types
AddType application/javascript .js
AddType text/css .css

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Set cache headers
<IfModule mod_expires.c>
    ExpiresActive on
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
</IfModule>
EOF
fi

echo "✅ PHP backend setup completed"
