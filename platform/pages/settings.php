<?php
// platform/pages/settings.php
require_once __DIR__ . '/../../config/config.php';

// Check admin authentication
if (!isset($_SESSION['platform_admin_id'])) {
    header('Location: /platform/auth/login');
    exit;
}

$page_title = 'Platform Settings';
include __DIR__ . '/../layout/header.php';
?>

<div class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">
                    <i class="fas fa-cog text-primary"></i>
                    Platform Settings
                </h1>
                <p class="page-subtitle">Manage platform configuration and system settings</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-success" onclick="saveAllSettings()">
                    <i class="fas fa-save"></i> Save All Changes
                </button>
            </div>
        </div>
    </div>

    <!-- Settings Navigation -->
    <div class="row">
        <div class="col-lg-3">
            <div class="settings-nav">
                <div class="nav flex-column nav-pills" id="settingsTab" role="tablist">
                    <button class="nav-link active" id="general-tab" data-bs-toggle="pill" data-bs-target="#general" type="button" role="tab">
                        <i class="fas fa-cog"></i> General Settings
                    </button>
                    <button class="nav-link" id="email-tab" data-bs-toggle="pill" data-bs-target="#email" type="button" role="tab">
                        <i class="fas fa-envelope"></i> Email Configuration
                    </button>
                    <button class="nav-link" id="payment-tab" data-bs-toggle="pill" data-bs-target="#payment" type="button" role="tab">
                        <i class="fas fa-credit-card"></i> Payment Settings
                    </button>
                    <button class="nav-link" id="security-tab" data-bs-toggle="pill" data-bs-target="#security" type="button" role="tab">
                        <i class="fas fa-shield-alt"></i> Security
                    </button>
                    <button class="nav-link" id="api-tab" data-bs-toggle="pill" data-bs-target="#api" type="button" role="tab">
                        <i class="fas fa-code"></i> API Settings
                    </button>
                    <button class="nav-link" id="notifications-tab" data-bs-toggle="pill" data-bs-target="#notifications" type="button" role="tab">
                        <i class="fas fa-bell"></i> Notifications
                    </button>
                    <button class="nav-link" id="maintenance-tab" data-bs-toggle="pill" data-bs-target="#maintenance" type="button" role="tab">
                        <i class="fas fa-tools"></i> Maintenance
                    </button>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <div class="tab-content" id="settingsTabContent">
                <!-- General Settings -->
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">General Platform Settings</h5>
                        </div>
                        <div class="card-body">
                            <form id="generalSettingsForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Platform Name</label>
                                            <input type="text" class="form-control" name="platform_name" value="BRC Load Platform">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Platform URL</label>
                                            <input type="url" class="form-control" name="platform_url" value="https://brcload.com">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Default Timezone</label>
                                            <select class="form-select" name="timezone">
                                                <option value="UTC">UTC</option>
                                                <option value="America/New_York">Eastern Time</option>
                                                <option value="America/Chicago">Central Time</option>
                                                <option value="America/Denver">Mountain Time</option>
                                                <option value="America/Los_Angeles">Pacific Time</option>
                                                <option value="Europe/London">London</option>
                                                <option value="Europe/Istanbul" selected>Istanbul</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Default Language</label>
                                            <select class="form-select" name="language">
                                                <option value="en" selected>English</option>
                                                <option value="tr">Turkish</option>
                                                <option value="es">Spanish</option>
                                                <option value="fr">French</option>
                                                <option value="de">German</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Platform Description</label>
                                    <textarea class="form-control" name="platform_description" rows="3">Multi-tenant SaaS platform for business risk and compliance management.</textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="registration_enabled" id="registrationEnabled" checked>
                                                <label class="form-check-label" for="registrationEnabled">
                                                    Enable New Company Registration
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="trial_enabled" id="trialEnabled" checked>
                                                <label class="form-check-label" for="trialEnabled">
                                                    Enable Trial Accounts
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Trial Period (days)</label>
                                            <input type="number" class="form-control" name="trial_days" value="14" min="1" max="365">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Max Companies per Trial</label>
                                            <input type="number" class="form-control" name="max_trial_companies" value="100" min="1">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Email Configuration -->
                <div class="tab-pane fade" id="email" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Email Configuration</h5>
                        </div>
                        <div class="card-body">
                            <form id="emailSettingsForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">SMTP Host</label>
                                            <input type="text" class="form-control" name="smtp_host" value="smtp.gmail.com">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">SMTP Port</label>
                                            <input type="number" class="form-control" name="smtp_port" value="587">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">SMTP Username</label>
                                            <input type="email" class="form-control" name="smtp_username" value="admin@brcload.com">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">SMTP Password</label>
                                            <input type="password" class="form-control" name="smtp_password" placeholder="••••••••">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">From Email</label>
                                            <input type="email" class="form-control" name="from_email" value="noreply@brcload.com">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">From Name</label>
                                            <input type="text" class="form-control" name="from_name" value="BRC Load Platform">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Encryption</label>
                                            <select class="form-select" name="smtp_encryption">
                                                <option value="tls" selected>TLS</option>
                                                <option value="ssl">SSL</option>
                                                <option value="none">None</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <button type="button" class="btn btn-outline-primary mt-4" onclick="testEmailConnection()">
                                                <i class="fas fa-paper-plane"></i> Test Email Connection
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr>
                                <h6>Email Templates</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="welcome_email_enabled" id="welcomeEmail" checked>
                                            <label class="form-check-label" for="welcomeEmail">
                                                Welcome Email
                                            </label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="trial_reminder_enabled" id="trialReminder" checked>
                                            <label class="form-check-label" for="trialReminder">
                                                Trial Reminder
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="payment_confirmation_enabled" id="paymentConfirmation" checked>
                                            <label class="form-check-label" for="paymentConfirmation">
                                                Payment Confirmation
                                            </label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="monthly_report_enabled" id="monthlyReport" checked>
                                            <label class="form-check-label" for="monthlyReport">
                                                Monthly Reports
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Payment Settings -->
                <div class="tab-pane fade" id="payment" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Payment Gateway Settings</h5>
                        </div>
                        <div class="card-body">
                            <form id="paymentSettingsForm">
                                <div class="mb-4">
                                    <h6>Stripe Configuration</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="stripe_enabled" id="stripeEnabled" checked>
                                                    <label class="form-check-label" for="stripeEnabled">
                                                        Enable Stripe
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Environment</label>
                                                <select class="form-select" name="stripe_mode">
                                                    <option value="test" selected>Test Mode</option>
                                                    <option value="live">Live Mode</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Publishable Key</label>
                                                <input type="text" class="form-control" name="stripe_publishable_key" placeholder="pk_test_...">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Secret Key</label>
                                                <input type="password" class="form-control" name="stripe_secret_key" placeholder="sk_test_...">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6>PayPal Configuration</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="paypal_enabled" id="paypalEnabled">
                                                    <label class="form-check-label" for="paypalEnabled">
                                                        Enable PayPal
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Environment</label>
                                                <select class="form-select" name="paypal_mode">
                                                    <option value="sandbox" selected>Sandbox</option>
                                                    <option value="live">Live</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Client ID</label>
                                                <input type="text" class="form-control" name="paypal_client_id">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Client Secret</label>
                                                <input type="password" class="form-control" name="paypal_client_secret">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <h6>General Payment Settings</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Default Currency</label>
                                                <select class="form-select" name="default_currency">
                                                    <option value="USD" selected>USD - US Dollar</option>
                                                    <option value="EUR">EUR - Euro</option>
                                                    <option value="GBP">GBP - British Pound</option>
                                                    <option value="TRY">TRY - Turkish Lira</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Payment Retry Attempts</label>
                                                <input type="number" class="form-control" name="payment_retry_attempts" value="3" min="1" max="10">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="tab-pane fade" id="security" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Security Settings</h5>
                        </div>
                        <div class="card-body">
                            <form id="securitySettingsForm">
                                <div class="mb-4">
                                    <h6>Authentication Settings</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="two_factor_enabled" id="twoFactorEnabled">
                                                <label class="form-check-label" for="twoFactorEnabled">
                                                    Enable Two-Factor Authentication
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="password_expiry_enabled" id="passwordExpiryEnabled">
                                                <label class="form-check-label" for="passwordExpiryEnabled">
                                                    Enable Password Expiry
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Session Timeout (minutes)</label>
                                                <input type="number" class="form-control" name="session_timeout" value="60" min="15" max="480">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Password Expiry (days)</label>
                                                <input type="number" class="form-control" name="password_expiry_days" value="90" min="30" max="365">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6>Rate Limiting</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Login Attempts per Hour</label>
                                                <input type="number" class="form-control" name="login_rate_limit" value="5" min="1" max="100">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">API Requests per Minute</label>
                                                <input type="number" class="form-control" name="api_rate_limit" value="100" min="10" max="1000">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6>Password Policy</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Minimum Password Length</label>
                                                <input type="number" class="form-control" name="min_password_length" value="8" min="6" max="32">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="require_special_chars" id="requireSpecialChars" checked>
                                                <label class="form-check-label" for="requireSpecialChars">
                                                    Require Special Characters
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <h6>IP Restrictions</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Allowed IP Addresses (one per line)</label>
                                        <textarea class="form-control" name="allowed_ips" rows="4" placeholder="192.168.1.1&#10;10.0.0.0/8&#10;Leave empty to allow all IPs"></textarea>
                                        <div class="form-text">Use CIDR notation for ranges (e.g., 192.168.1.0/24)</div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- API Settings -->
                <div class="tab-pane fade" id="api" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="card-title mb-0">API Settings</h5>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-sm btn-outline-primary" onclick="generateApiKey()">
                                        <i class="fas fa-key"></i> Generate New Key
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="apiSettingsForm">
                                <div class="mb-4">
                                    <h6>API Configuration</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="api_enabled" id="apiEnabled" checked>
                                                <label class="form-check-label" for="apiEnabled">
                                                    Enable API Access
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="api_logging_enabled" id="apiLoggingEnabled" checked>
                                                <label class="form-check-label" for="apiLoggingEnabled">
                                                    Enable API Logging
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">API Base URL</label>
                                        <input type="url" class="form-control" name="api_base_url" value="https://api.brcload.com/v1" readonly>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6>Current API Keys</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Key Name</th>
                                                    <th>Key</th>
                                                    <th>Created</th>
                                                    <th>Last Used</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Master Key</td>
                                                    <td><code>brc_sk_live_...a1b2c3</code></td>
                                                    <td>Jan 15, 2025</td>
                                                    <td>2 hours ago</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-danger" onclick="revokeApiKey('master')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <h6>Webhook Settings</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Webhook URL</label>
                                        <input type="url" class="form-control" name="webhook_url" placeholder="https://your-app.com/webhooks/brc">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Webhook Secret</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" name="webhook_secret" placeholder="Enter webhook secret">
                                            <button class="btn btn-outline-secondary" type="button" onclick="generateWebhookSecret()">Generate</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="tab-pane fade" id="notifications" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Notification Settings</h5>
                        </div>
                        <div class="card-body">
                            <form id="notificationSettingsForm">
                                <div class="mb-4">
                                    <h6>Admin Notifications</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" name="new_company_notification" id="newCompanyNotification" checked>
                                                <label class="form-check-label" for="newCompanyNotification">
                                                    New Company Registration
                                                </label>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" name="payment_failed_notification" id="paymentFailedNotification" checked>
                                                <label class="form-check-label" for="paymentFailedNotification">
                                                    Payment Failures
                                                </label>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" name="system_error_notification" id="systemErrorNotification" checked>
                                                <label class="form-check-label" for="systemErrorNotification">
                                                    System Errors
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" name="module_published_notification" id="modulePublishedNotification" checked>
                                                <label class="form-check-label" for="modulePublishedNotification">
                                                    Module Publications
                                                </label>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" name="high_usage_notification" id="highUsageNotification" checked>
                                                <label class="form-check-label" for="highUsageNotification">
                                                    High Usage Alerts
                                                </label>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" name="security_notification" id="securityNotification" checked>
                                                <label class="form-check-label" for="securityNotification">
                                                    Security Alerts
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6>Delivery Methods</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" name="email_notifications" id="emailNotifications" checked>
                                                <label class="form-check-label" for="emailNotifications">
                                                    Email Notifications
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" name="slack_notifications" id="slackNotifications">
                                                <label class="form-check-label" for="slackNotifications">
                                                    Slack Notifications
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" name="sms_notifications" id="smsNotifications">
                                                <label class="form-check-label" for="smsNotifications">
                                                    SMS Notifications
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <h6>Slack Integration</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Slack Webhook URL</label>
                                        <input type="url" class="form-control" name="slack_webhook_url" placeholder="https://hooks.slack.com/services/...">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Default Channel</label>
                                        <input type="text" class="form-control" name="slack_channel" placeholder="#alerts" value="#alerts">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Maintenance -->
                <div class="tab-pane fade" id="maintenance" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Maintenance & System Tools</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="maintenance-tool">
                                        <h6><i class="fas fa-database text-primary"></i> Database</h6>
                                        <p class="text-muted">Database maintenance and optimization tools</p>
                                        <div class="btn-group w-100">
                                            <button class="btn btn-outline-primary" onclick="optimizeDatabase()">
                                                <i class="fas fa-cog"></i> Optimize
                                            </button>
                                            <button class="btn btn-outline-info" onclick="backupDatabase()">
                                                <i class="fas fa-download"></i> Backup
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="maintenance-tool">
                                        <h6><i class="fas fa-trash text-warning"></i> Cache</h6>
                                        <p class="text-muted">Clear application cache and temporary files</p>
                                        <div class="btn-group w-100">
                                            <button class="btn btn-outline-warning" onclick="clearCache()">
                                                <i class="fas fa-trash"></i> Clear Cache
                                            </button>
                                            <button class="btn btn-outline-secondary" onclick="clearLogs()">
                                                <i class="fas fa-eraser"></i> Clear Logs
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="maintenance-tool">
                                        <h6><i class="fas fa-tools text-success"></i> Maintenance Mode</h6>
                                        <p class="text-muted">Enable maintenance mode for system updates</p>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenanceMode">
                                            <label class="form-check-label" for="maintenanceMode">
                                                Enable Maintenance Mode
                                            </label>
                                        </div>
                                        <textarea class="form-control" name="maintenance_message" rows="2" placeholder="We're performing scheduled maintenance..."></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="maintenance-tool">
                                        <h6><i class="fas fa-shield-alt text-danger"></i> System Health</h6>
                                        <p class="text-muted">Check system health and run diagnostics</p>
                                        <button class="btn btn-outline-info w-100 mb-2" onclick="runHealthCheck()">
                                            <i class="fas fa-heartbeat"></i> Run Health Check
                                        </button>
                                        <button class="btn btn-outline-secondary w-100" onclick="runDiagnostics()">
                                            <i class="fas fa-stethoscope"></i> Run Diagnostics
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.settings-nav {
    position: sticky;
    top: 20px;
}

