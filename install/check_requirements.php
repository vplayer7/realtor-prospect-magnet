
<?php
header('Content-Type: application/json');

$requirements = [];

// Check PHP version
if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    $requirements[] = [
        'name' => 'PHP Version',
        'status' => 'success',
        'message' => 'PHP ' . PHP_VERSION . ' (Required: 7.4+)'
    ];
} else {
    $requirements[] = [
        'name' => 'PHP Version',
        'status' => 'error',
        'message' => 'PHP ' . PHP_VERSION . ' (Required: 7.4+)'
    ];
}

// Check PDO MySQL
if (extension_loaded('pdo_mysql')) {
    $requirements[] = [
        'name' => 'PDO MySQL',
        'status' => 'success',
        'message' => 'Available'
    ];
} else {
    $requirements[] = [
        'name' => 'PDO MySQL',
        'status' => 'error',
        'message' => 'Not available - Required for database operations'
    ];
}

// Check file permissions
$configWritable = is_writable('../');
if ($configWritable) {
    $requirements[] = [
        'name' => 'File Permissions',
        'status' => 'success',
        'message' => 'Root directory is writable'
    ];
} else {
    $requirements[] = [
        'name' => 'File Permissions',
        'status' => 'error',
        'message' => 'Root directory is not writable'
    ];
}

// Check mail function
if (function_exists('mail')) {
    $requirements[] = [
        'name' => 'Mail Function',
        'status' => 'success',
        'message' => 'Available'
    ];
} else {
    $requirements[] = [
        'name' => 'Mail Function',
        'status' => 'error',
        'message' => 'Not available - Required for notifications'
    ];
}

echo json_encode(['requirements' => $requirements]);
?>
