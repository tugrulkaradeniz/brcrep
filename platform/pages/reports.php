<?php
// platform/pages/reports.php
require_once __DIR__ . '/../../config/config.php';

// Check admin authentication
if (!isset($_SESSION['platform_admin_id'])) {
    header('Location: /platform/auth/login');
    exit;
}

$page_title = 'Reports';
include __DIR__ . '/../layout/header.php';
?>

<div class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">
                    <i class="fas fa-file-alt text-primary"></i>
                    Reports
                </h1>
                <p class="page-subtitle">Generate and manage platform reports</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createReportModal">
                    <i class="fas fa-plus"></i> Create New Report
                </button>
            </div>
        </div>
    </div>

    <!-- Report Types -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="report-card" onclick="generateQuickReport('companies')">
                <div class="report-icon bg-primary">
                    <i class="fas fa-building"></i>
                </div>
                <div class="report-content">
                    <h5>Company Report</h5>
                    <p>Detailed company analytics and subscription data</p>
                    <div class="report-meta">
                        <span class="badge bg-light text-dark">Last generated: 2 hours ago</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="report-card" onclick="generateQuickReport('modules')">
                <div class="report-icon bg-success">
                    <i class="fas fa-puzzle-piece"></i>
                </div>
                <div class="report-content">
                    <h5>Module Report</h5>
                    <p>Module usage statistics and performance metrics</p>
                    <div class="report-meta">
                        <span class="badge bg-light text-dark">Last generated: 1 day ago</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="report-card" onclick="generateQuickReport('revenue')">
                <div class="report-icon bg-warning">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="report-content">
                    <h5>Revenue Report</h5>
                    <p>Financial performance and billing analytics</p>
                    <div class="report-meta">
                        <span class="badge bg-light text-dark">Last generated: 6 hours ago</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="report-card" onclick="generateQuickReport('usage')">
                <div class="report-icon bg-info">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="report-content">
                    <h5>Usage Report</h5>
                    <p>Platform usage statistics and user behavior</p>
                    <div class="report-meta">
                        <span class="badge bg-light text-dark">Last generated: 3 hours ago</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Report Type</label>
                    <select class="form-select" id="reportType">
                        <option value="">All Reports</option>
                        <option value="company">Company Reports</option>
                        <option value="module">Module Reports</option>
                        <option value="revenue">Revenue Reports</option>
                        <option value="usage">Usage Reports</option>
                        <option value="custom">Custom Reports</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="reportStatus">
                        <option value="">All Status</option>
                        <option value="completed">Completed</option>
                        <option value="processing">Processing</option>
                        <option value="failed">Failed</option>
                        <option value="scheduled">Scheduled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date Range</label>
                    <div class="input-group">
                        <input type="date" class="form-control" id="startDate">
                        <span class="input-group-text">to</span>
                        <input type="date" class="form-control" id="endDate">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" id="searchReports" placeholder="Search reports...">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                        <i class="fas fa-times"></i> Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="card-title mb-0">Generated Reports</h5>
                </div>
                <div class="col-auto">
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" onclick="refreshReports()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="scheduleReport()">
                            <i class="fas fa-clock"></i> Schedule
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="reportsTable">
                    <thead class="table-dark">
                        <tr>
                            <th width="40">
                                <input type="checkbox" class="form-check-input" id="selectAllReports">
                            </th>
                            <th>Report Name</th>
                            <th>Type</th>
                            <th>Generated By</th>
                            <th>Date Range</th>
                            <th>Status</th>
                            <th>Size</th>
                            <th>Created</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input report-checkbox" value="1">
                            </td>
                            <td>
                                <div class="report-info">
                                    <strong>Monthly Company Analysis</strong>
                                    <small class="d-block text-muted">Comprehensive company metrics</small>
                                </div>
                            </td>
                            <td><span class="badge bg-primary">Company</span></td>
                            <td>Admin User</td>
                            <td>Jan 1 - Jan 31, 2025</td>
                            <td><span class="status-badge status-completed">Completed</span></td>
                            <td>2.4 MB</td>
                            <td>2 hours ago</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-outline-primary" onclick="downloadReport(1)" title="Download">
                                        <i class="fas fa-download"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" onclick="viewReport(1)" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteReport(1)" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input report-checkbox" value="2">
                            </td>
                            <td>
                                <div class="report-info">
                                    <strong>Module Usage Statistics</strong>
                                    <small class="d-block text-muted">Weekly module performance data</small>
                                </div>
                            </td>
                            <td><span class="badge bg-success">Module</span></td>
                            <td>System</td>
                            <td>Jan 22 - Jan 28, 2025</td>
                            <td><span class="status-badge status-processing">Processing</span></td>
                            <td>-</td>
                            <td>1 hour ago</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-outline-secondary" disabled title="Processing">
                                        <i class="fas fa-clock"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning" onclick="cancelReport(2)" title="Cancel">
                                        <i class="fas fa-stop"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input report-checkbox" value="3">
                            </td>
                            <td>
                                <div class="report-info">
                                    <strong>Revenue Analysis Q4</strong>
                                    <small class="d-block text-muted">Quarterly financial summary</small>
                                </div>
                            </td>
                            <td><span class="badge bg-warning text-dark">Revenue</span></td>
                            <td>Finance Admin</td>
                            <td>Oct 1 - Dec 31, 2024</td>
                            <td><span class="status-badge status-completed">Completed</span></td>
                            <td>5.8 MB</td>
                            <td>1 day ago</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-outline-primary" onclick="downloadReport(3)" title="Download">
                                        <i class="fas fa-download"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" onclick="viewReport(3)" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteReport(3)" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="row align-items-center">
                <div class="col">
                    <small class="text-muted">Showing 3 reports</small>
                </div>
                <div class="col-auto">
                    <div class="bulk-actions">
                        <select class="form-select form-select-sm" id="bulkAction">
                            <option value="">Bulk Actions</option>
                            <option value="download">Download Selected</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                        <button class="btn btn-sm btn-outline-primary" onclick="executeBulkAction()">Apply</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Report Modal -->
