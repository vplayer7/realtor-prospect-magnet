
<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Get lead statistics
    $stmt = $pdo->query("SELECT COUNT(*) as total_leads FROM leads");
    $total_leads = $stmt->fetch()['total_leads'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as today_leads FROM leads WHERE DATE(created_at) = CURDATE()");
    $today_leads = $stmt->fetch()['today_leads'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as week_leads FROM leads WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $week_leads = $stmt->fetch()['week_leads'];
    
    // Get recent leads
    $stmt = $pdo->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 10");
    $recent_leads = $stmt->fetchAll();
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Real Estate Lead Capture</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Real Estate Lead Capture Admin</h1>
            <div class="flex items-center space-x-4">
                <a href="../index.html" target="_blank" class="bg-green-500 hover:bg-green-700 px-3 py-1 rounded flex items-center">
                    <i class="fas fa-external-link-alt mr-2"></i>View Site
                </a>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                <a href="logout.php" class="bg-red-500 hover:bg-red-700 px-3 py-1 rounded">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-6">
        <!-- Navigation Tabs -->
        <div class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <a href="#" onclick="showTab('dashboard')" id="dashboard-tab" class="tab-link active py-2 px-1 border-b-2 border-blue-500 font-medium text-blue-600">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>
                    <a href="#" onclick="showTab('leads')" id="leads-tab" class="tab-link py-2 px-1 border-b-2 border-transparent font-medium text-gray-500 hover:text-gray-700">
                        <i class="fas fa-users mr-2"></i>Leads
                    </a>
                    <a href="#" onclick="showTab('settings')" id="settings-tab" class="tab-link py-2 px-1 border-b-2 border-transparent font-medium text-gray-500 hover:text-gray-700">
                        <i class="fas fa-cog mr-2"></i>Settings
                    </a>
                </nav>
            </div>
        </div>

        <!-- Dashboard Tab -->
        <div id="dashboard-content" class="tab-content">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-900"><?php echo $total_leads; ?></p>
                            <p class="text-gray-600">Total Leads</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-500">
                            <i class="fas fa-calendar-day text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-900"><?php echo $today_leads; ?></p>
                            <p class="text-gray-600">Today's Leads</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                            <i class="fas fa-calendar-week text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-semibold text-gray-900"><?php echo $week_leads; ?></p>
                            <p class="text-gray-600">This Week</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Leads -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Leads</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($recent_leads as $lead): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($lead['name']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($lead['email']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($lead['phone']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($lead['property_type'] ?? 'N/A'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('M j, Y', strtotime($lead['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Leads Tab -->
        <div id="leads-content" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">All Leads</h3>
                    <div class="flex space-x-2">
                        <button onclick="exportCSV()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-download mr-2"></i>Export CSV
                        </button>
                        <button onclick="refreshLeads()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-refresh mr-2"></i>Refresh
                        </button>
                    </div>
                </div>
                <div id="leads-table-container">
                    <!-- Leads table will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Settings Tab -->
        <div id="settings-content" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Funnel Configuration</h3>
                </div>
                <div class="p-6">
                    <form id="settings-form">
                        <!-- Settings form will be loaded here -->
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab-link').forEach(tab => {
                tab.classList.remove('active', 'border-blue-500', 'text-blue-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected tab content
            document.getElementById(tabName + '-content').classList.remove('hidden');
            
            // Add active class to selected tab
            const activeTab = document.getElementById(tabName + '-tab');
            activeTab.classList.add('active', 'border-blue-500', 'text-blue-600');
            activeTab.classList.remove('border-transparent', 'text-gray-500');
            
            // Load content based on tab
            if (tabName === 'leads') {
                loadLeads();
            } else if (tabName === 'settings') {
                loadSettings();
            }
        }

        function loadLeads() {
            fetch('api/get_leads.php')
                .then(response => response.text())
                .then(html => {
                    document.getElementById('leads-table-container').innerHTML = html;
                })
                .catch(error => {
                    console.error('Error loading leads:', error);
                });
        }

        function loadSettings() {
            fetch('api/get_settings.php')
                .then(response => response.text())
                .then(html => {
                    document.getElementById('settings-form').innerHTML = html;
                })
                .catch(error => {
                    console.error('Error loading settings:', error);
                });
        }

        function exportCSV() {
            window.location.href = 'api/export_csv.php';
        }

        function refreshLeads() {
            loadLeads();
        }

        // Load leads when page loads if on leads tab
        document.addEventListener('DOMContentLoaded', function() {
            // Default to dashboard tab
            showTab('dashboard');
        });
    </script>
</body>
</html>
