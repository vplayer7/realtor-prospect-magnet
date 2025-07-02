
<?php
// Simple installation script for cPanel hosting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$success = false;
$errors = [];
$step = $_GET['step'] ?? 'config';

// Default configuration
$config = [
    'db_host' => 'localhost',
    'db_name' => 'real_estate_leads',
    'db_user' => '',
    'db_pass' => '',
    'admin_user' => 'admin',
    'admin_email' => 'admin@yourdomain.com',
    'admin_pass' => 'admin123',
    'google_api_key' => ''
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'install') {
    $config = array_merge($config, $_POST);
    
    try {
        // Test database connection
        $pdo = new PDO(
            "mysql:host={$config['db_host']};dbname={$config['db_name']}",
            $config['db_user'],
            $config['db_pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // Read and execute database schema
        $sql = file_get_contents('install/database.sql');
        if ($sql === false) {
            throw new Exception('Could not read database.sql file');
        }
        
        // Execute SQL statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        
        // Update admin credentials
        $hashedPassword = password_hash($config['admin_pass'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admin_users SET username = ?, email = ?, password_hash = ? WHERE id = 1");
        $stmt->execute([$config['admin_user'], $config['admin_email'], $hashedPassword]);
        
        // Update Google Maps API key if provided
        if (!empty($config['google_api_key'])) {
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'google_maps_api_key'");
            $stmt->execute([$config['google_api_key']]);
        }
        
        // Create config.php
        $configContent = "<?php
// Auto-generated configuration file

// Database configuration
define('DB_HOST', '{$config['db_host']}');
define('DB_NAME', '{$config['db_name']}');
define('DB_USER', '{$config['db_user']}');
define('DB_PASS', '{$config['db_pass']}');

// Site configuration
define('SITE_URL', 'https://{$_SERVER['HTTP_HOST']}');
define('ADMIN_EMAIL', '{$config['admin_email']}');

// Google Maps API
define('GOOGLE_MAPS_API_KEY', '{$config['google_api_key']}');

// Email configuration
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('FROM_EMAIL', '{$config['admin_email']}');
define('FROM_NAME', 'Real Estate Lead Capture');

// Security settings
define('CSRF_SECRET', '" . bin2hex(random_bytes(32)) . "');
define('SESSION_TIMEOUT', 3600);

// Error reporting
define('DEBUG_MODE', false);
error_reporting(0);
ini_set('display_errors', 0);

// Timezone
date_default_timezone_set('America/New_York');

// Installation complete marker
define('INSTALLATION_COMPLETE', true);

// Database connection function
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
        die(\"Database connection failed\");
    }
}

// Utility functions
function sanitizeInput(\$input) {
    return htmlspecialchars(strip_tags(trim(\$input)), ENT_QUOTES, 'UTF-8');
}

function isValidEmail(\$email) {
    return filter_var(\$email, FILTER_VALIDATE_EMAIL) !== false;
}

function isValidPhone(\$phone) {
    return preg_match('/^[\+]?[1-9][\d]{0,15}$/', preg_replace('/[^\d+]/', '', \$phone));
}
?>";
        
        if (file_put_contents('config.php', $configContent) === false) {
            throw new Exception('Could not write config.php file');
        }
        
        $success = true;
        
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}

// Check if already installed
if (file_exists('config.php')) {
    $configContent = file_get_contents('config.php');
    if (strpos($configContent, 'INSTALLATION_COMPLETE') !== false) {
        echo '<div style="text-align: center; margin-top: 50px; font-family: Arial;">
                <h2>✅ Installation Already Complete</h2>
                <p>Your Real Estate Lead Capture system is ready!</p>
                <p><a href="admin/" style="background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Go to Admin Panel</a></p>
                <p><a href="index.html" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px;">View Lead Capture Form</a></p>
              </div>';
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple Installation - Real Estate Lead Capture</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="password"] { 
            width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; 
        }
        .btn { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #005a8b; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .info { background: #d1ecf1; color: #0c5460; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <h1>🏠 Real Estate Lead Capture - Simple Installation</h1>
    
    <?php if ($success): ?>
        <div class="success">
            <h2>✅ Installation Successful!</h2>
            <p>Your system has been installed successfully.</p>
            <p><strong>Admin Credentials:</strong></p>
            <ul>
                <li>Username: <?= htmlspecialchars($config['admin_user']) ?></li>
                <li>Email: <?= htmlspecialchars($config['admin_email']) ?></li>
                <li>Password: <?= htmlspecialchars($config['admin_pass']) ?></li>
            </ul>
            <p>
                <a href="admin/" class="btn">Go to Admin Panel</a>
                <a href="index.html" class="btn" style="background: #28a745; margin-left: 10px;">View Lead Form</a>
            </p>
            <div class="info">
                <strong>Important:</strong> Delete this file (simple-install.php) and the install/ folder for security.
            </div>
        </div>
    <?php else: ?>
        <?php if (!empty($errors)): ?>
            <div class="error">
                <strong>Errors:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="info">
            <strong>Before you start:</strong>
            <ol>
                <li>Create a MySQL database in cPanel</li>
                <li>Create a database user with full privileges</li>
                <li>Note down the database name, username, and password</li>
            </ol>
        </div>
        
        <form method="post" action="?step=install">
            <h2>Database Configuration</h2>
            
            <div class="form-group">
                <label>Database Host:</label>
                <input type="text" name="db_host" value="<?= htmlspecialchars($config['db_host']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Database Name:</label>
                <input type="text" name="db_name" value="<?= htmlspecialchars($config['db_name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Database Username:</label>
                <input type="text" name="db_user" value="<?= htmlspecialchars($config['db_user']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Database Password:</label>
                <input type="password" name="db_pass" value="<?= htmlspecialchars($config['db_pass']) ?>">
            </div>
            
            <h2>Admin Account</h2>
            
            <div class="form-group">
                <label>Admin Username:</label>
                <input type="text" name="admin_user" value="<?= htmlspecialchars($config['admin_user']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Admin Email:</label>
                <input type="email" name="admin_email" value="<?= htmlspecialchars($config['admin_email']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Admin Password:</label>
                <input type="password" name="admin_pass" value="<?= htmlspecialchars($config['admin_pass']) ?>" required>
            </div>
            
            <h2>Optional Settings</h2>
            
            <div class="form-group">
                <label>Google Maps API Key (optional):</label>
                <input type="text" name="google_api_key" value="<?= htmlspecialchars($config['google_api_key']) ?>">
            </div>
            
            <button type="submit" class="btn">Install System</button>
        </form>
    <?php endif; ?>
</body>
</html>
