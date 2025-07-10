<?php
require_once 'get_content.php';
$data = json_decode(file_get_contents('php://input'), true);

// Get content from get_content.php
$content_response = file_get_contents($_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/get_content.php');
$content_data = json_decode($content_response, true);

if ($content_data && $content_data['success']) {
    $settings = $content_data['settings'];
    $questions = $content_data['questions'];
} else {
    // Fallback to direct database query
    try {
        require_once 'config.php';
        $pdo = getDBConnection();
        
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
        $settings_result = $stmt->fetchAll();
        
        $settings = [];
        foreach ($settings_result as $setting) {
            $settings[$setting['setting_key']] = $setting['setting_value'];
        }
        
        $stmt = $pdo->query("SELECT * FROM quiz_questions WHERE is_active = 1 ORDER BY question_order");
        $questions_result = $stmt->fetchAll();
        
        $questions = [];
        foreach ($questions_result as $question) {
            $stmt = $pdo->prepare("SELECT option_value, option_label FROM quiz_options WHERE question_id = ? AND is_active = 1 ORDER BY option_order");
            $stmt->execute([$question['question_id']]);
            $options = $stmt->fetchAll();
            
            $questions[] = [
                'id' => $question['question_id'],
                'title' => $question['title'],
                'icon' => $question['icon'],
                'options' => $options
            ];
        }
    } catch (Exception $e) {
        // Use default values if database fails
        $settings = [
            'site_title' => 'Real Estate Lead Capture - Find Your Dream Home',
            'site_description' => 'Professional real estate lead capture system for brokers and agents. Generate qualified leads with our interactive property search and quiz system.',
            'step1_title' => 'Find Your Dream Home',
            'step1_subtitle' => 'Enter your desired location to get started',
            'step1_placeholder' => 'Enter address, city, or ZIP code',
            'step1_button_text' => 'Start Your Search',
            'step1_powered_by' => 'Powered by Google Maps • Trusted by thousands of buyers',
            'step2_progress_text' => 'Question {current} of {total}',
            'step3_title' => 'Almost There!',
            'step3_subtitle' => 'Let us know how to reach you with the perfect property matches',
            'step3_privacy_title' => 'Your Privacy Matters',
            'step3_privacy_text' => 'We\'ll only use your information to send you relevant property listings and market updates. You can unsubscribe at any time.',
            'step4_title' => 'Your Property Search Results',
            'step4_subtitle' => 'Based on your preferences for',
            'step4_preferences_title' => 'Your Preferences',
            'success_title' => 'Thank You',
            'success_subtitle' => 'Your property search preferences have been submitted successfully.',
            'success_next_steps_title' => 'What happens next?',
            'background_image_url' => 'https://images.unsplash.com/photo-1487958449943-2429e8be8625?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80',
            'google_maps_api_key' => 'YOUR_GOOGLE_MAPS_API_KEY'
        ];
        $questions = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($settings['site_title'] ?? 'Real Estate Lead Capture - Find Your Dream Home'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($settings['site_description'] ?? 'Professional real estate lead capture system for brokers and agents. Generate qualified leads with our interactive property search and quiz system.'); ?>">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo htmlspecialchars($settings['site_title'] ?? 'Real Estate Lead Capture - Find Your Dream Home'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($settings['site_description'] ?? 'Professional real estate lead capture system for brokers and agents.'); ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?php echo htmlspecialchars($settings['background_image_url'] ?? 'https://images.unsplash.com/photo-1487958449943-2429e8be8625?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80'); ?>">
    
    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($settings['site_title'] ?? 'Real Estate Lead Capture - Find Your Dream Home'); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($settings['site_description'] ?? 'Professional real estate lead capture system for brokers and agents.'); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($settings['background_image_url'] ?? 'https://images.unsplash.com/photo-1487958449943-2429e8be8625?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80'); ?>">
    
    <!-- Dynamic CSS with PHP variables -->
    <style>
        :root {
            --background-image-url: url('<?php echo htmlspecialchars($settings['background_image_url'] ?? 'https://images.unsplash.com/photo-1487958449943-2429e8be8625?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80'); ?>');
        }
    </style>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Step 1: Address Search -->
    <div id="step1" class="step active">
        <div class="address-search-container">
            <div class="search-card">
                <div class="icon-container">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h1><?php echo htmlspecialchars($settings['step1_title'] ?? 'Find Your Dream Home'); ?></h1>
                <p><?php echo htmlspecialchars($settings['step1_subtitle'] ?? 'Enter your desired location to get started'); ?></p>
                
                <div class="search-input-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="addressInput" placeholder="<?php echo htmlspecialchars($settings['step1_placeholder'] ?? 'Enter address, city, or ZIP code'); ?>">
                </div>
                
                <button id="startSearchBtn" class="primary-btn" disabled>
                    <?php echo htmlspecialchars($settings['step1_button_text'] ?? 'Start Your Search'); ?>
                </button>
                
                <p class="powered-by">
                    <?php echo htmlspecialchars($settings['step1_powered_by'] ?? 'Powered by Google Maps • Trusted by thousands of buyers'); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Step 2: Property Quiz -->
    <div id="step2" class="step">
        <div class="quiz-container">
            <div class="quiz-card">
                <div class="quiz-header">
                    <div class="quiz-icon">
                        <i id="questionIcon" class="fas fa-home"></i>
                    </div>
                    <h2 id="questionTitle">What type of property are you looking for?</h2>
                    <div class="progress-container">
                        <div class="progress-bar">
                            <div id="progressFill" class="progress-fill"></div>
                        </div>
                        <p id="progressText" class="progress-text"><?php echo htmlspecialchars($settings['step2_progress_text'] ?? 'Question {current} of {total}'); ?></p>
                    </div>
                </div>
                
                <div class="quiz-content">
                    <div id="questionOptions" class="question-options">
                        <!-- Options will be dynamically inserted here -->
                    </div>
                    
                    <div class="quiz-navigation">
                        <button id="quizBackBtn" class="secondary-btn">
                            <i class="fas fa-chevron-left"></i> Back
                        </button>
                        <button id="quizNextBtn" class="primary-btn" disabled>
                            Next <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 3: Lead Capture Form -->
    <div id="step3" class="step">
        <div class="form-container">
            <div class="form-card">
                <div class="form-header">
                    <div class="form-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <h2><?php echo htmlspecialchars($settings['step3_title'] ?? 'Almost There!'); ?></h2>
                    <p><?php echo htmlspecialchars($settings['step3_subtitle'] ?? 'Let us know how to reach you with the perfect property matches'); ?></p>
                </div>
                
                <div class="form-content">
                    <div class="form-group">
                        <label for="fullName">Full Name *</label>
                        <div class="input-container">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" id="fullName" placeholder="Enter your full name" required>
                        </div>
                        <span class="error-message" id="nameError"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <div class="input-container">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" id="email" placeholder="Enter your email address" required>
                        </div>
                        <span class="error-message" id="emailError"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number *</label>
                        <div class="input-container">
                            <i class="fas fa-phone input-icon"></i>
                            <input type="tel" id="phone" placeholder="Enter your phone number" required>
                        </div>
                        <span class="error-message" id="phoneError"></span>
                    </div>
                    
                    <div class="privacy-notice">
                        <i class="fas fa-shield-alt"></i>
                        <div>
                            <h4><?php echo htmlspecialchars($settings['step3_privacy_title'] ?? 'Your Privacy Matters'); ?></h4>
                            <p><?php echo htmlspecialchars($settings['step3_privacy_text'] ?? 'We\'ll only use your information to send you relevant property listings and market updates. You can unsubscribe at any time.'); ?></p>
                        </div>
                    </div>
                    
                    <div class="form-navigation">
                        <button id="formBackBtn" class="secondary-btn">
                            <i class="fas fa-chevron-left"></i> Back
                        </button>
                        <button id="formNextBtn" class="primary-btn">
                            View Results <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 4: Map Results -->
    <div id="step4" class="step">
        <div class="results-container">
            <div class="results-card">
                <div class="results-header">
                    <h2><?php echo htmlspecialchars($settings['step4_title'] ?? 'Your Property Search Results'); ?></h2>
                    <p><?php echo htmlspecialchars($settings['step4_subtitle'] ?? 'Based on your preferences for'); ?> <span id="selectedAddress"></span></p>
                </div>
                
                <div class="results-content">
                    <div class="map-section">
                        <div id="map" class="map-container"></div>
                    </div>
                    
                    <div class="summary-section">
                        <h3><?php echo htmlspecialchars($settings['step4_preferences_title'] ?? 'Your Preferences'); ?></h3>
                        <div id="preferencesSummary" class="preferences-summary">
                            <!-- Preferences will be dynamically inserted here -->
                        </div>
                        
                        <div class="results-navigation">
                            <button id="resultsBackBtn" class="secondary-btn">
                                <i class="fas fa-chevron-left"></i> Back
                            </button>
                            <button id="submitBtn" class="primary-btn">
                                Submit Information
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2><?php echo htmlspecialchars($settings['success_title'] ?? 'Thank You'); ?>, <span id="submittedName"></span>!</h2>
            <p><?php echo htmlspecialchars($settings['success_subtitle'] ?? 'Your property search preferences have been submitted successfully.'); ?></p>
            
            <div class="next-steps">
                <h3><?php echo htmlspecialchars($settings['success_next_steps_title'] ?? 'What happens next?'); ?></h3>
                <div class="next-steps-list">
                    <div class="next-step">
                        <i class="fas fa-envelope"></i>
                        <span>You'll receive matching properties via email within 24 hours</span>
                    </div>
                    <div class="next-step">
                        <i class="fas fa-phone"></i>
                        <span>Our expert agent will call you to discuss your needs</span>
                    </div>
                    <div class="next-step">
                        <i class="fas fa-home"></i>
                        <span>We'll schedule property viewings that match your criteria</span>
                    </div>
                </div>
            </div>
            
            <button onclick="startNewSearch()" class="primary-btn">
                Start Another Search
            </button>
        </div>
    </div>

    <!-- Load Google Maps API dynamically -->
    <script>
        // PHP data for JavaScript
        window.phpData = {
            settings: <?php echo json_encode($settings); ?>,
            questions: <?php echo json_encode($questions); ?>
        };
        
        // Load Google Maps API with the key from settings
        const apiKey = '<?php echo htmlspecialchars($settings['google_maps_api_key'] ?? 'YOUR_GOOGLE_MAPS_API_KEY'); ?>';
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=places&callback=initializeApp`;
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    </script>
    <script src="script.js"></script>
</body>
</html>