<?php
// platform/pages/backup.php
require_once __DIR__ . '/../../config/config.php';

// Check admin authentication
if (!isset($_SESSION['platform_admin_id'])) {
    header('Location: /platform/auth/login');
    exit;
}

$page_title = 'Backup & Restore';
include __DIR__ . '/../layout/header.php';
?>

<div class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">
                    <i class="fas fa-download text-primary"></i>
                    Backup & Restore
                </h1>
                <p class="page-subtitle">Secure your platform data with automated backups</p>
            </div>
            <div class="col-auto">
                <div class="btn-group">
                    <button class="btn btn-outline-primary" onclick="refreshBackupsList()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button class="btn btn-primary" onclick="createBackup()">
                        <i class="fas fa-plus"></i> Create Backup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup Overview -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="backup-stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">24</div>
                    <div class="stat-label">Total Backups</div>
                    <div class="stat-change">Last: 2 hours ago</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="backup-stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-database"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">2.4 GB</div>
                    <div class="stat-label">Total Size</div>
                    <div class="stat-change">+150 MB this week</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="backup-stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">Daily</div>
                    <div class="stat-label">Auto Backup</div>
                    <div class="stat-change">Next: in 6 hours</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="backup-stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-cloud"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">AWS S3</div>
                    <div class="stat-label">Storage</div>
                    <div class="stat-change">Connected</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup Tabs -->
    <div class="row">
        <div class="col-lg-3">
            <div class="backup-nav">
                <div class="nav flex-column nav-pills" id="backupTab" role="tablist">
                    <button class="nav-link active" id="backups-tab" data-bs-toggle="pill" data-bs-target="#backups" type="button" role="tab">
                        <i class="fas fa-download"></i> Backups
                    </button>
                    <button class="nav-link" id="schedule-tab" data-bs-toggle="pill" data-bs-target="#schedule" type="button" role="tab">
                        <i class="fas fa-calendar-alt"></i> Schedule
                    </button>
                    <button class="nav-link" id="storage-tab" data-bs-toggle="pill" data-bs-target="#storage" type="button" role="tab">
                        <i class="fas fa-cloud"></i> Storage Settings
                    </button>
                    <button class="nav-link" id="restore-tab" data-bs-toggle="pill" data-bs-target="#restore" type="button" role="tab">
                        <i class="fas fa-upload"></i> Restore
                    </button>
                    <button class="nav-link" id="monitoring-tab" data-bs-toggle="pill" data-bs-target="#monitoring" type="button" role="tab">
                        <i class="fas fa-chart-line"></i> Monitoring
                    </button>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <div class="tab-content" id="backupTabContent">
                <!-- Backups List Tab -->
                <div class="tab-pane fade show active" id="backups" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="card-title mb-0">Available Backups</h5>
                                </div>
                                <div class="col-auto">
                                    <div class="btn-group">
                                        <select class="form-select form-select-sm me-2" id="backupFilter">
                                            <option value="">All Backups</option>
                                            <option value="manual">Manual</option>
                                            <option value="automatic">Automatic</option>
                                            <option value="scheduled">Scheduled</option>
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" onclick="cleanupOldBackups()">
                                            <i class="fas fa-trash"></i> Cleanup
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="backupsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40">
                                                <input type="checkbox" class="form-check-input" id="selectAllBackups">
                                            </th>
                                            <th>Backup Name</th>
                                            <th>Type</th>
                                            <th>Size</th>
                                            <th>Created</th>
                                            <th>Status</th>
                                            <th>Location</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input backup-checkbox" value="backup_001">
                                            </td>
                                            <td>
                                                <div class="backup-info">
                                                    <strong>platform_backup_20250128_154230</strong>
                                                    <small class="d-block text-muted">Full database + files</small>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-primary">Automatic</span></td>
                                            <td>
                                                <span class="backup-size">142.5 MB</span>
                                                <div class="backup-progress">
                                                    <div class="progress-bar bg-success" style="width: 100%"></div>
                                                </div>
                                            </td>
                                            <td>
                                                <span title="2025-01-28 15:42:30">2 hours ago</span>
                                            </td>
                                            <td><span class="status-badge status-completed">Completed</span></td>
                                            <td>
                                                <i class="fas fa-cloud text-primary"></i>
                                                AWS S3
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="downloadBackup('backup_001')" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-info" onclick="validateBackup('backup_001')" title="Validate">
                                                        <i class="fas fa-check-circle"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-success" onclick="restoreBackup('backup_001')" title="Restore">
                                                        <i class="fas fa-upload"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteBackup('backup_001')" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input backup-checkbox" value="backup_002">
                                            </td>
                                            <td>
                                                <div class="backup-info">
                                                    <strong>manual_backup_before_update</strong>
                                                    <small class="d-block text-muted">Pre-update safety backup</small>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-success">Manual</span></td>
                                            <td>
                                                <span class="backup-size">138.2 MB</span>
                                                <div class="backup-progress">
                                                    <div class="progress-bar bg-success" style="width: 100%"></div>
                                                </div>
                                            </td>
                                            <td>
                                                <span title="2025-01-27 09:15:45">1 day ago</span>
                                            </td>
                                            <td><span class="status-badge status-completed">Completed</span></td>
                                            <td>
                                                <i class="fas fa-server text-secondary"></i>
                                                Local
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="downloadBackup('backup_002')" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-info" onclick="validateBackup('backup_002')" title="Validate">
                                                        <i class="fas fa-check-circle"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-success" onclick="restoreBackup('backup_002')" title="Restore">
                                                        <i class="fas fa-upload"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteBackup('backup_002')" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input backup-checkbox" value="backup_003">
                                            </td>
                                            <td>
                                                <div class="backup-info">
                                                    <strong>platform_backup_20250128_120000</strong>
                                                    <small class="d-block text-muted">Scheduled daily backup</small>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-warning text-dark">In Progress</span></td>
                                            <td>
                                                <span class="backup-size">~140 MB</span>
                                                <div class="backup-progress">
                                                    <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" style="width: 65%"></div>
                                                </div>
                                            </td>
                                            <td>
                                                <span title="2025-01-28 12:00:00">Started 6 hours ago</span>
                                            </td>
                                            <td><span class="status-badge status-processing">In Progress (65%)</span></td>
                                            <td>
                                                <i class="fas fa-cloud text-primary"></i>
                                                AWS S3
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="btn btn-sm btn-outline-warning" onclick="cancelBackup('backup_003')" title="Cancel">
                                                        <i class="fas fa-stop"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-info" onclick="viewBackupLog('backup_003')" title="View Log">
                                                        <i class="fas fa-list"></i>
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
                                    <small class="text-muted">Total: 24 backups (2.4 GB)</small>
                                </div>
                                <div class="col-auto">
                                    <div class="bulk-actions">
                                        <select class="form-select form-select-sm" id="bulkBackupAction">
                                            <option value="">Bulk Actions</option>
                                            <option value="download">Download Selected</option>
                                            <option value="validate">Validate Selected</option>
                                            <option value="delete">Delete Selected</option>
                                        </select>
                                        <button class="btn btn-sm btn-outline-primary" onclick="executeBulkBackupAction()">Apply</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Schedule Tab -->
                <div class="tab-pane fade" id="schedule" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Backup Schedule Configuration</h5>
                        </div>
                        <div class="card-body">
                            <form id="scheduleForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="auto_backup_enabled" id="autoBackupEnabled" checked>
                                                <label class="form-check-label" for="autoBackupEnabled">
                                                    <strong>Enable Automatic Backups</strong>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Backup Frequency</label>
                                            <select class="form-select" name="backup_frequency">
                                                <option value="hourly">Every Hour</option>
                                                <option value="daily" selected>Daily</option>
                                                <option value="weekly">Weekly</option>
                                                <option value="monthly">Monthly</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Backup Time</label>
                                            <input type="time" class="form-control" name="backup_time" value="02:00">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Retention Period (days)</label>
                                            <input type="number" class="form-control" name="retention_days" value="30" min="1" max="365">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6>Backup Types</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="backup_types[]" value="database" id="backupDatabase" checked>
                                                <label class="form-check-label" for="backupDatabase">
                                                    Database
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="backup_types[]" value="files" id="backupFiles" checked>
                                                <label class="form-check-label" for="backupFiles">
                                                    Application Files
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="backup_types[]" value="uploads" id="backupUploads" checked>
                                                <label class="form-check-label" for="backupUploads">
                                                    User Uploads
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="backup_types[]" value="logs" id="backupLogs">
                                                <label class="form-check-label" for="backupLogs">
                                                    System Logs
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6>Notification Settings</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="notify_success" id="notifySuccess" checked>
                                                <label class="form-check-label" for="notifySuccess">
                                                    Notify on successful backup
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="notify_failure" id="notifyFailure" checked>
                                                <label class="form-check-label" for="notifyFailure">
                                                    Notify on backup failure
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <label class="form-label">Notification Email</label>
                                        <input type="email" class="form-control" name="notification_email" value="admin@brcload.com">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Schedule
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Next Scheduled Backups</h6>
                        </div>
                        <div class="card-body">
                            <div class="schedule-timeline">
                                <div class="schedule-item">
                                    <div class="schedule-time">
                                        <strong>Today 02:00</strong>
                                        <small class="d-block text-muted">in 6 hours</small>
                                    </div>
                                    <div class="schedule-details">
                                        <div class="schedule-type">Daily Automatic Backup</div>
                                        <div class="schedule-desc">Database + Files + Uploads</div>
                                    </div>
                                    <div class="schedule-actions">
                                        <button class="btn btn-sm btn-outline-primary" onclick="runScheduledBackupNow('daily')">
                                            <i class="fas fa-play"></i> Run Now
                                        </button>
                                    </div>
                                </div>
                                <div class="schedule-item">
                                    <div class="schedule-time">
                                        <strong>Sunday 02:00</strong>
                                        <small class="d-block text-muted">in 3 days</small>
                                    </div>
                                    <div class="schedule-details">
                                        <div class="schedule-type">Weekly Full Backup</div>
                                        <div class="schedule-desc">Complete system backup</div>
                                    </div>
                                    <div class="schedule-actions">
                                        <button class="btn btn-sm btn-outline-primary" onclick="runScheduledBackupNow('weekly')">
                                            <i class="fas fa-play"></i> Run Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Storage Settings Tab -->
                <div class="tab-pane fade" id="storage" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Storage Configuration</h5>
                        </div>
                        <div class="card-body">
                            <form id="storageForm">
                                <div class="mb-4">
                                    <h6>Primary Storage Location</h6>
                                    <div class="storage-options">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="primary_storage" id="storageLocal" value="local">
                                            <label class="form-check-label" for="storageLocal">
                                                <strong>Local Server</strong>
                                                <small class="d-block text-muted">Store backups on the local server</small>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="primary_storage" id="storageS3" value="s3" checked>
                                            <label class="form-check-label" for="storageS3">
                                                <strong>Amazon S3</strong>
                                                <small class="d-block text-muted">Store backups in AWS S3 bucket</small>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="primary_storage" id="storageFTP" value="ftp">
                                            <label class="form-check-label" for="storageFTP">
                                                <strong>FTP Server</strong>
                                                <small class="d-block text-muted">Store backups on remote FTP server</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- AWS S3 Configuration -->
                                <div id="s3Config" class="storage-config">
                                    <h6>AWS S3 Configuration</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Access Key ID</label>
                                                <input type="text" class="form-control" name="s3_access_key" value="AKIA...">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Secret Access Key</label>
                                                <input type="password" class="form-control" name="s3_secret_key" placeholder="••••••••">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Bucket Name</label>
                                                <input type="text" class="form-control" name="s3_bucket" value="brc-platform-backups">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Region</label>
                                                <select class="form-select" name="s3_region">
                                                    <option value="us-east-1" selected>US East (N. Virginia)</option>
                                                    <option value="us-west-2">US West (Oregon)</option>
                                                    <option value="eu-west-1">Europe (Ireland)</option>
                                                    <option value="ap-southeast-1">Asia Pacific (Singapore)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-outline-primary" onclick="testS3Connection()">
                                            <i class="fas fa-plug"></i> Test Connection
                                        </button>
                                    </div>
                                </div>

                                <!-- FTP Configuration -->
                                <div id="ftpConfig" class="storage-config" style="display: none;">
                                    <h6>FTP Server Configuration</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">FTP Host</label>
                                                <input type="text" class="form-control" name="ftp_host">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">FTP Port</label>
                                                <input type="number" class="form-control" name="ftp_port" value="21">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Username</label>
                                                <input type="text" class="form-control" name="ftp_username">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Password</label>
                                                <input type="password" class="form-control" name="ftp_password">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Remote Directory</label>
                                        <input type="text" class="form-control" name="ftp_directory" value="/backups">
                                    </div>
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-outline-primary" onclick="testFTPConnection()">
                                            <i class="fas fa-plug"></i> Test Connection
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6>Backup Encryption</h6>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="encrypt_backups" id="encryptBackups" checked>
                                        <label class="form-check-label" for="encryptBackups">
                                            Enable backup encryption (AES-256)
                                        </label>
                                    </div>
                                    <div class="mt-3">
                                        <label class="form-label">Encryption Password</label>
                                        <input type="password" class="form-control" name="encryption_password" placeholder="••••••••">
                                        <div class="form-text">Leave empty to use default platform encryption key</div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Storage Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Restore Tab -->
                <div class="tab-pane fade" id="restore" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Restore System</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Warning:</strong> System restore will overwrite current data. Make sure to create a backup before proceeding.
                            </div>
                            
                            <form id="restoreForm">
                                <div class="mb-4">
                                    <h6>Restore Source</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="restore_source" id="restoreFromBackup" value="backup" checked>
                                        <label class="form-check-label" for="restoreFromBackup">
                                            <strong>From Existing Backup</strong>
                                            <small class="d-block text-muted">Select from available backups</small>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="restore_source" id="restoreFromFile" value="file">
                                        <label class="form-check-label" for="restoreFromFile">
                                            <strong>Upload Backup File</strong>
                                            <small class="d-block text-muted">Upload a backup file from your computer</small>
                                        </label>
                                    </div>
                                </div>

                                <div id="backupSelection" class="restore-option">
                                    <div class="mb-3">
                                        <label class="form-label">Select Backup</label>
                                        <select class="form-select" name="backup_id">
                                            <option value="">Choose backup to restore...</option>
                                            <option value="backup_001">platform_backup_20250128_154230 (142.5 MB)</option>
                                            <option value="backup_002">manual_backup_before_update (138.2 MB)</option>
                                        </select>
                                    </div>
                                </div>

                                <div id="fileUpload" class="restore-option" style="display: none;">
                                    <div class="mb-3">
                                        <label class="form-label">Backup File</label>
                                        <input type="file" class="form-control" name="backup_file" accept=".zip,.sql,.tar.gz">
                                        <div class="form-text">Supported formats: .zip, .sql, .tar.gz</div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6>Restore Components</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="restore_components[]" value="database" id="restoreDatabase" checked>
                                                <label class="form-check-label" for="restoreDatabase">
                                                    Database
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="restore_components[]" value="files" id="restoreFiles">
                                                <label class="form-check-label" for="restoreFiles">
                                                    Application Files
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="restore_components[]" value="uploads" id="restoreUploads">
                                                <label class="form-check-label" for="restoreUploads">
                                                    User Uploads
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="restore_components[]" value="config" id="restoreConfig">
                                                <label class="form-check-label" for="restoreConfig">
                                                    Configuration Files
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="create_pre_restore_backup" id="createPreRestoreBackup" checked>
                                        <label class="form-check-label" for="createPreRestoreBackup">
                                            <strong>Create backup before restore</strong>
                                            <small class="d-block text-muted">Recommended: Create a backup of current state before restoring</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-outline-secondary" onclick="validateRestoreBackup()">
                                        <i class="fas fa-check-circle"></i> Validate Backup
                                    </button>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-upload"></i> Start Restore
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Monitoring Tab -->
                <div class="tab-pane fade" id="monitoring" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Backup Success Rate</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="backupSuccessChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Storage Usage</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="storageUsageChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Recent Backup Activity</h6>
                        </div>
                        <div class="card-body">
                            <div class="backup-activity-timeline">
                                <div class="activity-item">
                                    <div class="activity-icon bg-success">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">Daily backup completed</div>
                                        <div class="activity-desc">platform_backup_20250128_154230 (142.5 MB)</div>
                                        <div class="activity-time">2 hours ago</div>
                                    </div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-icon bg-info">
                                        <i class="fas fa-upload"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">Backup uploaded to S3</div>
                                        <div class="activity-desc">Upload completed in 45 seconds</div>
                                        <div class="activity-time">2 hours ago</div>
                                    </div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-icon bg-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">Backup validation warning</div>
                                        <div class="activity-desc">Manual backup file seems corrupted</div>
                                        <div class="activity-time">1 day ago</div>
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
.backup-stat-card {
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

.backup-nav {
    position: sticky;
    top: 20px;
}

.backup-nav .nav-link {
    color: #5a5c69;
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    margin-bottom: 0.25rem;
    text-align: left;
    transition: all 0.3s ease;
}

.backup-nav .nav-link:hover {
    background: #f8f9fc;
    color: #5a5c69;
}

.backup-nav .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.backup-nav .nav-link i {
    width: 20px;
    margin-right: 10px;
}

.backup-info strong {
    color: #5a5c69;
    font-weight: 600;
}

.backup-size {
    font-weight: 600;
    color: #5a5c69;
}

.backup-progress {
    width: 100%;
    height: 4px;
    background: #e9ecef;
    border-radius: 2px;
    overflow: hidden;
    margin-top: 4px;
}

.progress-bar {
    height: 100%;
    transition: width 0.6s ease;
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

.action-buttons {
    display: flex;
    gap: 4px;
}

.bulk-actions {
    display: flex;
    gap: 8px;
    align-items: center;
}

.storage-options .form-check {
    background: #f8f9fc;
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.storage-config {
    background: #f8f9fc;
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1rem;
}

.schedule-timeline, .backup-activity-timeline {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.schedule-item, .activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fc;
    border: 1px solid #e3e6f0;
    border-radius: 8px;
}

.schedule-time {
    min-width: 120px;
}

.schedule-details, .activity-content {
    flex: 1;
}

.schedule-type, .activity-title {
    font-weight: 600;
    color: #5a5c69;
}

.schedule-desc, .activity-desc {
    color: #6c757d;
    font-size: 0.875rem;
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

.activity-time {
    color: #6c757d;
    font-size: 0.875rem;
}

.restore-option {
    background: #f8f9fc;
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
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
document.addEventListener('DOMContentLoaded', function() {
    initBackupCharts();
    setupStorageToggle();
    setupRestoreToggle();
});

function initBackupCharts() {
    // Backup Success Rate Chart
    const successCtx = document.getElementById('backupSuccessChart').getContext('2d');
    new Chart(successCtx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'Success Rate %',
                data: [98, 100, 96, 100],
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { 
                y: { 
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });

    // Storage Usage Chart
    const storageCtx = document.getElementById('storageUsageChart').getContext('2d');
    new Chart(storageCtx, {
        type: 'doughnut',
        data: {
            labels: ['Used (2.4 GB)', 'Available (7.6 GB)'],
            datasets: [{
                data: [2.4, 7.6],
                backgroundColor: ['#007bff', '#e9ecef'],
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

function setupStorageToggle() {
    document.querySelectorAll('input[name="primary_storage"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.storage-config').forEach(config => {
                config.style.display = 'none';
            });
            
            if (this.value === 's3') {
                document.getElementById('s3Config').style.display = 'block';
            } else if (this.value === 'ftp') {
                document.getElementById('ftpConfig').style.display = 'block';
            }
        });
    });
}

function setupRestoreToggle() {
    document.querySelectorAll('input[name="restore_source"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.restore-option').forEach(option => {
                option.style.display = 'none';
            });
            
            if (this.value === 'backup') {
                document.getElementById('backupSelection').style.display = 'block';
            } else if (this.value === 'file') {
                document.getElementById('fileUpload').style.display = 'block';
            }
        });
    });
}

// Backup management functions
function createBackup() {
    if (confirm('Create a new backup now? This may take several minutes.')) {
        fetch('/platform/ajax/backup.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=create_backup'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Backup creation started!', 'success');
                refreshBackupsList();
            } else {
                showNotification(data.message || 'Error creating backup', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error creating backup', 'error');
        });
    }
}

function downloadBackup(backupId) {
    window.location.href = `/platform/ajax/backup.php?action=download&id=${backupId}`;
}

function validateBackup(backupId) {
    fetch('/platform/ajax/backup.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=validate_backup&id=${backupId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Backup validation completed successfully!', 'success');
        } else {
            showNotification(data.message || 'Backup validation failed', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error validating backup', 'error');
    });
}

function restoreBackup(backupId) {
    if (confirm('Are you sure you want to restore this backup? This will overwrite current data.')) {
        fetch('/platform/ajax/backup.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=restore_backup&id=${backupId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Restore started! You will be logged out when complete.', 'success');
            } else {
                showNotification(data.message || 'Error starting restore', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error starting restore', 'error');
        });
    }
}

function deleteBackup(backupId) {
    if (confirm('Delete this backup? This action cannot be undone.')) {
        fetch('/platform/ajax/backup.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete_backup&id=${backupId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Backup deleted successfully!', 'success');
                document.querySelector(`input[value="${backupId}"]`).closest('tr').remove();
            } else {
                showNotification(data.message || 'Error deleting backup', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error deleting backup', 'error');
        });
    }
}

function cancelBackup(backupId) {
    if (confirm('Cancel this backup operation?')) {
        fetch('/platform/ajax/backup.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=cancel_backup&id=${backupId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Backup cancelled!', 'success');
                refreshBackupsList();
            } else {
                showNotification(data.message || 'Error cancelling backup', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error cancelling backup', 'error');
        });
    }
}