<div class="modal fade" id="createReportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createReportForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Report Name *</label>
                                <input type="text" class="form-control" name="report_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Report Type *</label>
                                <select class="form-select" name="report_type" required>
                                    <option value="">Select Type</option>
                                    <option value="company">Company Report</option>
                                    <option value="module">Module Report</option>
                                    <option value="revenue">Revenue Report</option>
                                    <option value="usage">Usage Report</option>
                                    <option value="custom">Custom Report</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Start Date *</label>
                                <input type="date" class="form-control" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">End Date *</label>
                                <input type="date" class="form-control" name="end_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Format</label>
                                <select class="form-select" name="format">
                                    <option value="pdf">PDF</option>
                                    <option value="excel">Excel</option>
                                    <option value="csv">CSV</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Delivery Method</label>
                                <select class="form-select" name="delivery">
                                    <option value="download">Download</option>
                                    <option value="email">Email</option>
                                    <option value="both">Both</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="schedule_recurring" id="scheduleRecurring">
                            <label class="form-check-label" for="scheduleRecurring">
                                Schedule as recurring report
                            </label>
                        </div>
                    </div>
                    <div id="recurringOptions" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Frequency</label>
                                    <select class="form-select" name="frequency">
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="quarterly">Quarterly</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Next Run</label>
                                    <input type="datetime-local" class="form-control" name="next_run">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.report-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #e3e6f0;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 1rem;
}

.report-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.3);
}

.report-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    margin-bottom: 1rem;
}

.report-content h5 {
    font-weight: 600;
    color: #5a5c69;
    margin-bottom: 0.5rem;
}

