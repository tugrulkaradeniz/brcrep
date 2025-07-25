<?php
// platform/pages/api.php
require_once __DIR__ . '/../../config/config.php';

// Check admin authentication
if (!isset($_SESSION['platform_admin_id'])) {
    header('Location: /platform/auth/login');
    exit;
}

$page_title = 'API Management';
include __DIR__ . '/../layout/header.php';
?>

<div class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">
                    <i class="fas fa-code text-primary"></i>
                    API Management
                </h1>
                <p class="page-subtitle">Manage API keys, endpoints, and documentation</p>
            </div>
            <div class="col-auto">
                <div class="btn-group">
                    <button class="btn btn-outline-primary" onclick="openApiDocs()">
                        <i class="fas fa-book"></i> API Docs
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createApiKeyModal">
                        <i class="fas fa-key"></i> Generate API Key
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- API Overview -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="api-stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-key"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">12</div>
                    <div class="stat-label">Active API Keys</div>
                    <div class="stat-change positive">+2 this week</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="api-stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">1,247</div>
                    <div class="stat-label">API Calls Today</div>
                    <div class="stat-change positive">+15% from yesterday</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="api-stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">98.7%</div>
                    <div class="stat-label">Uptime</div>
                    <div class="stat-change positive">99.9% this month</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="api-stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">142ms</div>
                    <div class="stat-label">Avg Response Time</div>
                    <div class="stat-change positive">-12ms improvement</div>
                </div>
            </div>
        </div>
    </div>

    <!-- API Tabs -->
    <div class="row">
        <div class="col-lg-3">
            <div class="api-nav">
                <div class="nav flex-column nav-pills" id="apiTab" role="tablist">
                    <button class="nav-link active" id="keys-tab" data-bs-toggle="pill" data-bs-target="#keys" type="button" role="tab">
                        <i class="fas fa-key"></i> API Keys
                    </button>
                    <button class="nav-link" id="endpoints-tab" data-bs-toggle="pill" data-bs-target="#endpoints" type="button" role="tab">
                        <i class="fas fa-plug"></i> Endpoints
                    </button>
                    <button class="nav-link" id="usage-tab" data-bs-toggle="pill" data-bs-target="#usage" type="button" role="tab">
                        <i class="fas fa-chart-bar"></i> Usage Analytics
                    </button>
                    <button class="nav-link" id="rate-limits-tab" data-bs-toggle="pill" data-bs-target="#rate-limits" type="button" role="tab">
                        <i class="fas fa-shield-alt"></i> Rate Limits
                    </button>
                    <button class="nav-link" id="webhooks-tab" data-bs-toggle="pill" data-bs-target="#webhooks" type="button" role="tab">
                        <i class="fas fa-rss"></i> Webhooks
                    </button>
                    <button class="nav-link" id="logs-tab" data-bs-toggle="pill" data-bs-target="#logs" type="button" role="tab">
                        <i class="fas fa-list"></i> API Logs
                    </button>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <div class="tab-content" id="apiTabContent">
                <!-- API Keys Tab -->
                <div class="tab-pane fade show active" id="keys" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="card-title mb-0">API Keys Management</h5>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createApiKeyModal">
                                        <i class="fas fa-plus"></i> Generate New Key
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Key Name</th>
                                            <th>Key</th>
                                            <th>Permissions</th>
                                            <th>Last Used</th>
                                            <th>Usage</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="key-info">
                                                    <strong>Production Master</strong>
                                                    <small class="d-block text-muted">Full access key</small>
                                                </div>
                                            </td>
                                            <td>
                                                <code class="api-key">brc_live_sk_1A2B3C...XY7Z</code>
                                                <button class="btn btn-sm btn-link p-0 ms-2" onclick="copyToClipboard('brc_live_sk_1A2B3C4D5E6F7G8H9I0J1K2L3M4N5O6P7Q8R9S0T1U2V3W4X5Y6Z')">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <span class="badge bg-danger">Full Access</span>
                                            </td>
                                            <td>2 hours ago</td>
                                            <td>
                                                <div class="usage-bar">
                                                    <div class="usage-fill" style="width: 65%"></div>
                                                </div>
                                                <small class="text-muted">650/1000 requests</small>
                                            </td>
                                            <td><span class="status-badge status-active">Active</span></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="editApiKey(1)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-warning" onclick="rotateApiKey(1)">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="revokeApiKey(1)">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="key-info">
                                                    <strong>Mobile App</strong>
                                                    <small class="d-block text-muted">Read-only access</small>
                                                </div>
                                            </td>
                                            <td>
                                                <code class="api-key">brc_live_pk_9Z8Y7X...321A</code>
                                                <button class="btn btn-sm btn-link p-0 ms-2" onclick="copyToClipboard('brc_live_pk_9Z8Y7X6W5V4U3T2S1R0Q9P8O7N6M5L4K3J2I1H0G9F8E7D6C5B4A321Z')">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">Read Only</span>
                                            </td>
                                            <td>1 day ago</td>
                                            <td>
                                                <div class="usage-bar">
                                                    <div class="usage-fill" style="width: 23%"></div>
                                                </div>
                                                <small class="text-muted">230/1000 requests</small>
                                            </td>
                                            <td><span class="status-badge status-active">Active</span></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="editApiKey(2)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-warning" onclick="rotateApiKey(2)">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="revokeApiKey(2)">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="key-info">
                                                    <strong>Analytics Service</strong>
                                                    <small class="d-block text-muted">Analytics endpoints only</small>
                                                </div>
                                            </td>
                                            <td>
                                                <code class="api-key">brc_live_ak_4F5G6H...890B</code>
                                                <button class="btn btn-sm btn-link p-0 ms-2" onclick="copyToClipboard('brc_live_ak_4F5G6H7I8J9K0L1M2N3O4P5Q6R7S8T9U0V1W2X3Y4Z5A6B7C8D9E0F1G2H3I4J5K6L890B')">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning text-dark">Limited</span>
                                            </td>
                                            <td>Never used</td>
                                            <td>
                                                <div class="usage-bar">
                                                    <div class="usage-fill" style="width: 0%"></div>
                                                </div>
                                                <small class="text-muted">0/500 requests</small>
                                            </td>
                                            <td><span class="status-badge status-inactive">Inactive</span></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="editApiKey(3)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-warning" onclick="rotateApiKey(3)">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="revokeApiKey(3)">
                                                        <i class="fas fa-ban"></i>
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

                <!-- API Endpoints Tab -->
                <div class="tab-pane fade" id="endpoints" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Available API Endpoints</h5>
                        </div>
                        <div class="card-body">
                            <div class="endpoint-group">
                                <h6 class="endpoint-group-title">
                                    <i class="fas fa-building text-primary"></i> Companies
                                </h6>
                                <div class="endpoint-list">
                                    <div class="endpoint-item">
                                        <div class="endpoint-method get">GET</div>
                                        <div class="endpoint-path">/api/v1/companies</div>
                                        <div class="endpoint-description">List all companies</div>
                                        <div class="endpoint-actions">
                                            <button class="btn btn-sm btn-outline-primary" onclick="testEndpoint('/api/v1/companies', 'GET')">
                                                <i class="fas fa-play"></i> Test
                                            </button>
                                        </div>
                                    </div>
                                    <div class="endpoint-item">
                                        <div class="endpoint-method post">POST</div>
                                        <div class="endpoint-path">/api/v1/companies</div>
                                        <div class="endpoint-description">Create new company</div>
                                        <div class="endpoint-actions">
                                            <button class="btn btn-sm btn-outline-primary" onclick="testEndpoint('/api/v1/companies', 'POST')">
                                                <i class="fas fa-play"></i> Test
                                            </button>
                                        </div>
                                    </div>
                                    <div class="endpoint-item">
                                        <div class="endpoint-method get">GET</div>
                                        <div class="endpoint-path">/api/v1/companies/{id}</div>
                                        <div class="endpoint-description">Get company by ID</div>
                                        <div class="endpoint-actions">
                                            <button class="btn btn-sm btn-outline-primary" onclick="testEndpoint('/api/v1/companies/1', 'GET')">
                                                <i class="fas fa-play"></i> Test
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="endpoint-group">
                                <h6 class="endpoint-group-title">
                                    <i class="fas fa-puzzle-piece text-success"></i> Modules
                                </h6>
                                <div class="endpoint-list">
                                    <div class="endpoint-item">
                                        <div class="endpoint-method get">GET</div>
                                        <div class="endpoint-path">/api/v1/modules</div>
                                        <div class="endpoint-description">List available modules</div>
                                        <div class="endpoint-actions">
                                            <button class="btn btn-sm btn-outline-primary" onclick="testEndpoint('/api/v1/modules', 'GET')">
                                                <i class="fas fa-play"></i> Test
                                            </button>
                                        </div>
                                    </div>
                                    <div class="endpoint-item">
                                        <div class="endpoint-method post">POST</div>
                                        <div class="endpoint-path">/api/v1/modules/{id}/install</div>
                                        <div class="endpoint-description">Install module for company</div>
                                        <div class="endpoint-actions">
                                            <button class="btn btn-sm btn-outline-primary" onclick="testEndpoint('/api/v1/modules/1/install', 'POST')">
                                                <i class="fas fa-play"></i> Test
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="endpoint-group">
                                <h6 class="endpoint-group-title">
                                    <i class="fas fa-chart-bar text-info"></i> Analytics
                                </h6>
                                <div class="endpoint-list">
                                    <div class="endpoint-item">
                                        <div class="endpoint-method get">GET</div>
                                        <div class="endpoint-path">/api/v1/analytics/revenue</div>
                                        <div class="endpoint-description">Get revenue analytics</div>
                                        <div class="endpoint-actions">
                                            <button class="btn btn-sm btn-outline-primary" onclick="testEndpoint('/api/v1/analytics/revenue', 'GET')">
                                                <i class="fas fa-play"></i> Test
                                            </button>
                                        </div>
                                    </div>
                                    <div class="endpoint-item">
                                        <div class="endpoint-method get">GET</div>
                                        <div class="endpoint-path">/api/v1/analytics/usage</div>
                                        <div class="endpoint-description">Get usage statistics</div>
                                        <div class="endpoint-actions">
                                            <button class="btn btn-sm btn-outline-primary" onclick="testEndpoint('/api/v1/analytics/usage', 'GET')">
                                                <i class="fas fa-play"></i> Test
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Usage Analytics Tab -->
                <div class="tab-pane fade" id="usage" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="card-title mb-0">API Usage Analytics</h5>
                                </div>
                                <div class="col-auto">
                                    <select class="form-select form-select-sm" id="usageTimeRange">
                                        <option value="24h">Last 24 Hours</option>
                                        <option value="7d" selected>Last 7 Days</option>
                                        <option value="30d">Last 30 Days</option>
                                        <option value="90d">Last 90 Days</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="apiUsageChart" height="300"></canvas>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Top Endpoints</h6>
                                </div>
                                <div class="card-body">
                                    <div class="endpoint-usage-list">
                                        <div class="usage-item">
                                            <div class="usage-endpoint">/api/v1/companies</div>
                                            <div class="usage-count">1,247 calls</div>
                                            <div class="usage-percentage">45%</div>
                                        </div>
                                        <div class="usage-item">
                                            <div class="usage-endpoint">/api/v1/modules</div>
                                            <div class="usage-count">892 calls</div>
                                            <div class="usage-percentage">32%</div>
                                        </div>
                                        <div class="usage-item">
                                            <div class="usage-endpoint">/api/v1/analytics/revenue</div>
                                            <div class="usage-count">456 calls</div>
                                            <div class="usage-percentage">16%</div>
                                        </div>
                                        <div class="usage-item">
                                            <div class="usage-endpoint">/api/v1/users</div>
                                            <div class="usage-count">234 calls</div>
                                            <div class="usage-percentage">7%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Error Rates</h6>
                                </div>
                                <div class="card-body">
                                    <div class="error-stats">
                                        <div class="error-item">
                                            <div class="error-code">200 OK</div>
                                            <div class="error-count">2,567</div>
                                            <div class="error-percentage success">92.5%</div>
                                        </div>
                                        <div class="error-item">
                                            <div class="error-code">400 Bad Request</div>
                                            <div class="error-count">134</div>
                                            <div class="error-percentage warning">4.8%</div>
                                        </div>
                                        <div class="error-item">
                                            <div class="error-code">401 Unauthorized</div>
                                            <div class="error-count">45</div>
                                            <div class="error-percentage warning">1.6%</div>
                                        </div>
                                        <div class="error-item">
                                            <div class="error-code">500 Server Error</div>
                                            <div class="error-count">12</div>
                                            <div class="error-percentage danger">0.4%</div>
                                        </div>
                                        <div class="error-item">
                                            <div class="error-code">429 Rate Limited</div>
                                            <div class="error-count">18</div>
                                            <div class="error-percentage warning">0.7%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rate Limits Tab -->
                <div class="tab-pane fade" id="rate-limits" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Rate Limiting Configuration</h5>
                        </div>
                        <div class="card-body">
                            <form id="rateLimitsForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Default Rate Limit (per minute)</label>
                                            <input type="number" class="form-control" name="default_rate_limit" value="100" min="1" max="10000">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Burst Rate Limit</label>
                                            <input type="number" class="form-control" name="burst_rate_limit" value="200" min="1" max="20000">
                                        </div>
                                    </div>
                                </div>
                                
                                <h6>Custom Rate Limits by Endpoint</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Endpoint</th>
                                                <th>Method</th>
                                                <th>Rate Limit</th>
                                                <th>Window</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>/api/v1/companies</td>
                                                <td><span class="badge bg-success">GET</span></td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm" value="200" min="1">
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm">
                                                        <option value="minute" selected>Per Minute</option>
                                                        <option value="hour">Per Hour</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="removeRateLimit(this)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>/api/v1/companies</td>
                                                <td><span class="badge bg-warning text-dark">POST</span></td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm" value="10" min="1">
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm">
                                                        <option value="minute" selected>Per Minute</option>
                                                        <option value="hour">Per Hour</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="removeRateLimit(this)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-3">
                                    <button type="button" class="btn btn-outline-secondary" onclick="addCustomRateLimit()">
                                        <i class="fas fa-plus"></i> Add Custom Rate Limit
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Rate Limits
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Webhooks Tab -->
                <div class="tab-pane fade" id="webhooks" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="card-title mb-0">Webhook Management</h5>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createWebhookModal">
                                        <i class="fas fa-plus"></i> Add Webhook
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="webhook-list">
                                <div class="webhook-item">
                                    <div class="webhook-header">
                                        <h6>Company Registration</h6>
                                        <span class="status-badge status-active">Active</span>
                                    </div>
                                    <div class="webhook-details">
                                        <p><strong>URL:</strong> https://app.example.com/webhooks/company-registered</p>
                                        <p><strong>Events:</strong> company.created, company.updated</p>
                                        <p><strong>Last delivery:</strong> 2 hours ago (Success)</p>
                                    </div>
                                    <div class="webhook-actions">
                                        <button class="btn btn-sm btn-outline-primary" onclick="testWebhook(1)">
                                            <i class="fas fa-paper-plane"></i> Test
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" onclick="viewWebhookLogs(1)">
                                            <i class="fas fa-list"></i> Logs
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="editWebhook(1)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteWebhook(1)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>

                                <div class="webhook-item">
                                    <div class="webhook-header">
                                        <h6>Payment Processing</h6>
                                        <span class="status-badge status-active">Active</span>
                                    </div>
                                    <div class="webhook-details">
                                        <p><strong>URL:</strong> https://billing.example.com/webhooks/payment</p>
                                        <p><strong>Events:</strong> payment.succeeded, payment.failed</p>
                                        <p><strong>Last delivery:</strong> 1 day ago (Success)</p>
                                    </div>
                                    <div class="webhook-actions">
                                        <button class="btn btn-sm btn-outline-primary" onclick="testWebhook(2)">
                                            <i class="fas fa-paper-plane"></i> Test
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" onclick="viewWebhookLogs(2)">
                                            <i class="fas fa-list"></i> Logs
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="editWebhook(2)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteWebhook(2)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- API Logs Tab -->
                <div class="tab-pane fade" id="logs" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="card-title mb-0">API Request Logs</h5>
                                </div>
                                <div class="col-auto">
                                    <div class="btn-group">
                                        <select class="form-select form-select-sm me-2" id="logFilter">
                                            <option value="">All Requests</option>
                                            <option value="2xx">Success (2xx)</option>
                                            <option value="4xx">Client Errors (4xx)</option>
                                            <option value="5xx">Server Errors (5xx)</option>
                                        </select>
                                        <button class="btn btn-sm btn-outline-primary" onclick="refreshLogs()">
                                            <i class="fas fa-sync-alt"></i> Refresh
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Timestamp</th>
                                            <th>Method</th>
                                            <th>Endpoint</th>
                                            <th>Status</th>
                                            <th>Response Time</th>
                                            <th>API Key</th>
                                            <th>IP Address</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>2025-01-28 14:23:45</td>
                                            <td><span class="method-badge get">GET</span></td>
                                            <td>/api/v1/companies</td>
                                            <td><span class="status-badge status-success">200</span></td>
                                            <td>145ms</td>
                                            <td><code>...XY7Z</code></td>
                                            <td>192.168.1.100</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="viewLogDetails('log_1')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>2025-01-28 14:22:31</td>
                                            <td><span class="method-badge post">POST</span></td>
                                            <td>/api/v1/companies</td>
                                            <td><span class="status-badge status-success">201</span></td>
                                            <td>267ms</td>
                                            <td><code>...XY7Z</code></td>
                                            <td>192.168.1.100</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="viewLogDetails('log_2')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>2025-01-28 14:21:15</td>
                                            <td><span class="method-badge get">GET</span></td>
                                            <td>/api/v1/analytics/revenue</td>
                                            <td><span class="status-badge status-error">500</span></td>
                                            <td>1,234ms</td>
                                            <td><code>...321A</code></td>
                                            <td>10.0.0.50</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="viewLogDetails('log_3')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row align-items-center">
                                <div class="col">
                                    <small class="text-muted">Showing last 100 requests</small>
                                </div>
                                <div class="col-auto">
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0">
                                            <li class="page-item disabled">
                                                <span class="page-link">Previous</span>
                                            </li>
                                            <li class="page-item active">
                                                <span class="page-link">1</span>
                                            </li>
                                            <li class="page-item">
                                                <a class="page-link" href="#">2</a>
                                            </li>
                                            <li class="page-item">
                                                <a class="page-link" href="#">3</a>
                                            </li>
                                            <li class="page-item">
                                                <a class="page-link" href="#">Next</a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create API Key Modal -->
