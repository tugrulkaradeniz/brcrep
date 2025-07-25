<?php
// platform/pages/logs.php
require_once __DIR__ . '/../../config/config.php';

// Check admin authentication
if (!isset($_SESSION['platform_admin_id'])) {
    header('Location: /platform/auth/login');
    exit;
}

$page_title = 'System Logs';
include __DIR__ . '/../layout/header.php';
?>

<div class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">
                    <i class="fas fa-list-alt text-primary"></i>
                    System Logs
                </h1>
                <p class="page-subtitle">Monitor system activities and troubleshoot issues</p>
            </div>
            <div class="col-auto">
                <div class="btn-group">
                    <button class="btn btn-outline-primary" onclick="refreshLogs()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button class="btn btn-outline-warning" onclick="clearLogs()">
                        <i class="fas fa-trash"></i> Clear Logs
                    </button>
                    <button class="btn btn-primary" onclick="downloadLogs()">
                        <i class="fas fa-download"></i> Download
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="log-stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">2,456</div>
                    <div class="stat-label">Info Logs</div>
                    <div class="stat-change">Last 24 hours</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="log-stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">89</div>
                    <div class="stat-label">Warning Logs</div>
                    <div class="stat-change">Last 24 hours</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="log-stat-card">
                <div class="stat-icon bg-danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">12</div>
                    <div class="stat-label">Error Logs</div>
                    <div class="stat-change">Last 24 hours</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="log-stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">98.5%</div>
                    <div class="stat-label">Success Rate</div>
                    <div class="stat-change">Last 24 hours</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Log Level</label>
                    <select class="form-select" id="logLevel">
                        <option value="">All Levels</option>
                        <option value="debug">Debug</option>
                        <option value="info">Info</option>
                        <option value="warning">Warning</option>
                        <option value="error">Error</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Log Type</label>
                    <select class="form-select" id="logType">
                        <option value="">All Types</option>
                        <option value="system">System</option>
                        <option value="auth">Authentication</option>
                        <option value="api">API</option>
                        <option value="payment">Payment</option>
                        <option value="module">Module</option>
                        <option value="security">Security</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Time Range</label>
                    <select class="form-select" id="timeRange">
                        <option value="1h">Last Hour</option>
                        <option value="24h" selected>Last 24 Hours</option>
                        <option value="7d">Last 7 Days</option>
                        <option value="30d">Last 30 Days</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <div class="search-box">
                        <input type="text" class="form-control" id="searchLogs" placeholder="Search in logs...">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                        <i class="fas fa-times"></i> Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Tabs -->
    <div class="row">
        <div class="col-lg-3">
            <div class="log-nav">
                <div class="nav flex-column nav-pills" id="logTab" role="tablist">
                    <button class="nav-link active" id="realtime-tab" data-bs-toggle="pill" data-bs-target="#realtime" type="button" role="tab">
                        <i class="fas fa-broadcast-tower"></i> Real-time Logs
                    </button>
                    <button class="nav-link" id="application-tab" data-bs-toggle="pill" data-bs-target="#application" type="button" role="tab">
                        <i class="fas fa-desktop"></i> Application Logs
                    </button>
                    <button class="nav-link" id="security-tab" data-bs-toggle="pill" data-bs-target="#security" type="button" role="tab">
                        <i class="fas fa-shield-alt"></i> Security Logs
                    </button>
                    <button class="nav-link" id="audit-tab" data-bs-toggle="pill" data-bs-target="#audit" type="button" role="tab">
                        <i class="fas fa-search"></i> Audit Trail
                    </button>
                    <button class="nav-link" id="error-tab" data-bs-toggle="pill" data-bs-target="#error" type="button" role="tab">
                        <i class="fas fa-bug"></i> Error Logs
                    </button>
                    <button class="nav-link" id="performance-tab" data-bs-toggle="pill" data-bs-target="#performance" type="button" role="tab">
                        <i class="fas fa-tachometer-alt"></i> Performance
                    </button>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <div class="tab-content" id="logTabContent">
                <!-- Real-time Logs Tab -->
                <div class="tab-pane fade show active" id="realtime" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="card-title mb-0">Real-time System Logs</h5>
                                </div>
                                <div class="col-auto">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="autoRefresh" checked>
                                        <label class="form-check-label" for="autoRefresh">
                                            Auto-refresh
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="log-viewer" id="realtimeLogViewer">
                                <div class="log-entry log-info">
                                    <div class="log-timestamp">2025-01-28 15:42:33</div>
                                    <div class="log-level level-info">INFO</div>
                                    <div class="log-type">SYSTEM</div>
                                    <div class="log-message">User login successful for admin@brcload.com</div>
                                    <div class="log-actions">
                                        <button class="btn btn-sm btn-link" onclick="viewLogDetails('log_001')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="log-entry log-warning">
                                    <div class="log-timestamp">2025-01-28 15:41:45</div>
                                    <div class="log-level level-warning">WARN</div>
                                    <div class="log-type">API</div>
                                    <div class="log-message">API rate limit approaching for key: brc_live_sk_...XY7Z (80/100 requests)</div>
                                    <div class="log-actions">
                                        <button class="btn btn-sm btn-link" onclick="viewLogDetails('log_002')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="log-entry log-error">
                                    <div class="log-timestamp">2025-01-28 15:40:12</div>
                                    <div class="log-level level-error">ERROR</div>
                                    <div class="log-type">PAYMENT</div>
                                    <div class="log-message">Payment processing failed for company TechCorp: Invalid card number</div>
                                    <div class="log-actions">
                                        <button class="btn btn-sm btn-link" onclick="viewLogDetails('log_003')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="log-entry log-info">
                                    <div class="log-timestamp">2025-01-28 15:39:28</div>
                                    <div class="log-level level-info">INFO</div>
                                    <div class="log-type">MODULE</div>
                                    <div class="log-message">Module "BRC Risk Assessment" installed for company Manufacturing Inc</div>
                                    <div class="log-actions">
                                        <button class="btn btn-sm btn-link" onclick="viewLogDetails('log_004')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="log-entry log-success">
                                    <div class="log-timestamp">2025-01-28 15:38:55</div>
                                    <div class="log-level level-success">SUCCESS</div>
                                    <div class="log-type">SYSTEM</div>
                                    <div class="log-message">Database backup completed successfully (backup_20250128_153855.sql)</div>
                                    <div class="log-actions">
                                        <button class="btn btn-sm btn-link" onclick="viewLogDetails('log_005')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row align-items-center">
                                <div class="col">
                                    <small class="text-muted">Showing last 100 entries</small>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-sm btn-outline-primary" onclick="pauseRealtime()">
                                        <i class="fas fa-pause"></i> Pause
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="clearRealtimeView()">
                                        <i class="fas fa-eraser"></i> Clear View
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Application Logs Tab -->
                <div class="tab-pane fade" id="application" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Application Logs</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Timestamp</th>
                                            <th>Level</th>
                                            <th>Component</th>
                                            <th>Message</th>
                                            <th>User</th>
                                            <th>IP</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>2025-01-28 15:42:33</td>
                                            <td><span class="log-level level-info">INFO</span></td>
                                            <td>Auth</td>
                                            <td>User authentication successful</td>
                                            <td>admin@brcload.com</td>
                                            <td>192.168.1.100</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="viewLogDetails('app_001')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>2025-01-28 15:41:15</td>
                                            <td><span class="log-level level-warning">WARN</span></td>
                                            <td>Database</td>
                                            <td>Slow query detected (2.5s): SELECT * FROM companies...</td>
                                            <td>system</td>
                                            <td>-</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="viewLogDetails('app_002')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>2025-01-28 15:40:42</td>
                                            <td><span class="log-level level-error">ERROR</span></td>
                                            <td>Payment</td>
                                            <td>Stripe webhook validation failed</td>
                                            <td>system</td>
                                            <td>54.187.174.169</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="viewLogDetails('app_003')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Logs Tab -->
                <div class="tab-pane fade" id="security" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Security Events</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Timestamp</th>
                                            <th>Event Type</th>
                                            <th>Severity</th>
                                            <th>Source IP</th>
                                            <th>User Agent</th>
                                            <th>Details</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>2025-01-28 15:35:22</td>
                                            <td>Failed Login</td>
                                            <td><span class="severity-medium">Medium</span></td>
                                            <td>203.0.113.42</td>
                                            <td>Mozilla/5.0...</td>
                                            <td>5 consecutive failed attempts for admin@brcload.com</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-danger" onclick="blockIP('203.0.113.42')">
                                                    <i class="fas fa-ban"></i> Block
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>2025-01-28 15:20:15</td>
                                            <td>SQL Injection</td>
                                            <td><span class="severity-high">High</span></td>
                                            <td>198.51.100.25</td>
                                            <td>curl/7.68.0</td>
                                            <td>Malicious SQL injection attempt detected</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-danger" onclick="blockIP('198.51.100.25')">
                                                    <i class="fas fa-ban"></i> Block
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>2025-01-28 14:58:33</td>
                                            <td>API Key Compromise</td>
                                            <td><span class="severity-critical">Critical</span></td>
                                            <td>192.0.2.100</td>
                                            <td>Python-requests/2.28.0</td>
                                            <td>API key used from suspicious location</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-warning" onclick="revokeApiKey('suspicious_key')">
                                                    <i class="fas fa-key"></i> Revoke
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Audit Trail Tab -->
                <div class="tab-pane fade" id="audit" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Audit Trail</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="audit-timeline">
                                <div class="audit-entry">
                                    <div class="audit-icon bg-success">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div class="audit-content">
                                        <div class="audit-header">
                                            <strong>Company Created</strong>
                                            <span class="audit-time">2 hours ago</span>
                                        </div>
                                        <div class="audit-details">
                                            <p>New company "TechCorp Solutions" was created by admin@brcload.com</p>
                                            <small class="text-muted">IP: 192.168.1.100 | User Agent: Mozilla/5.0...</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="audit-entry">
                                    <div class="audit-icon bg-info">
                                        <i class="fas fa-edit"></i>
                                    </div>
                                    <div class="audit-content">
                                        <div class="audit-header">
                                            <strong>Module Updated</strong>
                                            <span class="audit-time">3 hours ago</span>
                                        </div>
                                        <div class="audit-details">
                                            <p>BRC Risk Assessment module updated to version 2.1</p>
                                            <small class="text-muted">Updated by: platform_admin</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="audit-entry">
                                    <div class="audit-icon bg-warning">
                                        <i class="fas fa-key"></i>
                                    </div>
                                    <div class="audit-content">
                                        <div class="audit-header">
                                            <strong>API Key Generated</strong>
                                            <span class="audit-time">5 hours ago</span>
                                        </div>
                                        <div class="audit-details">
                                            <p>New API key "Mobile App" created with read-only permissions</p>
                                            <small class="text-muted">Generated by: admin@brcload.com</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="audit-entry">
                                    <div class="audit-icon bg-danger">
                                        <i class="fas fa-trash"></i>
                                    </div>
                                    <div class="audit-content">
                                        <div class="audit-header">
                                            <strong>Company Deleted</strong>
                                            <span class="audit-time">1 day ago</span>
                                        </div>
                                        <div class="audit-details">
                                            <p>Company "Test Corp" was permanently deleted</p>
                                            <small class="text-muted">Deleted by: admin@brcload.com | Reason: Requested by customer</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error Logs Tab -->
                <div class="tab-pane fade" id="error" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="card-title mb-0">Error Logs</h5>
                                </div>
                                <div class="col-auto">
                                    <select class="form-select form-select-sm" id="errorSeverity">
                                        <option value="">All Errors</option>
                                        <option value="warning">Warnings</option>
                                        <option value="error">Errors</option>
                                        <option value="critical">Critical</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="error-list">
                                <div class="error-item">
                                    <div class="error-header">
                                        <div class="error-level level-critical">CRITICAL</div>
                                        <div class="error-time">2025-01-28 14:32:15</div>
                                    </div>
                                    <div class="error-title">Database Connection Failed</div>
                                    <div class="error-message">
                                        <pre>PDOException: SQLSTATE[HY000] [1045] Access denied for user 'brc_user'@'localhost' (using password: YES)
