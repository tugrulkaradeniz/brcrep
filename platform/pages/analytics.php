<?php
// platform/pages/analytics.php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Company.php';
require_once __DIR__ . '/../../models/Module.php';

// Check admin authentication
if (!isset($_SESSION['platform_admin_id'])) {
    header('Location: /platform/auth/login');
    exit;
}

$company = new Company();
$module = new Module();

// Get analytics data
$totalCompanies = $company->getTotalCount();
$activeCompanies = $company->getActiveCount();
$totalModules = $module->getTotalCount();
$totalRevenue = $company->getTotalRevenue();
$monthlyGrowth = $company->getMonthlyGrowth();

$page_title = 'Analytics Dashboard';
include __DIR__ . '/../layout/header.php';
?>

<div class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">
                    <i class="fas fa-chart-line text-primary"></i>
                    Analytics Dashboard
                </h1>
                <p class="page-subtitle">Comprehensive platform analytics and insights</p>
            </div>
            <div class="col-auto">
                <div class="btn-group">
                    <button class="btn btn-outline-primary" onclick="refreshData()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button class="btn btn-primary" onclick="exportReport()">
                        <i class="fas fa-download"></i> Export Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Time Range Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h6 class="card-title mb-0">Time Range</h6>
                </div>
                <div class="col-md-8">
                    <div class="btn-group" role="group" id="timeRangeFilter">
                        <input type="radio" class="btn-check" name="timeRange" id="range7d" value="7d" checked>
                        <label class="btn btn-outline-primary" for="range7d">Last 7 Days</label>
                        
                        <input type="radio" class="btn-check" name="timeRange" id="range30d" value="30d">
                        <label class="btn btn-outline-primary" for="range30d">Last 30 Days</label>
                        
                        <input type="radio" class="btn-check" name="timeRange" id="range90d" value="90d">
                        <label class="btn btn-outline-primary" for="range90d">Last 90 Days</label>
                        
                        <input type="radio" class="btn-check" name="timeRange" id="range1y" value="1y">
                        <label class="btn btn-outline-primary" for="range1y">Last Year</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card">
                <div class="kpi-icon bg-primary">
                    <i class="fas fa-building"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value"><?= number_format($totalCompanies) ?></div>
                    <div class="kpi-label">Total Companies</div>
                    <div class="kpi-change positive">
                        <i class="fas fa-arrow-up"></i> +12% from last month
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card">
                <div class="kpi-icon bg-success">
                    <i class="fas fa-users"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value"><?= number_format($activeCompanies) ?></div>
                    <div class="kpi-label">Active Companies</div>
                    <div class="kpi-change positive">
                        <i class="fas fa-arrow-up"></i> +8% from last month
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card">
                <div class="kpi-icon bg-info">
                    <i class="fas fa-puzzle-piece"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value"><?= number_format($totalModules) ?></div>
                    <div class="kpi-label">Published Modules</div>
                    <div class="kpi-change positive">
                        <i class="fas fa-arrow-up"></i> +3 new this month
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card">
                <div class="kpi-icon bg-warning">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value">$<?= number_format($totalRevenue) ?></div>
                    <div class="kpi-label">Monthly Revenue</div>
                    <div class="kpi-change positive">
                        <i class="fas fa-arrow-up"></i> +15% from last month
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Revenue Chart -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">Revenue Trends</h5>
                        </div>
                        <div class="col-auto">
                            <select class="form-select form-select-sm" id="revenueMetric">
                                <option value="monthly">Monthly Revenue</option>
                                <option value="arr">Annual Recurring Revenue</option>
                                <option value="mrr">Monthly Recurring Revenue</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Company Growth Chart -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Company Growth</h5>
                </div>
                <div class="card-body">
                    <canvas id="growthChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Tables Row -->
    <div class="row mb-4">
        <!-- Top Performing Modules -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Top Performing Modules</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Module</th>
                                    <th>Downloads</th>
                                    <th>Revenue</th>
                                    <th>Growth</th>
                                </tr>
                            </thead>
                            <tbody id="topModulesTable">
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-shield-alt text-primary me-2"></i>
                                            BRC Risk Assessment
                                        </div>
                                    </td>
                                    <td>1,234</td>
                                    <td>$12,450</td>
                                    <td><span class="badge bg-success">+15%</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-chart-bar text-info me-2"></i>
                                            Analytics Dashboard
                                        </div>
                                    </td>
                                    <td>987</td>
                                    <td>$8,970</td>
                                    <td><span class="badge bg-success">+8%</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-tasks text-warning me-2"></i>
                                            Workflow Manager
                                        </div>
                                    </td>
                                    <td>756</td>
                                    <td>$6,804</td>
                                    <td><span class="badge bg-danger">-2%</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Activities</h5>
                </div>
                <div class="card-body">
                    <div class="activity-feed" id="activityFeed">
                        <div class="activity-item">
                            <div class="activity-icon bg-success">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">New company registered</div>
                                <div class="activity-desc">TechCorp signed up for Premium plan</div>
                                <div class="activity-time">2 hours ago</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon bg-info">
                                <i class="fas fa-download"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Module downloaded</div>
                                <div class="activity-desc">BRC Risk Assessment - 15 downloads</div>
                                <div class="activity-time">4 hours ago</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon bg-warning">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Payment received</div>
                                <div class="activity-desc">$2,500 from Manufacturing Co.</div>
                                <div class="activity-time">6 hours ago</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Geographic Distribution -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Geographic Distribution</h5>
                </div>
                <div class="card-body">
                    <div id="worldMap" style="height: 400px;"></div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Top Countries</h5>
                </div>
                <div class="card-body">
                    <div class="country-stats">
                        <div class="country-item">
                            <div class="country-flag">🇺🇸</div>
                            <div class="country-info">
                                <div class="country-name">United States</div>
                                <div class="country-count">156 companies</div>
                            </div>
                            <div class="country-percentage">42%</div>
                        </div>
                        <div class="country-item">
                            <div class="country-flag">🇬🇧</div>
                            <div class="country-info">
                                <div class="country-name">United Kingdom</div>
                                <div class="country-count">89 companies</div>
                            </div>
                            <div class="country-percentage">24%</div>
                        </div>
                        <div class="country-item">
                            <div class="country-flag">🇩🇪</div>
                            <div class="country-info">
                                <div class="country-name">Germany</div>
                                <div class="country-count">67 companies</div>
                            </div>
                            <div class="country-percentage">18%</div>
                        </div>
                        <div class="country-item">
                            <div class="country-flag">🇹🇷</div>
                            <div class="country-info">
                                <div class="country-name">Turkey</div>
                                <div class="country-count">23 companies</div>
                            </div>
                            <div class="country-percentage">6%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.kpi-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #e3e6f0;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    transition: transform 0.2s ease;
}