function viewBackupLog(backupId) {
    window.open(`/platform/backups/${backupId}/log`, '_blank');
}

function cleanupOldBackups() {
    if (confirm('Remove backups older than retention period?')) {
        fetch('/platform/ajax/backup.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=cleanup_old_backups'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(`Cleaned up ${data.removed_count} old backups!`, 'success');
                refreshBackupsList();
            } else {
                showNotification(data.message || 'Error during cleanup', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error during cleanup', 'error');
        });
    }
}

function refreshBackupsList() {
    location.reload();
}

// Storage connection tests
function testS3Connection() {
    const formData = new FormData(document.getElementById('storageForm'));
    formData.append('action', 'test_s3_connection');
    
    fetch('/platform/ajax/backup.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('S3 connection successful!', 'success');
        } else {
            showNotification(data.message || 'S3 connection failed', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error testing S3 connection', 'error');
    });
}

function testFTPConnection() {
    const formData = new FormData(document.getElementById('storageForm'));
    formData.append('action', 'test_ftp_connection');
    
    fetch('/platform/ajax/backup.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('FTP connection successful!', 'success');
        } else {
            showNotification(data.message || 'FTP connection failed', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error testing FTP connection', 'error');
    });
}

// Schedule functions
function runScheduledBackupNow(type) {
    if (confirm(`Run ${type} backup now?`)) {
        fetch('/platform/ajax/backup.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=run_scheduled_backup&type=${type}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Scheduled backup started!', 'success');
            } else {
                showNotification(data.message || 'Error starting backup', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error starting backup', 'error');
        });
    }
}

