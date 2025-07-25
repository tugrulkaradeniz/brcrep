<?php
// platform/pages/revenue.php
require_once __DIR__ . '/../../config/config.php';

// Check admin authentication
if (!isset($_SESSION['platform_admin_id'])) {
    header('Location: /platform/auth/login');
    exit;
}

$page_title = 'Revenue Management';
include __DIR__ . '/../layout/header.php';
?>

<div class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">
                    <i class="fas fa-dollar-sign text-primary"></i>
                    Revenue Management
                </h1>
                <p class="page-subtitle">Financial analytics and subscription management</p>
            </div>
            <div class="col-auto">
                <div class="btn-group">
                    <button class="btn btn-outline-primary" onclick="refreshData()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button class="btn btn-primary" onclick="exportFinancialReport()">
                        <i class="fas fa-download"></i> Export Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Overview -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="revenue-card">
                <div class="revenue-icon bg-success">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div class="revenue-content">
                    <div class="revenue-amount">$142,583</div>
                    <div class="revenue-label">Monthly Revenue</div>
                    <div class="revenue-change positive">
                        <i class="fas fa-arrow-up"></i> +18.5% from last month
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="revenue-card">
                <div class="revenue-icon bg-primary">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="revenue-content">
                    <div class="revenue-amount">$1,710,996</div>
                    <div class="revenue-label">Annual Recurring Revenue</div>
                    <div class="revenue-change positive">
                        <i class="fas fa-arrow-up"></i> +22.3% YoY
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="revenue-card">
                <div class="revenue-icon bg-warning">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="revenue-content">
                    <div class="revenue-amount">$24,750</div>
                    <div class="revenue-label">Pending Payments</div>
                    <div class="revenue-change neutral">
                        <i class="fas fa-clock"></i> 18 invoices pending
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="revenue-card">
                <div class="revenue-icon bg-info">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="revenue-content">
                    <div class="revenue-amount">2.3%</div>
                    <div class="revenue-label">Churn Rate</div>
                    <div class="revenue-change negative">
                        <i class="fas fa-arrow-down"></i> -0.5% improvement
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Charts -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">Revenue Trends</h5>
                        </div>
                        <div class="col-auto">
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="revenueView" id="monthly" checked>
                                <label class="btn btn-outline-primary" for="monthly">Monthly</label>
                                
                                <input type="radio" class="btn-check" name="revenueView" id="quarterly">
                                <label class="btn btn-outline-primary" for="quarterly">Quarterly</label>
                                
                                <input type="radio" class="btn-check" name="revenueView" id="yearly">
                                <label class="btn btn-outline-primary" for="yearly">Yearly</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="350"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Revenue by Subscription</h5>
                </div>
                <div class="card-body">
                    <canvas id="subscriptionChart" height="350"></canvas>
                    <div class="subscription-legend mt-3">
                        <div class="legend-item">
                            <span class="legend-color bg-primary"></span>
                            <span>Premium ($89,450)</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color bg-success"></span>
                            <span>Basic ($42,890)</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color bg-warning"></span>
                            <span>Trial ($10,243)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Analytics -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Top Revenue Companies</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Company</th>
                                    <th>Plan</th>
                                    <th>Monthly Revenue</th>
                                    <th>Total Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="company-info">
                                            <strong>TechCorp Solutions</strong>
                                            <small class="d-block text-muted">Technology</small>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-primary">Premium</span></td>
                                    <td>$2,450</td>
                                    <td>$29,400</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="company-info">
                                            <strong>Manufacturing Inc</strong>
                                            <small class="d-block text-muted">Manufacturing</small>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-warning text-dark">Enterprise</span></td>
                                    <td>$4,200</td>
                                    <td>$50,400</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="company-info">
                                            <strong>Retail Masters</strong>
                                            <small class="d-block text-muted">Retail</small>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Basic</span></td>
                                    <td>$890</td>
                                    <td>$10,680</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Revenue Metrics</h5>
                </div>
                <div class="card-body">
                    <div class="metric-item">
                        <div class="metric-label">Average Revenue Per User (ARPU)</div>
                        <div class="metric-value">$245.67</div>
                        <div class="metric-change positive">+$12.50 from last month</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-label">Customer Lifetime Value (CLV)</div>
                        <div class="metric-value">$2,847.34</div>
                        <div class="metric-change positive">+8.3% improvement</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-label">Monthly Recurring Revenue (MRR)</div>
                        <div class="metric-value">$142,583</div>
                        <div class="metric-change positive">+$22,150 from last month</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-label">Revenue Growth Rate</div>
                        <div class="metric-value">18.5%</div>
                        <div class="metric-change positive">Month-over-month growth</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Management -->
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="card-title mb-0">Recent Transactions</h5>
                </div>
                <div class="col-auto">
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" onclick="exportTransactions()">
                            <i class="fas fa-download"></i> Export
                        </button>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                            <i class="fas fa-plus"></i> Add Transaction
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Transaction ID</th>
                            <th>Company</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment Method</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>#TXN-2025-001234</code></td>
                            <td>TechCorp Solutions</td>
                            <td><span class="badge bg-success">Subscription</span></td>
                            <td>$2,450.00</td>
                            <td><span class="payment-status status-completed">Completed</span></td>
                            <td>Credit Card ****1234</td>
                            <td>Jan 28, 2025</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="viewTransaction('TXN-2025-001234')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><code>#TXN-2025-001233</code></td>
                            <td>Manufacturing Inc</td>
                            <td><span class="badge bg-warning text-dark">Setup Fee</span></td>
                            <td>$500.00</td>
                            <td><span class="payment-status status-pending">Pending</span></td>
                            <td>Bank Transfer</td>
                            <td>Jan 27, 2025</td>
                            <td>
                                <button class="btn btn-sm btn-outline-warning" onclick="retryPayment('TXN-2025-001233')">
                                    <i class="fas fa-redo"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><code>#TXN-2025-001232</code></td>
                            <td>Retail Masters</td>
                            <td><span class="badge bg-info">Module Purchase</span></td>
                            <td>$299.00</td>
                            <td><span class="payment-status status-failed">Failed</span></td>
                            <td>Credit Card ****5678</td>
                            <td>Jan 26, 2025</td>
                            <td>
                                <button class="btn btn-sm btn-outline-danger" onclick="handleFailedPayment('TXN-2025-001232')">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Transaction Modal -->