<div class="modal fade" id="createApiKeyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate New API Key</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createApiKeyForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Key Name *</label>
                        <input type="text" class="form-control" name="key_name" required>
                        <div class="form-text">A descriptive name for this API key</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Permissions *</label>
                        <select class="form-select" name="permissions" required>
                            <option value="">Select permission level</option>
                            <option value="read">Read Only</option>
                            <option value="write">Read & Write</option>
                            <option value="admin">Full Access</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rate Limit (requests per minute)</label>
                        <input type="number" class="form-control" name="rate_limit" value="100" min="1" max="10000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expiration Date</label>
                        <input type="date" class="form-control" name="expires_at">
                        <div class="form-text">Leave empty for no expiration</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Allowed IPs (optional)</label>
                        <textarea class="form-control" name="allowed_ips" rows="3" placeholder="192.168.1.1&#10;10.0.0.0/8"></textarea>
                        <div class="form-text">One IP address or CIDR range per line. Leave empty to allow all IPs.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate API Key</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Webhook Modal -->
<div class="modal fade" id="createWebhookModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Webhook</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createWebhookForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Webhook Name *</label>
                        <input type="text" class="form-control" name="webhook_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Endpoint URL *</label>
                        <input type="url" class="form-control" name="webhook_url" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Events</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="events[]" value="company.created" id="companyCreated">
                            <label class="form-check-label" for="companyCreated">Company Created</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="events[]" value="company.updated" id="companyUpdated">
                            <label class="form-check-label" for="companyUpdated">Company Updated</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="events[]" value="payment.succeeded" id="paymentSucceeded">
                            <label class="form-check-label" for="paymentSucceeded">Payment Succeeded</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="events[]" value="payment.failed" id="paymentFailed">
                            <label class="form-check-label" for="paymentFailed">Payment Failed</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Secret</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="webhook_secret" id="webhookSecret">
                            <button class="btn btn-outline-secondary" type="button" onclick="generateWebhookSecret()">Generate</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Webhook</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.api-stat-card {
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
}