// Restore functions
function validateRestoreBackup() {
    const formData = new FormData(document.getElementById('restoreForm'));
    formData.append('action', 'validate_restore_backup');
    
    fetch('/platform/ajax/backup.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Backup validation successful!', 'success');
        } else {
            showNotification(data.message || 'Backup validation failed', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error validating backup', 'error');
    });
}

// Form handlers
document.getElementById('scheduleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'save_schedule');
    
    fetch('/platform/ajax/backup.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Backup schedule saved successfully!', 'success');
        } else {
            showNotification(data.message || 'Error saving schedule', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error saving schedule', 'error');
    });
});

document.getElementById('storageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'save_storage_settings');
    
    fetch('/platform/ajax/backup.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Storage settings saved successfully!', 'success');
        } else {
            showNotification(data.message || 'Error saving storage settings', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error saving storage settings', 'error');
    });
});

document.getElementById('restoreForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!confirm('Are you absolutely sure you want to restore? This will overwrite all current data.')) {
        return;
    }
    
    const formData = new FormData(this);
    formData.append('action', 'start_restore');
    
    fetch('/platform/ajax/backup.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('System restore started! You will be logged out when complete.', 'success');
        } else {
            showNotification(data.message || 'Error starting restore', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error starting restore', 'error');
    });
});

// Bulk actions
function executeBulkBackupAction() {
    const action = document.getElementById('bulkBackupAction').value;
    const selectedIds = Array.from(document.querySelectorAll('.backup-checkbox:checked')).map(cb => cb.value);
    
    if (!action || selectedIds.length === 0) {
        showNotification('Please select an action and backups', 'warning');
        return;
    }
    
    if (confirm(`${action} ${selectedIds.length} backups?`)) {
        fetch('/platform/ajax/backup.php', {
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
                    data.downloadUrl && (window.location.href = data.downloadUrl);
                } else {
                    refreshBackupsList();
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
document.getElementById('selectAllBackups').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.backup-checkbox');
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
</script>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php include __DIR__ . '/../layout/footer.php'; ?>