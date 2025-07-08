
<?php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    exit('Access denied');
}

try {
    $pdo = getDBConnection();
    
    // Get settings
    $stmt = $pdo->query("SELECT * FROM settings ORDER BY setting_key");
    $settings = $stmt->fetchAll();
    
    // Get quiz questions
    $stmt = $pdo->query("SELECT * FROM quiz_questions WHERE is_active = 1 ORDER BY question_order");
    $questions = $stmt->fetchAll();
    
} catch (Exception $e) {
    http_response_code(500);
    exit('Database error');
}
?>

<div class="space-y-8">
    <!-- General Settings -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="text-lg font-semibold mb-4">General Settings</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php 
            $generalSettings = ['google_maps_api_key', 'admin_email', 'company_name'];
            foreach ($settings as $setting): 
                if (in_array($setting['setting_key'], $generalSettings)):
            ?>
            <div>
                <label for="<?php echo $setting['setting_key']; ?>" class="block text-sm font-medium text-gray-700 mb-2">
                    <?php echo ucwords(str_replace('_', ' ', $setting['setting_key'])); ?>
                </label>
                <p class="text-sm text-gray-500 mb-2"><?php echo htmlspecialchars($setting['description']); ?></p>
                <input type="text" id="<?php echo $setting['setting_key']; ?>" name="<?php echo $setting['setting_key']; ?>" 
                       value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                       class="settings-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <?php endif; endforeach; ?>
        </div>
    </div>

    <!-- Page Content Settings -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="text-lg font-semibold mb-4">Page Content</h4>
        <div class="space-y-6">
            <?php 
            $contentSettings = ['site_title', 'site_subtitle', 'background_image'];
            foreach ($settings as $setting): 
                if (in_array($setting['setting_key'], $contentSettings)):
            ?>
            <div>
                <label for="<?php echo $setting['setting_key']; ?>" class="block text-sm font-medium text-gray-700 mb-2">
                    <?php echo ucwords(str_replace('_', ' ', $setting['setting_key'])); ?>
                </label>
                <p class="text-sm text-gray-500 mb-2"><?php echo htmlspecialchars($setting['description']); ?></p>
                <input type="url" id="<?php echo $setting['setting_key']; ?>" name="<?php echo $setting['setting_key']; ?>" 
                       value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                       class="settings-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <?php endif; endforeach; ?>
        </div>
    </div>

    <!-- Step 1 Settings -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="text-lg font-semibold mb-4">Step 1 - Address Search</h4>
        <div class="space-y-6">
            <?php 
            foreach ($settings as $setting): 
                if (strpos($setting['setting_key'], 'step1_') === 0):
            ?>
            <div>
                <label for="<?php echo $setting['setting_key']; ?>" class="block text-sm font-medium text-gray-700 mb-2">
                    <?php echo ucwords(str_replace(['step1_', '_'], ['', ' '], $setting['setting_key'])); ?>
                </label>
                <p class="text-sm text-gray-500 mb-2"><?php echo htmlspecialchars($setting['description']); ?></p>
                <input type="text" id="<?php echo $setting['setting_key']; ?>" name="<?php echo $setting['setting_key']; ?>" 
                       value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                       class="settings-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <?php endif; endforeach; ?>
        </div>
    </div>

    <!-- Quiz Questions -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="text-lg font-semibold mb-4">Quiz Questions</h4>
        <div id="quiz-questions">
            <?php foreach ($questions as $index => $question): ?>
            <div class="quiz-question-item bg-white p-4 rounded border mb-4">
                <div class="flex justify-between items-center mb-3">
                    <h5 class="font-medium">Question <?php echo $index + 1; ?></h5>
                    <button type="button" class="configure-btn text-blue-600 hover:text-blue-800" data-question-id="<?php echo $question['question_id']; ?>">
                        <i class="fas fa-cog"></i> Configure
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Question Text</label>
                        <input type="text" name="question_title_<?php echo $question['question_id']; ?>" 
                               value="<?php echo htmlspecialchars($question['title']); ?>"
                               class="settings-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Icon Class</label>
                        <input type="text" name="question_icon_<?php echo $question['question_id']; ?>" 
                               value="<?php echo htmlspecialchars($question['icon']); ?>"
                               class="settings-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div id="question-options-<?php echo $question['question_id']; ?>" class="question-options hidden">
                    <h6 class="font-medium mb-2">Answer Options</h6>
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM quiz_options WHERE question_id = ? AND is_active = 1 ORDER BY option_order");
                    $stmt->execute([$question['question_id']]);
                    $options = $stmt->fetchAll();
                    foreach ($options as $option):
                    ?>
                    <div class="flex gap-2 mb-2">
                        <input type="text" name="option_value_<?php echo $option['id']; ?>" 
                               value="<?php echo htmlspecialchars($option['option_value']); ?>"
                               placeholder="Value" class="settings-input flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="text" name="option_label_<?php echo $option['id']; ?>" 
                               value="<?php echo htmlspecialchars($option['option_label']); ?>"
                               placeholder="Label" class="settings-input flex-2 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Step 2 Settings -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="text-lg font-semibold mb-4">Step 2 - Quiz Navigation</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php 
            foreach ($settings as $setting): 
                if (strpos($setting['setting_key'], 'step2_') === 0):
            ?>
            <div>
                <label for="<?php echo $setting['setting_key']; ?>" class="block text-sm font-medium text-gray-700 mb-2">
                    <?php echo ucwords(str_replace(['step2_', '_'], ['', ' '], $setting['setting_key'])); ?>
                </label>
                <input type="text" id="<?php echo $setting['setting_key']; ?>" name="<?php echo $setting['setting_key']; ?>" 
                       value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                       class="settings-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <?php endif; endforeach; ?>
        </div>
    </div>

    <!-- Step 3 Settings -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="text-lg font-semibold mb-4">Step 3 - Lead Capture Form</h4>
        <div class="space-y-6">
            <?php 
            foreach ($settings as $setting): 
                if (strpos($setting['setting_key'], 'step3_') === 0):
            ?>
            <div>
                <label for="<?php echo $setting['setting_key']; ?>" class="block text-sm font-medium text-gray-700 mb-2">
                    <?php echo ucwords(str_replace(['step3_', '_'], ['', ' '], $setting['setting_key'])); ?>
                </label>
                <p class="text-sm text-gray-500 mb-2"><?php echo htmlspecialchars($setting['description']); ?></p>
                <?php if (in_array($setting['setting_key'], ['privacy_text'])): ?>
                <textarea id="<?php echo $setting['setting_key']; ?>" name="<?php echo $setting['setting_key']; ?>" rows="3" 
                          class="settings-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($setting['setting_value']); ?></textarea>
                <?php else: ?>
                <input type="text" id="<?php echo $setting['setting_key']; ?>" name="<?php echo $setting['setting_key']; ?>" 
                       value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                       class="settings-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <?php endif; ?>
            </div>
            <?php endif; endforeach; ?>
        </div>
    </div>

    <!-- Step 4 Settings -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="text-lg font-semibold mb-4">Step 4 - Results Page</h4>
        <div class="space-y-6">
            <?php 
            foreach ($settings as $setting): 
                if (strpos($setting['setting_key'], 'step4_') === 0):
            ?>
            <div>
                <label for="<?php echo $setting['setting_key']; ?>" class="block text-sm font-medium text-gray-700 mb-2">
                    <?php echo ucwords(str_replace(['step4_', '_'], ['', ' '], $setting['setting_key'])); ?>
                </label>
                <input type="text" id="<?php echo $setting['setting_key']; ?>" name="<?php echo $setting['setting_key']; ?>" 
                       value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                       class="settings-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <?php endif; endforeach; ?>
        </div>
    </div>

    <!-- Success Modal Settings -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="text-lg font-semibold mb-4">Success Modal</h4>
        <div class="space-y-6">
            <?php 
            foreach ($settings as $setting): 
                if (strpos($setting['setting_key'], 'success_') === 0):
            ?>
            <div>
                <label for="<?php echo $setting['setting_key']; ?>" class="block text-sm font-medium text-gray-700 mb-2">
                    <?php echo ucwords(str_replace(['success_', '_'], ['', ' '], $setting['setting_key'])); ?>
                </label>
                <p class="text-sm text-gray-500 mb-2"><?php echo htmlspecialchars($setting['description']); ?></p>
                <?php if (in_array($setting['setting_key'], ['success_message'])): ?>
                <textarea id="<?php echo $setting['setting_key']; ?>" name="<?php echo $setting['setting_key']; ?>" rows="2" 
                          class="settings-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($setting['setting_value']); ?></textarea>
                <?php else: ?>
                <input type="text" id="<?php echo $setting['setting_key']; ?>" name="<?php echo $setting['setting_key']; ?>" 
                       value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                       class="settings-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <?php endif; ?>
            </div>
            <?php endif; endforeach; ?>
        </div>
    </div>

    <!-- Legacy Settings -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="text-lg font-semibold mb-4">Legacy Settings</h4>
        <div class="space-y-6">
            <?php 
            $legacySettings = ['privacy_text'];
            foreach ($settings as $setting): 
                if (in_array($setting['setting_key'], $legacySettings)):
            ?>
            <div>
                <label for="<?php echo $setting['setting_key']; ?>" class="block text-sm font-medium text-gray-700 mb-2">
                    <?php echo ucwords(str_replace('_', ' ', $setting['setting_key'])); ?>
                </label>
                <p class="text-sm text-gray-500 mb-2"><?php echo htmlspecialchars($setting['description']); ?></p>
                <textarea id="<?php echo $setting['setting_key']; ?>" name="<?php echo $setting['setting_key']; ?>" rows="3" 
                          class="settings-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($setting['setting_value']); ?></textarea>
            </div>
            <?php endif; endforeach; ?>
        </div>
    </div>
    
    <div class="pt-4">
        <button type="button" id="save-settings-btn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-save mr-2"></i>Save Settings
        </button>
    </div>