.stat-change.positive { color: #28a745; }

.api-nav {
    position: sticky;
    top: 20px;
}

.api-nav .nav-link {
    color: #5a5c69;
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    margin-bottom: 0.25rem;
    text-align: left;
    transition: all 0.3s ease;
}

.api-nav .nav-link:hover {
    background: #f8f9fc;
    color: #5a5c69;
}

.api-nav .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.api-nav .nav-link i {
    width: 20px;
    margin-right: 10px;
}

.api-key {
    background: #f8f9fc;
    color: #6c757d;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.875rem;
    font-family: monospace;
}

.usage-bar {
    width: 100px;
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.usage-fill {
    height: 100%;
    background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
    transition: width 0.3s ease;
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

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

.status-success {
    background: #d4edda;
    color: #155724;
}

.status-error {
    background: #f8d7da;
    color: #721c24;
}

.endpoint-group {
    margin-bottom: 2rem;
}

.endpoint-group-title {
    color: #5a5c69;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e3e6f0;
}

.endpoint-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    background: #f8f9fc;
    border-radius: 8px;
    margin-bottom: 0.5rem;
}

.endpoint-method {
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: bold;
    font-size: 0.75rem;
    min-width: 60px;
    text-align: center;
}

.endpoint-method.get {
    background: #d4edda;
    color: #155724;
}

.endpoint-method.post {
    background: #fff3cd;
    color: #856404;
}

.endpoint-method.put {
    background: #cce5ff;
    color: #004085;
}

.endpoint-method.delete {
    background: #f8d7da;
    color: #721c24;
}

.endpoint-path {
    font-family: monospace;
    background: white;
    padding: 4px 8px;
    border-radius: 4px;
    flex: 1;
}

.endpoint-description {
    color: #6c757d;
    font-size: 0.875rem;
    flex: 2;
}

.usage-item, .error-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e3e6f0;
}

.usage-item:last-child, .error-item:last-child {
    border-bottom: none;
}

.usage-endpoint, .error-code {
    font-family: monospace;
    font-size: 0.875rem;
}

.error-percentage.success { color: #28a745; }
.error-percentage.warning { color: #ffc107; }
.error-percentage.danger { color: #dc3545; }

.webhook-item {
    background: #f8f9fc;
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1rem;
}

.webhook-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.webhook-header h6 {
    margin: 0;
    color: #5a5c69;
}

.webhook-details p {
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.webhook-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.method-badge {
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: bold;
    font-size: 0.7rem;
}

.method-badge.get {
    background: #d4edda;
    color: #155724;
}

.method-badge.post {
    background: #fff3cd;
    color: #856404;
}

.method-badge.put {
    background: #cce5ff;
    color: #004085;
}

.method-badge.delete {
    background: #f8d7da;
    color: #721c24;
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

.card {
    border: 1px solid #e3e6f0;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initApiUsageChart();
});

function initApiUsageChart() {
    const ctx = document.getElementById('apiUsageChart').getContext('2d');
    window.apiUsageChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'API Calls',
                data: [1200, 1900, 800, 1500, 2000, 1100, 900],
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
                            return value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

// API Key Management
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showNotification('API key copied to clipboard!', 'success');
    });
}

function editApiKey(id) {
    // Open edit modal or redirect to edit page
    console.log('Edit API key:', id);
}

function rotateApiKey(id) {
    if (confirm('Generate a new API key? The old key will be immediately revoked.')) {
        fetch('/platform/ajax/api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=rotate_key&key_id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('API key rotated successfully!', 'success');
                location.reload();
            } else {
                showNotification(data.message || 'Error rotating API key', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error rotating API key', 'error');
        });
    }
}

function revokeApiKey(id) {
    if (confirm('Revoke this API key? This action cannot be undone.')) {
        fetch('/platform/ajax/api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=revoke_key&key_id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('API key revoked successfully!', 'success');
                location.reload();
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

// Endpoint Testing
function testEndpoint(endpoint, method) {
    // Open API testing modal or redirect to API testing tool
    window.open(`/platform/api/test?endpoint=${encodeURIComponent(endpoint)}&method=${method}`, '_blank');
}

// Webhook Management
function testWebhook(id) {
    fetch('/platform/ajax/api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=test_webhook&webhook_id=${id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Test webhook sent successfully!', 'success');
        } else {
            showNotification(data.message || 'Error sending test webhook', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error sending test webhook', 'error');
    });
}

function viewWebhookLogs(id) {
    window.open(`/platform/webhooks/${id}/logs`, '_blank');
}

function editWebhook(id) {
    // Open edit webhook modal
    console.log('Edit webhook:', id);
}

function deleteWebhook(id) {
    if (confirm('Delete this webhook? This action cannot be undone.')) {
        fetch('/platform/ajax/api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete_webhook&webhook_id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Webhook deleted successfully!', 'success');
                location.reload();
            } else {
                showNotification(data.message || 'Error deleting webhook', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error deleting webhook', 'error');
        });
    }
}

function generateWebhookSecret() {
    const secret = 'whsec_' + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
    document.getElementById('webhookSecret').value = secret;
}

// Rate Limiting
function addCustomRateLimit() {
    // Add new row to rate limits table
    const tbody = document.querySelector('#rate-limits tbody');
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" placeholder="/api/v1/endpoint"></td>
        <td>
            <select class="form-select form-select-sm">
                <option value="GET">GET</option>
                <option value="POST">POST</option>
                <option value="PUT">PUT</option>
                <option value="DELETE">DELETE</option>
            </select>
        </td>
        <td><input type="number" class="form-control form-control-sm" value="100" min="1"></td>
        <td>
            <select class="form-select form-select-sm">
                <option value="minute">Per Minute</option>
                <option value="hour">Per Hour</option>
            </select>
        </td>
        <td>
            <button class="btn btn-sm btn-outline-danger" onclick="removeRateLimit(this)">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(newRow);
}

function removeRateLimit(button) {
    if (confirm('Remove this rate limit?')) {
        button.closest('tr').remove();
    }
}

// Log Management
function viewLogDetails(logId) {
    // Open log details modal
    console.log('View log details:', logId);
}

function refreshLogs() {
    location.reload();
}

// API Documentation
function openApiDocs() {
    window.open('/platform/api/docs', '_blank');
}

// Form Handlers
document.getElementById('createApiKeyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'create_api_key');
    
    fetch('/platform/ajax/api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('API key generated successfully!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('createApiKeyModal')).hide();
            // Show the new API key in a modal or alert
            if (data.api_key) {
                alert(`Your new API key:\n\n${data.api_key}\n\nPlease save this key securely. You won't be able to see it again.`);
            }
            location.reload();
        } else {
            showNotification(data.message || 'Error generating API key', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error generating API key', 'error');
    });
});

document.getElementById('createWebhookForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'create_webhook');
    
    fetch('/platform/ajax/api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Webhook created successfully!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('createWebhookModal')).hide();
            location.reload();
        } else {
            showNotification(data.message || 'Error creating webhook', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error creating webhook', 'error');
    });
});

document.getElementById('rateLimitsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'save_rate_limits');
    
    fetch('/platform/ajax/api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Rate limits saved successfully!', 'success');
        } else {
            showNotification(data.message || 'Error saving rate limits', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error saving rate limits', 'error');
    });
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
</script>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php include __DIR__ . '/../layout/footer.php'; ?>