
<?php
session_start();

// Check if already installed
if (file_exists('../config.php')) {
    $config_content = file_get_contents('../config.php');
    if (strpos($config_content, 'INSTALLATION_COMPLETE') !== false) {
        die('<div style="text-align: center; margin-top: 50px; font-family: Arial;"><h2>Installation Already Complete</h2><p>The system has already been installed. Please delete the install folder for security.</p></div>');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real Estate Lead Magnet - Installation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .install-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .logo p {
            color: #666;
            font-size: 16px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .requirements {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .requirements h3 {
            color: #333;
            margin-bottom: 15px;
        }
        
        .requirement-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .requirement-item .status {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 12px;
        }
        
        .status.success { background: #28a745; }
        .status.error { background: #dc3545; }
        
        .step {
            display: none;
        }
        
        .step.active {
            display: block;
        }
        
        .progress-bar {
            width: 100%;
            height: 6px;
            background: #e1e5e9;
            border-radius: 3px;
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            width: 0%;
            transition: width 0.3s;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="logo">
            <h1>🏠 Real Estate Lead Magnet</h1>
            <p>Installation Wizard</p>
        </div>
        
        <div class="progress-bar">
            <div class="progress-fill" id="progressFill"></div>
        </div>
        
        <!-- Step 1: Requirements Check -->
        <div class="step active" id="step1">
            <h2>System Requirements</h2>
            <div class="requirements">
                <h3>Checking Requirements...</h3>
                <div id="requirementsList"></div>
            </div>
            <button class="btn" onclick="checkRequirements()" id="checkBtn">Check Requirements</button>
            <button class="btn" onclick="nextStep()" id="nextBtn1" style="display: none;">Continue to Database Setup</button>
        </div>
        
        <!-- Step 2: Database Configuration -->
        <div class="step" id="step2">
            <h2>Database Configuration</h2>
            <form id="dbForm">
                <div class="form-group">
                    <label for="db_host">Database Host</label>
                    <input type="text" id="db_host" name="db_host" value="localhost" required>
                </div>
                
                <div class="form-group">
                    <label for="db_name">Database Name</label>
                    <input type="text" id="db_name" name="db_name" value="real_estate_leads" required>
                </div>
                
                <div class="form-group">
                    <label for="db_user">Database Username</label>
                    <input type="text" id="db_user" name="db_user" required>
                </div>
                
                <div class="form-group">
                    <label for="db_pass">Database Password</label>
                    <input type="password" id="db_pass" name="db_pass">
                </div>
                
                <button type="button" class="btn" onclick="testConnection()">Test Connection</button>
                <button type="button" class="btn" onclick="nextStep()" id="nextBtn2" style="display: none;">Continue to Admin Setup</button>
            </form>
        </div>
        
        <!-- Step 3: Admin Account -->
        <div class="step" id="step3">
            <h2>Admin Account Setup</h2>
            <form id="adminForm">
                <div class="form-group">
                    <label for="admin_user">Admin Username</label>
                    <input type="text" id="admin_user" name="admin_user" value="admin" required>
                </div>
                
                <div class="form-group">
                    <label for="admin_email">Admin Email</label>
                    <input type="email" id="admin_email" name="admin_email" required>
                </div>
                
                <div class="form-group">
                    <label for="admin_pass">Admin Password</label>
                    <input type="password" id="admin_pass" name="admin_pass" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="google_api_key">Google Maps API Key (Optional)</label>
                    <input type="text" id="google_api_key" name="google_api_key" placeholder="Enter your Google Maps API key">
                </div>
                
                <button type="button" class="btn" onclick="installSystem()">Install System</button>
            </form>
        </div>
        
        <!-- Step 4: Installation Complete -->
        <div class="step" id="step4">
            <h2>Installation Complete!</h2>
            <div class="alert alert-success">
                <strong>Success!</strong> Your Real Estate Lead Magnet has been installed successfully.
            </div>
            
            <div style="margin: 30px 0;">
                <h3>Next Steps:</h3>
                <ul style="margin-left: 20px; line-height: 1.6;">
                    <li>Access your admin panel at: <strong>yourdomain.com/admin/</strong></li>
                    <li>Configure your settings and customize the funnel</li>
                    <li>Set up your email notifications</li>
                    <li>Test the lead capture process</li>
                </ul>
            </div>
            
            <div class="alert alert-warning">
                <strong>Important:</strong> For security reasons, please delete the install folder after installation.
            </div>
            
            <button class="btn" onclick="deleteInstallFolder()" id="deleteBtn">Delete Install Folder & Go to Admin</button>
        </div>
        
        <div id="messageArea"></div>
    </div>

    <script>
        let currentStep = 1;
        let maxSteps = 4;
        
        function updateProgress() {
            const progress = (currentStep / maxSteps) * 100;
            document.getElementById('progressFill').style.width = progress + '%';
        }
        
        function nextStep() {
            document.getElementById('step' + currentStep).classList.remove('active');
            currentStep++;
            document.getElementById('step' + currentStep).classList.add('active');
            updateProgress();
        }
        
        function showMessage(message, type = 'success') {
            const messageArea = document.getElementById('messageArea');
            messageArea.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
            setTimeout(() => {
                messageArea.innerHTML = '';
            }, 5000);
        }
        
        function checkRequirements() {
            const btn = document.getElementById('checkBtn');
            btn.disabled = true;
            btn.textContent = 'Checking...';
            
            fetch('check_requirements.php')
                .then(response => response.json())
                .then(data => {
                    const list = document.getElementById('requirementsList');
                    list.innerHTML = '';
                    
                    let allPassed = true;
                    data.requirements.forEach(req => {
                        const item = document.createElement('div');
                        item.className = 'requirement-item';
                        item.innerHTML = `
                            <div class="status ${req.status}">${req.status === 'success' ? '✓' : '✗'}</div>
                            <span>${req.name}: ${req.message}</span>
                        `;
                        list.appendChild(item);
                        
                        if (req.status !== 'success') allPassed = false;
                    });
                    
                    btn.disabled = false;
                    btn.textContent = 'Check Again';
                    
                    if (allPassed) {
                        document.getElementById('nextBtn1').style.display = 'block';
                        showMessage('All requirements met! You can proceed with installation.');
                    } else {
                        showMessage('Some requirements are not met. Please fix them before continuing.', 'error');
                    }
                });
        }
        
        function testConnection() {
            const formData = new FormData(document.getElementById('dbForm'));
            
            fetch('test_connection.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('Database connection successful!');
                    document.getElementById('nextBtn2').style.display = 'block';
                } else {
                    showMessage('Database connection failed: ' + data.message, 'error');
                }
            });
        }
        
        function installSystem() {
            const dbForm = new FormData(document.getElementById('dbForm'));
            const adminForm = new FormData(document.getElementById('adminForm'));
            
            // Combine both forms
            const formData = new FormData();
            for (let [key, value] of dbForm) formData.append(key, value);
            for (let [key, value] of adminForm) formData.append(key, value);
            
            fetch('install.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    nextStep();
                } else {
                    showMessage('Installation failed: ' + data.message, 'error');
                }
            });
        }
        
        function deleteInstallFolder() {
            if (confirm('Are you sure you want to delete the install folder? This action cannot be undone.')) {
                fetch('delete_install.php', {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '../admin/';
                    } else {
                        showMessage('Could not delete install folder automatically. Please delete it manually for security.', 'warning');
                        setTimeout(() => {
                            window.location.href = '../admin/';
                        }, 3000);
                    }
                });
            }
        }
        
        // Initialize
        updateProgress();
    </script>
</body>
</html>