Stack trace:
#0 /var/www/html/config/database.php(15): PDO->__construct()
#1 /var/www/html/models/Company.php(23): Database::connect()
#2 /var/www/html/platform/pages/companies.php(12): Company->getAllCompanies()</pre>
                                    </div>
                                    <div class="error-actions">
                                        <button class="btn btn-sm btn-outline-primary" onclick="viewFullError('error_001')">
                                            <i class="fas fa-expand"></i> View Full
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" onclick="markResolved('error_001')">
                                            <i class="fas fa-check"></i> Mark Resolved
                                        </button>
                                    </div>
                                </div>
                                <div class="error-item">
                                    <div class="error-header">
                                        <div class="error-level level-error">ERROR</div>
                                        <div class="error-time">2025-01-28 13:45:22</div>
                                    </div>
                                    <div class="error-title">API Rate Limit Exceeded</div>
                                    <div class="error-message">
                                        <pre>Rate limit exceeded for API key: brc_live_sk_...XY7Z
Request: GET /api/v1/companies
Limit: 100 requests per minute
Current: 156 requests in last minute</pre>
                                    </div>
                                    <div class="error-actions">
                                        <button class="btn btn-sm btn-outline-primary" onclick="viewFullError('error_002')">
                                            <i class="fas fa-expand"></i> View Full
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" onclick="markResolved('error_002')">
                                            <i class="fas fa-check"></i> Mark Resolved
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Tab -->
                <div class="tab-pane fade" id="performance" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Response Time Trends</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="responseTimeChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Memory Usage</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="memoryUsageChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Slow Queries</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Query</th>
                                            <th>Execution Time</th>
                                            <th>Frequency</th>
                                            <th>Last Executed</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><code>SELECT * FROM companies WHERE status = 'active'</code></td>
                                            <td><span class="text-danger">2.4s</span></td>
                                            <td>47 times</td>
                                            <td>2 minutes ago</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="optimizeQuery('query_001')">
                                                    <i class="fas fa-bolt"></i> Optimize
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><code>SELECT * FROM modules m JOIN company_modules cm ON...</code></td>
                                            <td><span class="text-warning">1.8s</span></td>
                                            <td>23 times</td>
                                            <td>5 minutes ago</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="optimizeQuery('query_002')">
                                                    <i class="fas fa-bolt"></i> Optimize
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Log Details Modal -->
<div class="modal fade" id="logDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Entry Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="logDetailsContent">
                    <!-- Log details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="downloadLogEntry()">
                    <i class="fas fa-download"></i> Download
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.log-stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #e3e6f0;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #5a5c69;
    line-height: 1;
}

