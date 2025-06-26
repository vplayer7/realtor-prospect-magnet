
<?php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    exit('Access denied');
}

try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM settings ORDER BY setting_key");
    $settings = $stmt->fetchAll();
} catch (Exception $e) {
    http_response_code(500);
    exit('Database error');
}
?>

<div class="space-y-6">
    <?php foreach ($settings as $setting): ?>
    <div>
        <label for="<?php echo $setting['setting_key']; ?>" class="block text-sm font-medium text-gray-700 mb-2">
            <?php echo ucwords(str_replace('_', ' ', $setting['setting_key'])); ?>
        </label>
        <p class="text-sm text-gray-500 mb-2"><?php echo htmlspecialchars($setting['description']); ?></p>
        
        <?php if (in_array($setting['setting_key'], ['privacy_text', 'success_message'])): ?>
        <textarea id="<?php echo $setting['setting_key']; ?>" name="<?php echo $setting['setting_key']; ?>" rows="3" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($setting['setting_value']); ?></textarea>
        <?php else: ?>
        <input type="text" id="<?php echo $setting['setting_key']; ?>" name="<?php echo $setting['setting_key']; ?>" 
               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    
    <div class="pt-4">
        <button type="button" onclick="saveSettings()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-save mr-2"></i>Save Settings
        </button>
    </div>
</div>

<script>
function saveSettings() {
    const formData = new FormData();
    
    // Collect all form data
    <?php foreach ($settings as $setting): ?>
    formData.append('<?php echo $setting['setting_key']; ?>', document.getElementById('<?php echo $setting['setting_key']; ?>').value);
    <?php endforeach; ?>
    
    fetch('api/save_settings.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Settings saved successfully!');
        } else {
            alert('Error saving settings: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving settings');
    });
}
</script>