.settings-nav .nav-link {
    color: #5a5c69;
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    margin-bottom: 0.25rem;
    text-align: left;
    transition: all 0.3s ease;
}

.settings-nav .nav-link:hover {
    background: #f8f9fc;
    color: #5a5c69;
}

.settings-nav .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.settings-nav .nav-link i {
    width: 20px;
    margin-right: 10px;
}

.card {
    border: 1px solid #e3e6f0;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.card-header {
    background: white;
    border-bottom: 1px solid #e3e6f0;
}

.form-switch .form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

.maintenance-tool {
    background: #f8f9fc;
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    text-align: center;
}

.maintenance-tool h6 {
    color: #5a5c69;
    margin-bottom: 0.5rem;
}

.maintenance-tool p {
    font-size: 0.875rem;
    margin-bottom: 1rem;
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

code {
    background: #f8f9fc;
    color: #6c757d;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.875rem;
}
</style>

<script>
// Settings management
function saveAllSettings() {
    const forms = [
        'generalSettingsForm',
        'emailSettingsForm', 
        'paymentSettingsForm',
        'securitySettingsForm',
        'apiSettingsForm',
        'notificationSettingsForm'
    ];
    
    const allData = new FormData();
    allData.append('action', 'save_all_settings');
    
    forms.forEach(formId => {
        const form = document.getElementById(formId);
        if (form) {
            const formData = new FormData(form);
            for (let [key, value] of formData.entries()) {
                allData.append(key, value);
            }
        }
    });
    
    // Add maintenance settings
    const maintenanceMode = document.getElementById('maintenanceMode');
    const maintenanceMessage = document.querySelector('textarea[name="maintenance_message"]');
    if (maintenanceMode) allData.append('maintenance_mode', maintenanceMode.checked);
    if (maintenanceMessage) allData.append('maintenance_message', maintenanceMessage.value);
    
    fetch('/platform/ajax/settings.php', {
        method: 'POST',
        body: allData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Settings saved successfully!', 'success');
        } else {
            showNotification(data.message || 'Error saving settings', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error saving settings', 'error');
    });
}

// Email testing
function testEmailConnection() {
    const emailForm = document.getElementById('emailSettingsForm');
    const formData = new FormData(emailForm);
    formData.append('action', 'test_email');
    
    fetch('/platform/ajax/settings.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Email connection successful!', 'success');
        } else {
            showNotification(data.message || 'Email connection failed', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error testing email connection', 'error');
    });
}

// API management
function generateApiKey() {
    const keyName = prompt('Enter a name for this API key:');
    if (!keyName) return;
    
    fetch('/platform/ajax/settings.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=generate_api_key&key_name=${encodeURIComponent(keyName)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('API key generated successfully!', 'success');
            // Refresh the API keys table
            location.reload();
        } else {
            showNotification(data.message || 'Error generating API key', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error generating API key', 'error');
    });
}

function revokeApiKey(keyId) {
    if (confirm('Are you sure you want to revoke this API key? This action cannot be undone.')) {
        fetch('/platform/ajax/settings.php', {
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

function generateWebhookSecret() {
    const secretInput = document.querySelector('input[name="webhook_secret"]');
    const secret = 'whsec_' + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
    secretInput.value = secret;
}

// Maintenance functions
function optimizeDatabase() {
    if (confirm('This will optimize the database. Continue?')) {
        fetch('/platform/ajax/settings.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=optimize_database'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Database optimized successfully!', 'success');
            } else {
                showNotification(data.message || 'Error optimizing database', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error optimizing database', 'error');
        });
    }
}

function backupDatabase() {
    fetch('/platform/ajax/settings.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=backup_database'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Database backup created!', 'success');
            if (data.download_url) {
                window.location.href = data.download_url;
            }
        } else {
            showNotification(data.message || 'Error creating backup', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error creating backup', 'error');
    });
}

function clearCache() {
    if (confirm('Clear all application cache?')) {
        fetch('/platform/ajax/settings.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=clear_cache'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Cache cleared successfully!', 'success');
            } else {
                showNotification(data.message || 'Error clearing cache', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error clearing cache', 'error');
        });
    }
}

function clearLogs() {
    if (confirm('Clear all log files? This cannot be undone.')) {
        fetch('/platform/ajax/settings.php', {
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

function runHealthCheck() {
    fetch('/platform/ajax/settings.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=health_check'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('System health check completed!', 'success');
            // Show detailed results in a modal or alert
            alert(`Health Check Results:\n${data.results}`);
        } else {
            showNotification(data.message || 'Health check failed', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error running health check', 'error');
    });
}

function runDiagnostics() {
    fetch('/platform/ajax/settings.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=run_diagnostics'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Diagnostics completed!', 'success');
            // Show detailed results
            alert(`Diagnostics Results:\n${data.results}`);
        } else {
            showNotification(data.message || 'Diagnostics failed', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error running diagnostics', 'error');
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
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>