</div>

<!-- Success notification div -->
<div id="notification" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg hidden z-50">
    <i class="fas fa-check-circle mr-2"></i>
    <span id="notification-message">Settings saved successfully!</span>
</div>

<style>
.hidden { 
    display: none !important; 
}
.question-options {
    display: block !important; /* Remove hidden state */
    margin-top: 15px;
    padding: 15px;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}
</style>

<script>
console.log('Settings page loaded');

// Add event listeners for configure buttons
document.querySelectorAll('.configure-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const questionId = this.getAttribute('data-question-id');
        console.log('Configure button clicked for question:', questionId);
        toggleQuestionOptions(questionId);
    });
});

// Add event listener for save button
const saveBtn = document.getElementById('save-settings-btn');
if (saveBtn) {
    saveBtn.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('Save button clicked');
        saveSettings();
    });
} else {
    console.error('Save button not found');
}

function toggleQuestionOptions(questionId) {
    console.log('Toggling options for question:', questionId);
    const optionsDiv = document.getElementById('question-options-' + questionId);
    if (optionsDiv) {
        if (optionsDiv.classList.contains('hidden')) {
            optionsDiv.classList.remove('hidden');
            console.log('Showed options for question:', questionId);
        } else {
            optionsDiv.classList.add('hidden');
            console.log('Hidden options for question:', questionId);
        }
    } else {
        console.error('Could not find element with ID: question-options-' + questionId);
    }
}