<div class="modal fade" id="addTransactionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Manual Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addTransactionForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Company *</label>
                        <select class="form-select" name="company_id" required>
                            <option value="">Select Company</option>
                            <option value="1">TechCorp Solutions</option>
                            <option value="2">Manufacturing Inc</option>
                            <option value="3">Retail Masters</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Transaction Type *</label>
                        <select class="form-select" name="type" required>
                            <option value="">Select Type</option>
                            <option value="subscription">Subscription Payment</option>
                            <option value="setup">Setup Fee</option>
                            <option value="module">Module Purchase</option>
                            <option value="refund">Refund</option>
                            <option value="credit">Credit</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" name="amount" step="0.01" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select" name="payment_method">
                            <option value="credit_card">Credit Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="paypal">PayPal</option>
                            <option value="manual">Manual</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Transaction</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.revenue-card {
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

.revenue-card:hover {
    transform: translateY(-2px);
}

.revenue-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.revenue-content {
    flex: 1;
}

.revenue-amount {
    font-size: 2rem;
    font-weight: bold;
    color: #5a5c69;
    line-height: 1;
}

.revenue-label {
    color: #858796;
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.revenue-change {
    font-size: 0.875rem;
    font-weight: 500;
}

.revenue-change.positive { color: #28a745; }
.revenue-change.negative { color: #dc3545; }
.revenue-change.neutral { color: #6c757d; }

.subscription-legend {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 2px;
}

.metric-item {
    padding: 1rem 0;
    border-bottom: 1px solid #e3e6f0;
}

.metric-item:last-child {
    border-bottom: none;
}

.metric-label {
    color: #858796;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.metric-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: #5a5c69;
    margin-bottom: 0.25rem;
}

.metric-change {
    font-size: 0.75rem;
    color: #28a745;
}

.payment-status {
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

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-failed {
    background: #f8d7da;
    color: #721c24;
}

.company-info strong {
    color: #5a5c69;
    font-weight: 600;
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

code {
    background: #f8f9fc;
    color: #6c757d;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.875rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initRevenueCharts();
});

function initRevenueCharts() {
    // Revenue Trends Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    window.revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Revenue',
                data: [98000, 105000, 112000, 125000, 118000, 135000, 142000, 138000, 155000, 148000, 162000, 175000],
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5
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
                    beginAtZero: false,
                    ticks: {
                        callback: function(value) {
                            return '$' + (value / 1000) + 'K';
                        }
                    },
                    grid: {
                        color: '#e3e6f0'
                    }
                },
                x: {
                    grid: {
                        color: '#e3e6f0'
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // Subscription Revenue Chart
    const subscriptionCtx = document.getElementById('subscriptionChart').getContext('2d');
    window.subscriptionChart = new Chart(subscriptionCtx, {
        type: 'doughnut',
        data: {
            labels: ['Premium', 'Basic', 'Trial'],
            datasets: [{
                data: [89450, 42890, 10243],
                backgroundColor: ['#007bff', '#28a745', '#ffc107'],
                borderWidth: 2,
                borderColor: '#fff'
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
            cutout: '70%'
        }
    });
}

// Revenue view toggle
document.querySelectorAll('input[name="revenueView"]').forEach(radio => {
    radio.addEventListener('change', function() {
        updateRevenueChart(this.value);
    });
});

function updateRevenueChart(view) {
    // Update chart data based on view
    let newData, newLabels;
    
    switch(view) {
        case 'quarterly':
            newLabels = ['Q1 2024', 'Q2 2024', 'Q3 2024', 'Q4 2024'];
            newData = [315000, 375000, 445000, 485000];
            break;
        case 'yearly':
            newLabels = ['2021', '2022', '2023', '2024'];
            newData = [850000, 1200000, 1450000, 1620000];
            break;
        default: // monthly
            newLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            newData = [98000, 105000, 112000, 125000, 118000, 135000, 142000, 138000, 155000, 148000, 162000, 175000];
    }
    
    window.revenueChart.data.labels = newLabels;
    window.revenueChart.data.datasets[0].data = newData;
    window.revenueChart.update();
}

// Transaction management
function viewTransaction(id) {
    // Open transaction details modal or page
    window.open(`/platform/transactions/${id}`, '_blank');
}

function retryPayment(id) {
    if (confirm('Retry this payment?')) {
        fetch('/platform/ajax/revenue.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=retry_payment&transaction_id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Payment retry initiated!', 'success');
                refreshData();
            } else {
                showNotification(data.message || 'Error retrying payment', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error retrying payment', 'error');
        });
    }
}

function handleFailedPayment(id) {
    // Open failed payment handling modal
    alert('Failed payment handling - Implementation needed');
}

// Add transaction form
document.getElementById('addTransactionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'add_transaction');
    
    fetch('/platform/ajax/revenue.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Transaction added successfully!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('addTransactionModal')).hide();
            refreshData();
        } else {
            showNotification(data.message || 'Error adding transaction', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error adding transaction', 'error');
    });
});

function refreshData() {
    location.reload();
}

function exportFinancialReport() {
    const view = document.querySelector('input[name="revenueView"]:checked').value;
    window.location.href = `/platform/ajax/revenue.php?action=export_financial&view=${view}`;
}

function exportTransactions() {
    window.location.href = '/platform/ajax/revenue.php?action=export_transactions';
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
</script>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php include __DIR__ . '/../layout/footer.php'; ?>