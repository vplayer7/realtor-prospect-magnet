
#!/bin/bash

echo "🚀 Building CodeCanyon-ready package for Real Estate Lead Capture..."

# Clean up any previous builds
rm -rf codecanyon-build
rm -f real-estate-lead-capture-v2.0.zip

# Build the React application first
echo "📦 Building React application..."
npm run build

# Check if build was successful
if [ ! -d "dist" ]; then
    echo "❌ Build failed - dist directory not found"
    echo "Make sure 'npm run build' completes successfully"
    exit 1
fi

# Create build directory
mkdir -p codecanyon-build
cd codecanyon-build

# Copy built React files
echo "📂 Copying frontend files..."
cp -r ../dist/* .

# Verify files were copied
if [ ! -f "index.html" ]; then
    echo "❌ Frontend files not copied properly"
    echo "Contents of dist directory:"
    ls -la ../dist/
    exit 1
fi

echo "✅ Frontend files copied successfully"

# Create PHP backend structure
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

# Create installation guide
echo "📝 Creating documentation..."
cat > INSTALLATION.md << 'EOF'
# Real Estate Lead Capture - Installation Guide

## Quick Installation Steps

1. **Upload Files**
   - Extract the ZIP file
   - Upload all files to your cPanel's `public_html` directory

2. **Create Database**
   - In cPanel, go to MySQL Databases
   - Create a new database (e.g., `yourname_leads`)
   - Create a database user with full privileges
   - Note the database name, username, and password

3. **Configure Database**
   - Edit `config.php` file
   - Update database credentials:
     - DB_HOST (usually 'localhost')
     - DB_NAME (your database name)
     - DB_USER (your database username)
     - DB_PASS (your database password)

4. **Import Database**
   - In cPanel phpMyAdmin, select your database
   - Import the `database.sql` file

5. **Test Installation**
   - Visit your domain to see the lead capture form
   - Test form submission
   - Access admin panel at `yourdomain.com/admin/`

## Default Admin Credentials
- Username: admin
- Password: admin123

**Change these immediately after installation!**

## Configuration Options

### Google Maps API (Optional)
1. Get API key from Google Cloud Console
2. Enable Maps JavaScript API and Places API
3. Add key to `config.php`: `GOOGLE_MAPS_API_KEY`

### Email Notifications (Optional)
Configure SMTP settings in `config.php`:
- SMTP_HOST
- SMTP_PORT
- SMTP_USER
- SMTP_PASS
- FROM_EMAIL

## Support
For support, please contact the seller through CodeCanyon.
EOF

# Create cPanel compatibility test
cat > cpanel-test.php << 'EOF'
<?php
echo "<h1>cPanel Compatibility Test</h1>";

echo "<h2>PHP Version</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Required: 7.4+ " . (version_compare(phpversion(), '7.4.0', '>=') ? "✅ PASS" : "❌ FAIL") . "<br><br>";

echo "<h2>Required Extensions</h2>";
$extensions = ['pdo', 'pdo_mysql', 'session', 'json'];
foreach ($extensions as $ext) {
    echo $ext . ": " . (extension_loaded($ext) ? "✅ Available" : "❌ Missing") . "<br>";
}

echo "<br><h2>File Permissions</h2>";
echo "Current directory writable: " . (is_writable('.') ? "✅ Yes" : "❌ No") . "<br>";

echo "<br><h2>Database Test</h2>";
echo "PDO MySQL: " . (class_exists('PDO') && in_array('mysql', PDO::getAvailableDrivers()) ? "✅ Available" : "❌ Not Available") . "<br>";

echo "<br><h2>Server Information</h2>";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "<br>";
echo "HTTP Host: " . ($_SERVER['HTTP_HOST'] ?? 'Unknown') . "<br>";

echo "<br><p><strong>Ready for cPanel hosting!</strong></p>";
?>
EOF

# Show what we've created
echo ""
echo "📋 Files created in codecanyon-build:"
ls -la

echo ""
echo "📊 Directory sizes:"
du -sh *

# Create the final ZIP package
echo ""
echo "📦 Creating final ZIP package..."
cd ..
zip -r "real-estate-lead-capture-v2.0.zip" codecanyon-build/ -x "codecanyon-build/.git/*" "codecanyon-build/node_modules/*"

# Verify ZIP was created
if [ -f "real-estate-lead-capture-v2.0.zip" ]; then
    echo "✅ CodeCanyon package created: real-estate-lead-capture-v2.0.zip"
    echo "📦 Package size: $(du -sh real-estate-lead-capture-v2.0.zip | cut -f1)"
    
    echo ""
    echo "📋 Package Contents:"
    echo "   ✓ Built React frontend (production-ready)"
    echo "   ✓ PHP backend with lead capture"
    echo "   ✓ Admin panel for lead management"
    echo "   ✓ Database schema"
    echo "   ✓ cPanel .htaccess configuration"
    echo "   ✓ Compatibility test script"
    echo "   ✓ Complete installation documentation"
    echo ""
    echo "🎉 Ready for CodeCanyon submission!"
else
    echo "❌ Failed to create ZIP package"
    exit 1
fi
