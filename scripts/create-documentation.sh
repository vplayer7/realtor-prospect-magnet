
#!/bin/bash

echo "📝 Creating documentation..."

# Create installation guide
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

echo "✅ Documentation created successfully"