.kpi-card:hover {
    transform: translateY(-2px);
}

.kpi-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.kpi-content {
    flex: 1;
}

.kpi-value {
    font-size: 2rem;
    font-weight: bold;
    color: #5a5c69;
    line-height: 1;
}

.kpi-label {
    color: #858796;
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.kpi-change {
    font-size: 0.875rem;
    font-weight: 500;
}

.kpi-change.positive {
    color: #28a745;
}

.kpi-change.negative {
    color: #dc3545;
}

.activity-feed {
    max-height: 350px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #e3e6f0;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.875rem;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 600;
    color: #5a5c69;
    margin-bottom: 0.25rem;
}

.activity-desc {
    color: #858796;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.activity-time {
    color: #858796;
    font-size: 0.75rem;
}

.country-stats {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.country-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    background: #f8f9fc;
    border-radius: 8px;
}

.country-flag {
    font-size: 1.5rem;
}

.country-info {
    flex: 1;
}

.country-name {
    font-weight: 600;
    color: #5a5c69;
}

.country-count {
    color: #858796;
    font-size: 0.875rem;
}

.country-percentage {
    font-weight: bold;
    color: #5a5c69;
}

.card {
    border: 1px solid #e3e6f0;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.card-header {
    background: white;
    border-bottom: 1px solid #e3e6f0;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
}

.table td {
    vertical-align: middle;
    border-color: #e3e6f0;
}
</style>

<script>
// Chart.js setup
document.addEventListener('DOMContentLoaded', function() {
    initCharts();
    loadRealTimeData();
});

function initCharts() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    window.revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue',
                data: [12000, 15000, 18000, 22000, 25000, 28000],
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Growth Chart (Doughnut)
    const growthCtx = document.getElementById('growthChart').getContext('2d');
    window.growthChart = new Chart(growthCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Trial', 'Inactive'],
            datasets: [{
                data: [65, 25, 10],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Time range filter
document.querySelectorAll('input[name="timeRange"]').forEach(radio => {
    radio.addEventListener('change', function() {
        updateChartsForTimeRange(this.value);
    });
});

function updateChartsForTimeRange(range) {
    // Update charts based on selected time range
    console.log('Updating charts for range:', range);
    // Implement AJAX call to get data for specific time range
}

function refreshData() {
    // Refresh all data
    loadRealTimeData();
    updateChartsForTimeRange(document.querySelector('input[name="timeRange"]:checked').value);
    showNotification('Data refreshed successfully!', 'success');
}

function exportReport() {
    // Export analytics report
    const timeRange = document.querySelector('input[name="timeRange"]:checked').value;
    window.location.href = `/platform/ajax/analytics-export.php?range=${timeRange}`;
}

function loadRealTimeData() {
    // Load real-time data via AJAX
    fetch('/platform/ajax/analytics-data.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateKPIs(data.kpis);
                updateActivityFeed(data.activities);
                updateTopModules(data.topModules);
            }
        })
        .catch(console.error);
}

function updateKPIs(kpis) {
    // Update KPI values
    // This would update the actual KPI values from the API
}

function updateActivityFeed(activities) {
    // Update activity feed
    // This would populate the activity feed with real data
}

function updateTopModules(modules) {
    // Update top modules table
    // This would populate the table with real module data
}

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

// Auto-refresh data every 5 minutes
setInterval(loadRealTimeData, 300000);
</script>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php include __DIR__ . '/../layout/footer.php'; ?>