// Check subscription
if (!CompanyContext::checkSubscription('brc_risk_assessment')) {
    header('Location: ?page=marketplace');
    exit;
}

$pageTitle = 'BRC Risk Assessment';
$currentPage = 'brc_risk_assessment';

include 'customer/layout/header.php';
include 'customer/layout/sidebar.php';
?>

<div class="module-header">
    <div class="module-icon">
        <i class="fas fa-shield-alt"></i>
    </div>
    <h1 class="mb-2">BRC Risk Assessment</h1>
    <p class="mb-0 opacity-75">Comprehensive BRC-compliant risk assessment and management system</p>
    <div class="position-absolute top-0 end-0 p-3">
        <span class="badge bg-light text-dark fs-6">v2.1</span>
    </div>
</div>

<!-- Dashboard Overview Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="dashboard-card">
            <div class="dashboard-card-icon bg-primary text-white">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h6>Total Risks</h6>
            <h3 id="totalRisks">47</h3>
            <div class="progress mt-2" style="height: 4px;">
                <div class="progress-bar bg-danger" style="width: 17%"></div>
                <div class="progress-bar bg-warning" style="width: 49%"></div>
                <div class="progress-bar bg-success" style="width: 34%"></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="dashboard-card">
            <div class="dashboard-card-icon bg-success text-white">
                <i class="fas fa-check-circle"></i>
            </div>
            <h6>Completed</h6>
            <h3 id="completedRisks">34</h3>
            <small class="text-success">
                <i class="fas fa-arrow-up"></i> +12% this month
            </small>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="dashboard-card">
            <div class="dashboard-card-icon bg-warning text-white">
                <i class="fas fa-clock"></i>
            </div>
            <h6>Pending Reviews</h6>
            <h3 id="pendingReviews">8</h3>
            <small class="text-warning">
                <i class="fas fa-exclamation"></i> 3 overdue
            </small>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="dashboard-card">
            <div class="dashboard-card-icon bg-info text-white">
                <i class="fas fa-chart-line"></i>
            </div>
            <h6>Compliance Score</h6>
            <h3 id="complianceScore">94%</h3>
            <small class="text-success">
                <i class="fas fa-arrow-up"></i> +2% this week
            </small>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Quick Actions</h5>
                <button class="btn btn-primary" onclick="newRiskAssessment()">
                    <i class="fas fa-plus me-2"></i>New Risk Assessment
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="quick-action-card text-center p-3" onclick="newRiskAssessment()">
                            <i class="fas fa-plus-circle fa-2x text-primary mb-2"></i>
                            <h6>New Assessment</h6>
                            <small class="text-muted">Start a new risk assessment</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="quick-action-card text-center p-3" onclick="viewReports()">
                            <i class="fas fa-chart-bar fa-2x text-success mb-2"></i>
                            <h6>View Reports</h6>
                            <small class="text-muted">Generate compliance reports</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="quick-action-card text-center p-3" onclick="loadTemplate()">
                            <i class="fas fa-download fa-2x text-info mb-2"></i>
                            <h6>Load Template</h6>
                            <small class="text-muted">Use pre-defined templates</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="quick-action-card text-center p-3" onclick="exportData()">
                            <i class="fas fa-file-export fa-2x text-warning mb-2"></i>
                            <h6>Export Data</h6>
                            <small class="text-muted">Export assessments</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Risk Assessments -->
<div class="row">
    <div class="col-md-12">
        <div class="table-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Risk Assessments</h5>
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" placeholder="Search assessments..." id="searchInput">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Risk Description</th>
                            <th>Area</th>
                            <th>Risk Score</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th>Due Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="assessmentsTableBody">
                        <tr>
                            <td><strong>RA-2024-001</strong></td>
                            <td>Bacterial contamination in raw material storage</td>
                            <td><span class="badge bg-primary">Food Safety</span></td>
                            <td>
                                <span class="badge bg-warning">12</span>
                                <small class="text-muted">High</small>
                            </td>
                            <td><span class="badge bg-success">Approved</span></td>
                            <td>John Doe</td>
                            <td>2024-07-30</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="viewAssessment('RA-2024-001')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-success" onclick="editAssessment('RA-2024-001')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deleteAssessment('RA-2024-001')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>RA-2024-002</strong></td>
                            <td>Unauthorized access to production area</td>
                            <td><span class="badge bg-warning">Security</span></td>
                            <td>
                                <span class="badge bg-info">6</span>
                                <small class="text-muted">Medium</small>
                            </td>
                            <td><span class="badge bg-warning">Pending</span></td>
                            <td>Jane Smith</td>
                            <td>2024-07-25</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="viewAssessment('RA-2024-002')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-success" onclick="editAssessment('RA-2024-002')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deleteAssessment('RA-2024-002')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- New Risk Assessment Modal -->
<div class="modal fade" id="newAssessmentModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Risk Assessment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Risk Assessment Form will be loaded here -->
                <div id="riskAssessmentForm"></div>
            </div>
        </div>
    </div>
</div>

<style>
.module-header {
    background: linear-gradient(135deg, var(--primary-color), #0056b3);
    color: white;
    padding: 2rem;
    border-radius: 16px;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.module-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.1);
    transform: rotate(45deg);
}

.module-header .module-icon {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin-bottom: 1rem;
}

.quick-action-card {
    background: var(--light-bg);
    border-radius: 12px;
    transition: all 0.3s ease;
    cursor: pointer;
    border: 1px solid var(--border-color);
}

.quick-action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    background: white;
}
</style>

<script>
// Module JavaScript functions
function newRiskAssessment() {
    // Load the full risk assessment form
    const modal = new bootstrap.Modal(document.getElementById('newAssessmentModal'));
    
    // You would load the actual form here via AJAX
    document.getElementById('riskAssessmentForm').innerHTML = `
        <div class="text-center p-5">
            <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
            <p>Loading risk assessment form...</p>
        </div>
    `;
    
    modal.show();
    
    // Simulate loading
    setTimeout(() => {
        window.location.href = '?module=brc_risk_assessment&action=new';
    }, 1000);
}

function viewReports() {
    window.location.href = '?module=brc_risk_assessment&action=reports';
}

function loadTemplate() {
    alert('Template loading functionality would be implemented here');
}

function exportData() {
    alert('Data export functionality would be implemented here');
}

function viewAssessment(id) {
    window.location.href = `?module=brc_risk_assessment&action=view&id=${id}`;
}

function editAssessment(id) {
    window.location.href = `?module=brc_risk_assessment&action=edit&id=${id}`;
}

function deleteAssessment(id) {
    if (confirm('Are you sure you want to delete this risk assessment?')) {
        // AJAX call to delete
        console.log('Deleting assessment:', id);
        alert('Assessment deleted successfully!');
        location.reload();
    }
}

// Search functionality
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const tableRows = document.querySelectorAll('#assessmentsTableBody tr');
    
    tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Auto-refresh data every 30 seconds
setInterval(() => {
    // Refresh dashboard stats
    console.log('Refreshing dashboard data...');
}, 30000);

console.log('BRC Risk Assessment module loaded successfully');
</script>

<?php include 'customer/layout/footer.php'; ?>