function showNotification(message, isSuccess = true) {
    const notification = document.getElementById('notification');
    const messageSpan = document.getElementById('notification-message');
    
    if (notification && messageSpan) {
        messageSpan.textContent = message;
        notification.className = isSuccess 
            ? 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50'
            : 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
        
        notification.classList.remove('hidden');
        
        setTimeout(() => {
            notification.classList.add('hidden');
        }, 3000);
    }
}

function handleResponse(response) {
    console.log('Response status:', response.status);
    return response.text().then(text => {
        console.log('Raw response:', text);
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error('Failed to parse JSON:', e);
            throw new Error('Invalid JSON response: ' + text.substring(0, 100) + '...');
        }
    }).then(data => {
        console.log('Parsed response data:', data);
        if (data.success) {
            showNotification('Settings saved successfully!', true);
        } else {
            showNotification('Error saving settings: ' + (data.message || 'Unknown error'), false);
        }
    });
}

function handleError(error) {
    console.error('Fetch error:', error);
    showNotification('Error saving settings: ' + error.message, false);
}

function saveSettings() {
    console.log('Save settings function called');
    
    // Show loading state
    const saveButton = document.getElementById('save-settings-btn');
    if (saveButton) {
        saveButton.disabled = true;
        saveButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
    }
    
    const formData = new FormData();
    
    // Collect ALL inputs, textareas, and selects
    const allInputs = document.querySelectorAll('input, textarea, select');
    console.log('Found total inputs:', allInputs.length);
    
    allInputs.forEach((input, index) => {
        if (input.name && input.name.trim() !== '') {
            formData.append(input.name, input.value);
            console.log(`Input ${index}: ${input.name} = ${input.value}`);
        }
    });
    
    // Add quiz options to form data
    const optionInputs = document.querySelectorAll('input[name^="option_"]');
    optionInputs.forEach(input => {
        formData.append(input.name, input.value);
    });
    
    console.log('Sending request to save_settings.php');
    
    // Fix API path
    fetch('../api/save_settings.php', {
        method: 'POST',
        body: formData
    })
    .then(handleResponse)
    .catch(handleError)
    .finally(() => {
        // Reset button state
        if (saveButton) {
            saveButton.disabled = false;
            saveButton.innerHTML = '<i class="fas fa-save mr-2"></i>Save Settings';
        }
    });
}
</script>