.report-content p {
    color: #858796;
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.report-meta {
    margin-top: auto;
}

.report-info strong {
    color: #5a5c69;
    font-weight: 600;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 500;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
}

.status-completed {
    background: #d4edda;
    color: #155724;
}

.status-processing {
    background: #fff3cd;
    color: #856404;
}

.status-failed {
    background: #f8d7da;
    color: #721c24;
}

.status-scheduled {
    background: #d1ecf1;
    color: #0c5460;
}

.action-buttons {
    display: flex;
    gap: 4px;
}

.bulk-actions {
    display: flex;
    gap: 8px;
    align-items: center;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: white;
}

.table td {
    vertical-align: middle;
    border-color: #e3e6f0;
}

.table tbody tr:hover {
    background-color: #f8f9fc;
}

.card {
    border: 1px solid #e3e6f0;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}
</style>

<script>
// Form handling
document.getElementById('scheduleRecurring').addEventListener('change', function() {
    const recurringOptions = document.getElementById('recurringOptions');
    recurringOptions.style.display = this.checked ? 'block' : 'none';
});

// Create report form
document.getElementById('createReportForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/platform/ajax/reports.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Report generation started!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('createReportModal')).hide();
            refreshReports();
        } else {
            showNotification(data.message || 'Error creating report', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error creating report', 'error');
    });
});

// Quick report generation
function generateQuickReport(type) {
    if (confirm(`Generate a ${type} report for the last 30 days?`)) {
        fetch('/platform/ajax/reports.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=quick_report&type=${type}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Report generation started!', 'success');
                refreshReports();
            } else {
                showNotification(data.message || 'Error generating report', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error generating report', 'error');
        });
    }
}

// Report actions
function downloadReport(id) {
    window.location.href = `/platform/ajax/reports.php?action=download&id=${id}`;
}

function viewReport(id) {
    window.open(`/platform/ajax/reports.php?action=view&id=${id}`, '_blank');
}

function deleteReport(id) {
    if (confirm('Are you sure you want to delete this report?')) {
        fetch('/platform/ajax/reports.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete&id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Report deleted successfully!', 'success');
                document.querySelector(`input[value="${id}"]`).closest('tr').remove();
            } else {
                showNotification(data.message || 'Error deleting report', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error deleting report', 'error');
        });
    }
}

function cancelReport(id) {
    if (confirm('Cancel this report generation?')) {
        fetch('/platform/ajax/reports.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=cancel&id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Report generation cancelled!', 'success');
                refreshReports();
            } else {
                showNotification(data.message || 'Error cancelling report', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error cancelling report', 'error');
        });
    }
}

function refreshReports() {
    location.reload();
}

function scheduleReport() {
    // Open create report modal with scheduling enabled
    document.getElementById('scheduleRecurring').checked = true;
    document.getElementById('recurringOptions').style.display = 'block';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('createReportModal')).show();
}

function clearFilters() {
    document.getElementById('reportType').value = '';
    document.getElementById('reportStatus').value = '';
    document.getElementById('startDate').value = '';
    document.getElementById('endDate').value = '';
    document.getElementById('searchReports').value = '';
}

// Bulk actions
function executeBulkAction() {
    const action = document.getElementById('bulkAction').value;
    const selectedIds = Array.from(document.querySelectorAll('.report-checkbox:checked')).map(cb => cb.value);
    
    if (!action || selectedIds.length === 0) {
        showNotification('Please select an action and reports', 'warning');
        return;
    }
    
    if (confirm(`${action} ${selectedIds.length} reports?`)) {
        fetch('/platform/ajax/reports.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=bulk_${action}&ids=${selectedIds.join(',')}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(`Bulk ${action} completed successfully!`, 'success');
                if (action === 'download') {
                    // Handle bulk download
                    data.downloadUrl && (window.location.href = data.downloadUrl);
                } else {
                    refreshReports();
                }
            } else {
                showNotification(data.message || `Error performing bulk ${action}`, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification(`Error performing bulk ${action}`, 'error');
        });
    }
}

// Select all functionality
document.getElementById('selectAllReports').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.report-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Auto-refresh processing reports every 30 seconds
setInterval(() => {
    const processingReports = document.querySelectorAll('.status-processing');
    if (processingReports.length > 0) {
        // Only refresh if there are processing reports
        fetch('/platform/ajax/reports.php?action=check_status')
            .then(response => response.json())
            .then(data => {
                if (data.updated) {
                    refreshReports();
                }
            })
            .catch(console.error);
    }
}, 30000);
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>