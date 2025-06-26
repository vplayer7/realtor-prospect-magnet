
<?php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    exit('Access denied');
}

try {
    $pdo = getDBConnection();
    
    // Get pagination parameters
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = 20;
    $offset = ($page - 1) * $per_page;
    
    // Get total count
    $count_stmt = $pdo->query("SELECT COUNT(*) as total FROM leads");
    $total_leads = $count_stmt->fetch()['total'];
    $total_pages = ceil($total_leads / $per_page);
    
    // Get leads with pagination
    $stmt = $pdo->prepare("SELECT * FROM leads ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute([$per_page, $offset]);
    $leads = $stmt->fetchAll();
    
} catch (Exception $e) {
    http_response_code(500);
    exit('Database error');
}
?>

<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price Range</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($leads as $lead): ?>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $lead['id']; ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($lead['name']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($lead['email']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($lead['phone']); ?></td>
                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate"><?php echo htmlspecialchars($lead['address']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($lead['property_type'] ?? 'N/A'); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($lead['price_range'] ?? 'N/A'); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('M j, Y H:i', strtotime($lead['created_at'])); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <button onclick="viewLead(<?php echo $lead['id']; ?>)" class="text-blue-600 hover:text-blue-900 mr-2">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button onclick="deleteLead(<?php echo $lead['id']; ?>)" class="text-red-600 hover:text-red-900">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($total_pages > 1): ?>
<div class="px-6 py-4 flex items-center justify-between border-t border-gray-200">
    <div class="flex-1 flex justify-between sm:hidden">
        <?php if ($page > 1): ?>
        <a href="#" onclick="loadLeadsPage(<?php echo $page - 1; ?>)" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Previous</a>
        <?php endif; ?>
        <?php if ($page < $total_pages): ?>
        <a href="#" onclick="loadLeadsPage(<?php echo $page + 1; ?>)" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Next</a>
        <?php endif; ?>
    </div>
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-gray-700">
                Showing <span class="font-medium"><?php echo $offset + 1; ?></span> to <span class="font-medium"><?php echo min($offset + $per_page, $total_leads); ?></span> of <span class="font-medium"><?php echo $total_leads; ?></span> results
            </p>
        </div>
        <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="#" onclick="loadLeadsPage(<?php echo $i; ?>)" class="<?php echo $i == $page ? 'bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>
            </nav>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function loadLeadsPage(page) {
    fetch(`api/get_leads.php?page=${page}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('leads-table-container').innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading leads page:', error);
        });
}

function viewLead(id) {
    // Implement lead detail view
    alert('View lead details for ID: ' + id);
}

function deleteLead(id) {
    if (confirm('Are you sure you want to delete this lead?')) {
        fetch('api/delete_lead.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({id: id})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadLeads();
            } else {
                alert('Error deleting lead: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting lead');
        });
    }
}
</script>
