
<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Get form data
    $dbHost = $_POST['db_host'] ?? '';
    $dbName = $_POST['db_name'] ?? '';
    $dbUser = $_POST['db_user'] ?? '';
    $dbPass = $_POST['db_pass'] ?? '';
    $adminUser = $_POST['admin_user'] ?? '';
    $adminEmail = $_POST['admin_email'] ?? '';
    $adminPass = $_POST['admin_pass'] ?? '';
    $googleApiKey = $_POST['google_api_key'] ?? '';
    
    // Validate required fields
    if (empty($dbHost) || empty($dbName) || empty($dbUser) || empty($adminUser) || empty($adminEmail) || empty($adminPass)) {
        throw new Exception('Please fill in all required fields');
    }
    
    // Test database connection
    $pdo = new PDO(
        "mysql:host={$dbHost};dbname={$dbName}",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    // Read and execute SQL file
    $sqlFile = 'database.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception('Database SQL file not found');
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Remove CREATE DATABASE and USE statements since we're already connected
    $sql = preg_replace('/CREATE DATABASE.*?;/i', '', $sql);
    $sql = preg_replace('/USE.*?;/i', '', $sql);
    
    // Split SQL into individual statements and execute them one by one
    $statements = explode(';', $sql);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement) && !preg_match('/^\s*--/', $statement) && !preg_match('/^\s*\/\*/', $statement)) {
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                // Skip ON DUPLICATE KEY UPDATE errors for INSERT statements
                if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                    throw new Exception('SQL Error: ' . $e->getMessage() . ' in statement: ' . substr($statement, 0, 100));
                }
            }
        }
    }
    
    // Verify tables were created
    $requiredTables = ['leads', 'admin_users', 'settings', 'quiz_questions', 'quiz_options'];
    $stmt = $pdo->query("SHOW TABLES");
    $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($requiredTables as $table) {
        if (!in_array($table, $existingTables)) {
            throw new Exception("Required table '{$table}' was not created successfully");
        }
    }
    
    // Update admin user with provided credentials
    $hashedPassword = password_hash($adminPass, PASSWORD_DEFAULT);
    
    // First, try to update existing admin user
    $stmt = $pdo->prepare("UPDATE admin_users SET username = ?, email = ?, password_hash = ? WHERE id = 1");
    $result = $stmt->execute([$adminUser, $adminEmail, $hashedPassword]);
    
    // If no rows were affected, insert new admin user
    if ($stmt->rowCount() == 0) {
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$adminUser, $adminEmail, $hashedPassword]);
    }
    
    // Update settings
    if (!empty($googleApiKey)) {
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'google_maps_api_key'");
        $stmt->execute([$googleApiKey]);
    }
    
    $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'admin_email'");
    $stmt->execute([$adminEmail]);
    
    // Create config.php file
    $configContent = "<?php
// config.php - Configuration file for the real estate lead capture system

// Database configuration
define('DB_HOST', '{$dbHost}');
define('DB_NAME', '{$dbName}');
define('DB_USER', '{$dbUser}');
define('DB_PASS', '{$dbPass}');

// Site configuration
define('SITE_URL', 'https://yourdomain.com');
define('ADMIN_EMAIL', '{$adminEmail}');

// Google Maps API
define('GOOGLE_MAPS_API_KEY', '{$googleApiKey}');

// Email configuration
define('SMTP_HOST', 'your_smtp_host');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your_smtp_username');
define('SMTP_PASS', 'your_smtp_password');
define('FROM_EMAIL', 'noreply@yourdomain.com');
define('FROM_NAME', 'Real Estate Lead Capture');

// Security settings
define('CSRF_SECRET', '" . bin2hex(random_bytes(32)) . "');
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

// Installation marker
define('INSTALLATION_COMPLETE', true);

// Function to get database connection
function getDBConnection() {
    try {
        \$pdo = new PDO(
            \"mysql:host=\" . DB_HOST . \";dbname=\" . DB_NAME,
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return \$pdo;
    } catch (PDOException \$e) {
        if (DEBUG_MODE) {
            die(\"Database connection failed: \" . \$e->getMessage());
        } else {
            die(\"Database connection failed\");
        }
    }
}

// Function to sanitize input
function sanitizeInput(\$input) {
    return htmlspecialchars(strip_tags(trim(\$input)), ENT_QUOTES, 'UTF-8');
}

// Function to validate email
function isValidEmail(\$email) {
    return filter_var(\$email, FILTER_VALIDATE_EMAIL) !== false;
}

// Function to validate phone number
function isValidPhone(\$phone) {
    // Basic phone validation - adjust regex as needed
    return preg_match('/^[\+]?[1-9][\d]{0,15}$/', preg_replace('/[^\d+]/', '', \$phone));
}
?>";
    
    if (file_put_contents('../config.php', $configContent) === false) {
        throw new Exception('Could not create config.php file');
    }
    
    echo json_encode(['success' => true, 'message' => 'Installation completed successfully']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