.stat-label {
    color: #858796;
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.stat-change {
    font-size: 0.875rem;
    color: #6c757d;
}

.search-box {
    position: relative;
}

.search-box input {
    padding-right: 40px;
}

.search-box i {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
}

.log-nav {
    position: sticky;
    top: 20px;
}

.log-nav .nav-link {
    color: #5a5c69;
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    margin-bottom: 0.25rem;
    text-align: left;
    transition: all 0.3s ease;
}

.log-nav .nav-link:hover {
    background: #f8f9fc;
    color: #5a5c69;
}

.log-nav .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.log-nav .nav-link i {
    width: 20px;
    margin-right: 10px;
}

.log-viewer {
    max-height: 600px;
    overflow-y: auto;
    background: #1a1a1a;
    color: #ffffff;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
}

.log-entry {
    display: flex;
    align-items: center;
    padding: 0.5rem 1rem;
    border-bottom: 1px solid #333;
    gap: 1rem;
}

.log-entry:hover {
    background: #2a2a2a;
}

.log-timestamp {
    color: #888;
    min-width: 120px;
    font-size: 0.8rem;
}

.log-level {
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: bold;
    font-size: 0.7rem;
    min-width: 60px;
    text-align: center;
}

.level-info {
    background: #17a2b8;
    color: white;
}

.level-warning {
    background: #ffc107;
    color: #000;
}

.level-error {
    background: #dc3545;
    color: white;
}

.level-success {
    background: #28a745;
    color: white;
}

.level-critical {
    background: #6f42c1;
    color: white;
}

.log-type {
    background: #495057;
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.7rem;
    min-width: 80px;
    text-align: center;
}

.log-message {
    flex: 1;
    color: #fff;
}

.log-actions {
    min-width: 40px;
}

.severity-medium { color: #ffc107; }
.severity-high { color: #fd7e14; }
.severity-critical { color: #dc3545; }

.audit-timeline {
    padding: 1rem;
}

.audit-entry {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e3e6f0;
}

.audit-entry:last-child {
    border-bottom: none;
}

.audit-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.875rem;
}

.audit-content {
    flex: 1;
}

.audit-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.audit-time {
    color: #6c757d;
    font-size: 0.875rem;
}

.audit-details p {
    margin-bottom: 0.5rem;
}

.error-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.error-item {
    background: #f8f9fc;
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    padding: 1rem;
}

.error-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.error-time {
    color: #6c757d;
    font-size: 0.875rem;
}

.error-title {
    font-weight: 600;
    color: #5a5c69;
    margin-bottom: 0.5rem;
}

.error-message pre {
    background: #f1f3f4;
    padding: 1rem;
    border-radius: 4px;
    font-size: 0.8rem;
    max-height: 200px;
    overflow-y: auto;
}

.error-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

.card {
    border: 1px solid #e3e6f0;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.table th {
    border-top: none;
    font-weight: 600;
}

.table td {
    vertical-align: middle;
    border-color: #e3e6f0;
}

.table tbody tr:hover {
    background-color: #f8f9fc;
}
</style>

<script>
let autoRefreshInterval;
let isRealTimePaused = false;

document.addEventListener('DOMContentLoaded', function() {
    initPerformanceCharts();
    startAutoRefresh();
});

function initPerformanceCharts() {
    // Response Time Chart
    const responseCtx = document.getElementById('responseTimeChart').getContext('2d');
    new Chart(responseCtx, {
        type: 'line',
        data: {
            labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'],
            datasets: [{
                label: 'Response Time (ms)',
                data: [120, 145, 180, 165, 142, 138],
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Memory Usage Chart
    const memoryCtx = document.getElementById('memoryUsageChart').getContext('2d');
    new Chart(memoryCtx, {
        type: 'doughnut',
        data: {
            labels: ['Used', 'Available'],
            datasets: [{
                data: [65, 35],
                backgroundColor: ['#dc3545', '#e9ecef'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

function startAutoRefresh() {
    if (document.getElementById('autoRefresh').checked && !isRealTimePaused) {
        autoRefreshInterval = setInterval(refreshRealtimeLogs, 5000);
    }
}

function refreshRealtimeLogs() {
    if (isRealTimePaused) return;
    
    fetch('/platform/ajax/logs.php?action=get_realtime')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.logs) {
                updateRealtimeView(data.logs);
            }
        })
        .catch(console.error);
}

function updateRealtimeView(logs) {
    const viewer = document.getElementById('realtimeLogViewer');
    // Add new logs to the top
    logs.forEach(log => {
        const logEntry = createLogEntry(log);
        viewer.insertBefore(logEntry, viewer.firstChild);
    });
    
    // Keep only last 100 entries
    while (viewer.children.length > 100) {
        viewer.removeChild(viewer.lastChild);
    }
}

function createLogEntry(log) {
    const entry = document.createElement('div');
    entry.className = `log-entry log-${log.level}`;
    entry.innerHTML = `
        <div class="log-timestamp">${log.timestamp}</div>
        <div class="log-level level-${log.level}">${log.level.toUpperCase()}</div>
        <div class="log-type">${log.type.toUpperCase()}</div>
        <div class="log-message">${log.message}</div>
        <div class="log-actions">
            <button class="btn btn-sm btn-link" onclick="viewLogDetails('${log.id}')">
                <i class="fas fa-eye"></i>
            </button>
        </div>
    `;
    return entry;
}

// Auto-refresh toggle
document.getElementById('autoRefresh').addEventListener('change', function() {
    if (this.checked) {
        startAutoRefresh();
    } else {
        clearInterval(autoRefreshInterval);
    }
});

// Filter functions
document.getElementById('logLevel').addEventListener('change', applyFilters);
document.getElementById('logType').addEventListener('change', applyFilters);
document.getElementById('timeRange').addEventListener('change', applyFilters);
document.getElementById('searchLogs').addEventListener('input', applyFilters);

function applyFilters() {
    const level = document.getElementById('logLevel').value;
    const type = document.getElementById('logType').value;
    const timeRange = document.getElementById('timeRange').value;
    const search = document.getElementById('searchLogs').value.toLowerCase();
    
    // Apply filters to current view
    const entries = document.querySelectorAll('.log-entry');
    entries.forEach(entry => {
        const entryLevel = entry.querySelector('.log-level').textContent.toLowerCase();
        const entryType = entry.querySelector('.log-type').textContent.toLowerCase();
        const entryMessage = entry.querySelector('.log-message').textContent.toLowerCase();
        
        const matchesLevel = !level || entryLevel.includes(level);
        const matchesType = !type || entryType.includes(type);
        const matchesSearch = !search || entryMessage.includes(search);
        
        entry.style.display = (matchesLevel && matchesType && matchesSearch) ? '' : 'none';
    });
}

function clearFilters() {
    document.getElementById('logLevel').value = '';
    document.getElementById('logType').value = '';
    document.getElementById('timeRange').value = '24h';
    document.getElementById('searchLogs').value = '';
    applyFilters();
}

// Log management functions
function pauseRealtime() {
    isRealTimePaused = !isRealTimePaused;
    const button = event.target.closest('button');
    if (isRealTimePaused) {
        button.innerHTML = '<i class="fas fa-play"></i> Resume';
        button.className = 'btn btn-sm btn-outline-success';
        clearInterval(autoRefreshInterval);
    } else {
        button.innerHTML = '<i class="fas fa-pause"></i> Pause';
        button.className = 'btn btn-sm btn-outline-primary';
        startAutoRefresh();
    }
}

function clearRealtimeView() {
    if (confirm('Clear the current log view?')) {
        document.getElementById('realtimeLogViewer').innerHTML = '';
    }
}

function refreshLogs() {
    location.reload();
}

function clearLogs() {
    if (confirm('This will permanently delete all log files. Are you sure?')) {
        fetch('/platform/ajax/logs.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=clear_logs'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Logs cleared successfully!', 'success');
                location.reload();
            } else {
                showNotification(data.message || 'Error clearing logs', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error clearing logs', 'error');
        });
    }
}

function downloadLogs() {
    const timeRange = document.getElementById('timeRange').value;
    const level = document.getElementById('logLevel').value;
    const type = document.getElementById('logType').value;
    
    const params = new URLSearchParams({
        action: 'download_logs',
        time_range: timeRange,
        level: level,
        type: type
    });
    
    window.location.href = `/platform/ajax/logs.php?${params}`;
}

function viewLogDetails(logId) {
    fetch(`/platform/ajax/logs.php?action=get_log_details&id=${logId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('logDetailsContent').innerHTML = data.html;
                bootstrap.Modal.getOrCreateInstance(document.getElementById('logDetailsModal')).show();
            } else {
                showNotification('Error loading log details', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error loading log details', 'error');
        });
}

function downloadLogEntry() {
    // Download current log entry details
    const content = document.getElementById('logDetailsContent').textContent;
    const blob = new Blob([content], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.style.display = 'none';
    a.href = url;
    a.download = `log_entry_${Date.now()}.txt`;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
}

// Security functions
function blockIP(ip) {
    if (confirm(`Block IP address ${ip}?`)) {
        fetch('/platform/ajax/logs.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=block_ip&ip=${ip}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(`IP ${ip} blocked successfully!`, 'success');
            } else {
                showNotification(data.message || 'Error blocking IP', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error blocking IP', 'error');
        });
    }
}

function revokeApiKey(keyId) {
    if (confirm('Revoke this API key?')) {
        fetch('/platform/ajax/logs.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=revoke_api_key&key_id=${keyId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('API key revoked successfully!', 'success');
            } else {
                showNotification(data.message || 'Error revoking API key', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error revoking API key', 'error');
        });
    }
}

// Error management
function viewFullError(errorId) {
    // Expand error details
    console.log('View full error:', errorId);
}

function markResolved(errorId) {
    fetch('/platform/ajax/logs.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=mark_resolved&error_id=${errorId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Error marked as resolved!', 'success');
            // Remove or update the error item
        } else {
            showNotification(data.message || 'Error marking as resolved', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error marking as resolved', 'error');
    });
}

// Performance optimization
function optimizeQuery(queryId) {
    fetch('/platform/ajax/logs.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=optimize_query&query_id=${queryId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Query optimization suggestions generated!', 'success');
            // Show optimization suggestions
        } else {
            showNotification(data.message || 'Error generating optimization', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error generating optimization', 'error');
    });
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

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    clearInterval(autoRefreshInterval);
});
</script>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php include __DIR__ . '/../layout/footer.php'; ?>