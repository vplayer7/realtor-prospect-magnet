
<?php
header('Content-Type: application/json');

function deleteDirectory($dir) {
    if (!is_dir($dir)) {
        return false;
    }
    
    $files = array_diff(scandir($dir), array('.', '..'));
    
    foreach ($files as $file) {
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($path)) {
            deleteDirectory($path);
        } else {
            unlink($path);
        }
    }
    
    return rmdir($dir);
}

try {
    // Check if installation is complete
    if (!file_exists('../config.php')) {
        throw new Exception('Installation not complete');
    }
    
    $configContent = file_get_contents('../config.php');
    if (strpos($configContent, 'INSTALLATION_COMPLETE') === false) {
        throw new Exception('Installation not complete');
    }
    
    // Delete install directory
    $installDir = __DIR__;
    if (deleteDirectory($installDir)) {
        echo json_encode(['success' => true, 'message' => 'Install folder deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Could not delete install folder']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
