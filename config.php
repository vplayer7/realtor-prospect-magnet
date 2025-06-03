
<?php
// config.php - Configuration file for the real estate lead capture system

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'real_estate_leads');
define('DB_USER', 'your_db_username');
define('DB_PASS', 'your_db_password');

// Site configuration
define('SITE_URL', 'https://yourdomain.com');
define('ADMIN_EMAIL', 'admin@yourdomain.com');

// Google Maps API
define('GOOGLE_MAPS_API_KEY', 'YOUR_GOOGLE_MAPS_API_KEY');

// Email configuration
define('SMTP_HOST', 'your_smtp_host');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your_smtp_username');
define('SMTP_PASS', 'your_smtp_password');
define('FROM_EMAIL', 'noreply@yourdomain.com');
define('FROM_NAME', 'Real Estate Lead Capture');

// Security settings
define('CSRF_SECRET', 'your_secret_key_here');
define('SESSION_TIMEOUT', 3600); // 1 hour

// Error reporting (set to false in production)
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('America/New_York');

// Function to get database connection
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        if (DEBUG_MODE) {
            die("Database connection failed: " . $e->getMessage());
        } else {
            die("Database connection failed");
        }
    }
}

// Function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// Function to validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Function to validate phone number
function isValidPhone($phone) {
    // Basic phone validation - adjust regex as needed
    return preg_match('/^[\+]?[1-9][\d]{0,15}$/', preg_replace('/[^\d+]/', '', $phone));
}
?>
