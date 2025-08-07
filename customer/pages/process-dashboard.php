<?php
// ===== BRC PROCESS DASHBOARD =====
// File: customer/pages/process-dashboard.php
// Description: Real-time process monitoring dashboard

// Session and auth check
session_start();
if (!isset($_SESSION['company_id'])) {
    header('Location: /brcproject/customer/auth/login.php');
    exit;
}

$company_id = $_SESSION['company_id'];
$user_id = $_SESSION['user_id'] ?? null;
$company_name = $_SESSION['company_name'] ?? 'Unknown Company';

// Page title
$pageTitle = 'Process Dashboard';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - BRC Load Platform</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
        }
        
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #764ba2 100%);
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
            border-left: 4px solid transparent;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        }
        
        .stat-card.overdue {
            border-left-color: var(--danger-color);
        }
        
        .stat-card.due-soon {
            border-left-color: var(--warning-color);
        }
        
        .stat-card.in-progress {
            border-left-color: var(--info-color);
        }
        
        .stat-card.completed {
            border-left-color: var(--success-color);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.95rem;
            font-weight: 500;
        }
        
        .stat-icon {
            font-size: 2rem;
            opacity: 0.3;
            float: right;
            margin-top: -1rem;
        }
        
        .process-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.2s;
        }
        
        .process-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transform: translateY(-1px);
        }
        
        .process-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .process-title {
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        
        .process-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-in-progress {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-overdue {
            background: #f8d7da;
            color: #721c24;
        }
        
        .progress-container {
            margin: 1rem 0;
        }
        
        .progress {
            height: 8px;
            border-radius: 4px;
        }
        
        .task-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            margin: 0.5rem 0;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 3px solid transparent;
        }
        
        .task-item.overdue {
            border-left-color: var(--danger-color);
            background: #ffeaea;
        }
        
        .task-item.due-soon {
            border-left-color: var(--warning-color);
            background: #fff9e6;
        }
        
        .task-info {
            flex: 1;
        }
        
        .task-name {
            font-weight: 500;
            color: #333;
            margin-bottom: 0.25rem;
        }
        
        .task-meta {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .task-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.8rem;
        }
        
        .refresh-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            transition: all 0.2s;
        }
        
        .refresh-btn:hover {
            transform: scale(1.1);
            background: #5a6fd8;
        }
        
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }
        
        @media (max-width: 768px) {
            .process-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .task-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .task-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-md-end">
                    <!-- Notification Bell -->
                    <div class="dropdown d-inline-block me-2">
                        <button class="btn btn-outline-light position-relative" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notification-count" style="display: none;">
                                0
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" style="width: 350px; max-height: 400px; overflow-y: auto;">
                            <li><h6 class="dropdown-header">Notifications</h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <div id="notifications-list">
                                <li><span class="dropdown-item-text text-muted">Loading notifications...</span></li>
                            </div>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center" href="#" onclick="markAllRead()">Mark all as read</a></li>
                        </ul>
                    </div>
                    
                    <button class="btn btn-light btn-sm me-2" onclick="refreshDashboard()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button class="btn btn-outline-light btn-sm" onclick="showCreateProcess()">
                        <i class="fas fa-plus"></i> New Process
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Statistics Cards -->
        <div class="row mb-4" id="statsContainer">
            <div class="col-md-3 mb-3">
                <div class="stat-card overdue">
                    <div class="stat-number text-danger" id="overdueCount">-</div>
                    <div class="stat-label">Overdue Tasks</div>
                    <i class="fas fa-exclamation-triangle stat-icon text-danger"></i>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card due-soon">
                    <div class="stat-number text-warning" id="dueSoonCount">-</div>
                    <div class="stat-label">Due Soon</div>
                    <i class="fas fa-clock stat-icon text-warning"></i>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card in-progress">
                    <div class="stat-number text-info" id="inProgressCount">-</div>
                    <div class="stat-label">In Progress</div>
                    <i class="fas fa-play-circle stat-icon text-info"></i>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card completed">
                    <div class="stat-number text-success" id="completedTodayCount">-</div>
                    <div class="stat-label">Completed Today</div>
                    <i class="fas fa-check-circle stat-icon text-success"></i>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- My Tasks -->
            <div class="col-lg-8 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4><i class="fas fa-tasks me-2"></i>My Tasks</h4>
                    <div class="btn-group btn-group-sm" role="group">
                        <input type="radio" class="btn-check" name="taskFilter" id="allTasks" autocomplete="off" checked>
                        <label class="btn btn-outline-primary" for="allTasks">All</label>
                        
                        <input type="radio" class="btn-check" name="taskFilter" id="urgentTasks" autocomplete="off">
                        <label class="btn btn-outline-danger" for="urgentTasks">Urgent</label>
                        
                        <input type="radio" class="btn-check" name="taskFilter" id="todayTasks" autocomplete="off">
                        <label class="btn btn-outline-info" for="todayTasks">Today</label>
                    </div>
                </div>
                <div id="tasksContainer">
                    <div class="empty-state">
                        <i class="fas fa-spinner fa-spin"></i>
                        <h5>Loading tasks...</h5>
                    </div>
                </div>
            </div>

            <!-- Active Processes -->
            <div class="col-lg-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4><i class="fas fa-project-diagram me-2"></i>Active Processes</h4>
                </div>
                <div id="processesContainer">
                    <div class="empty-state">
                        <i class="fas fa-spinner fa-spin"></i>
                        <h5>Loading processes...</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Refresh Button -->
    <button class="refresh-btn" onclick="refreshDashboard()" title="Refresh Dashboard">
        <i class="fas fa-sync-alt"></i>
    </button>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // ===== PROCESS DASHBOARD JAVASCRIPT =====
        
        const COMPANY_ID = <?php echo $company_id; ?>;
        const USER_ID = <?php echo $user_id ?? 'null'; ?>;
        const API_BASE = '/brcproject/platform/ajax/process-management.php';
        
        let dashboardData = {};
        let tasksData = [];
        let processesData = [];
        let refreshInterval;

        let notificationInterval;
        
        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Initializing Process Dashboard');
            loadDashboardData();
            startAutoRefresh();
            setupEventListeners();
            startNotificationPolling();
        });
        
        // API call function
        async function apiCall(action, data = {}) {
            try {
                const response = await fetch(API_BASE, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action, company_id: COMPANY_ID, user_id: USER_ID, ...data })
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                return await response.json();
            } catch (error) {
                console.error('API Error:', error);
                showNotification('API call failed: ' + error.message, 'danger');
                return { success: false, error: error.message };
            }
        }
        
        // Load dashboard data
        async function loadDashboardData() {
            console.log('📊 Loading dashboard data...');
            
            try {
                // Show loading state
                setLoadingState(true);
                
                // Load all data concurrently
                const [dashboardResponse, tasksResponse, processesResponse] = await Promise.all([
                    apiCall('get_dashboard_data'),
                    apiCall('get_my_tasks'),
                    apiCall('get_active_executions')
                ]);
                
                // Update data
                if (dashboardResponse.success) {
                    dashboardData = dashboardResponse.dashboard_data[0] || {};
                    updateStatistics();
                }
                
                if (tasksResponse.success) {
                    tasksData = tasksResponse.tasks || [];
                    renderTasks();
                }
                
                if (processesResponse.success) {
                    processesData = processesResponse.executions || [];
                    renderProcesses();
                }
                
                console.log('✅ Dashboard data loaded successfully');
                
            } catch (error) {
                console.error('Error loading dashboard:', error);
                showNotification('Failed to load dashboard data', 'danger');
            } finally {
                setLoadingState(false);
            }
        }
        
        // Update statistics
        function updateStatistics() {
            const stats = {
                overdue: tasksData.filter(t => t.task_status === 'overdue').length,
                dueSoon: tasksData.filter(t => t.task_status === 'due_soon').length,
                inProgress: tasksData.filter(t => t.task_status === 'upcoming' || t.status === 'in_progress').length,
                completedToday: tasksData.filter(t => t.task_status === 'completed_today').length
            };
            
            document.getElementById('overdueCount').textContent = stats.overdue;
            document.getElementById('dueSoonCount').textContent = stats.dueSoon;
            document.getElementById('inProgressCount').textContent = stats.inProgress;
            document.getElementById('completedTodayCount').textContent = stats.completedToday;
        }
        
        // Render tasks
        function renderTasks() {
            const container = document.getElementById('tasksContainer');
            
            if (tasksData.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <h5>No tasks assigned</h5>
                        <p>All caught up! 🎉</p>
                    </div>
                `;
                return;
            }
            
            // Sort tasks by priority
            const sortedTasks = [...tasksData].sort((a, b) => {
                const priority = { 'overdue': 1, 'due_soon': 2, 'upcoming': 3, 'completed_today': 4 };
                return priority[a.task_status] - priority[b.task_status];
            });
            
            container.innerHTML = sortedTasks.map(task => `
                <div class="task-item ${task.task_status}">
                    <div class="task-info">
                        <div class="task-name">${task.step_name}</div>
                        <div class="task-meta">
                            <span class="me-3"><i class="fas fa-project-diagram me-1"></i>${task.execution_name}</span>
                            <span class="me-3"><i class="fas fa-clock me-1"></i>${formatDateTime(task.scheduled_completion)}</span>
                            <span><i class="fas fa-user me-1"></i>${task.assigned_role}</span>
                        </div>
                    </div>
                    <div class="task-actions">
                        ${task.status === 'pending' ? `
                            <button class="btn btn-success btn-sm" onclick="startTask(${task.id})">
                                <i class="fas fa-play"></i> Start
                            </button>
                        ` : ''}
                        ${task.status === 'in_progress' ? `
                            <button class="btn btn-primary btn-sm" onclick="openTaskModal(${task.id})">
                                <i class="fas fa-edit"></i> Complete
                            </button>
                        ` : ''}
                        <button class="btn btn-outline-secondary btn-sm" onclick="viewTaskDetails(${task.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        }
        
        // Render processes
        function renderProcesses() {
            const container = document.getElementById('processesContainer');
            
            if (processesData.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-project-diagram"></i>
                        <h6>No active processes</h6>
                        <button class="btn btn-primary btn-sm mt-2" onclick="showCreateProcess()">
                            <i class="fas fa-plus"></i> Create Process
                        </button>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = processesData.slice(0, 5).map(process => {
                const progress = Math.round((process.current_step / process.total_steps) * 100);
                return `
                    <div class="process-card">
                        <div class="process-header">
                            <h6 class="process-title">${process.execution_name}</h6>
                            <span class="process-status status-${process.status}">${process.status}</span>
                        </div>
                        <div class="process-meta mb-2">
                            <small class="text-muted">
                                <i class="fas fa-box me-1"></i>${process.batch_number || 'No batch'}
                            </small>
                        </div>
                        <div class="progress-container">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small>Progress</small>
                                <small>${progress}%</small>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-primary" style="width: ${progress}%"></div>
                            </div>
                            <small class="text-muted">Step ${process.current_step} of ${process.total_steps}</small>
                        </div>
                    </div>
                `;
            }).join('');
        }
        
        // Utility functions
        function formatDateTime(dateString) {
            if (!dateString) return 'Not scheduled';
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = date - now;
            const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
            
            if (diffHours < 0) {
                return `<span class="text-danger">${Math.abs(diffHours)}h ago</span>`;
            } else if (diffHours < 2) {
                return `<span class="text-warning">In ${diffHours}h</span>`;
            } else {
                return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            }
        }
        
        function setLoadingState(loading) {
            document.body.classList.toggle('loading', loading);
        }
        
        function showNotification(message, type = 'info') {
            // Simple notification - can be enhanced with proper toast library
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.top = '20px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
            }, 5000);
        }
        
        // Event handlers
        function refreshDashboard() {
            console.log('🔄 Refreshing dashboard...');
            loadDashboardData();
        }
        
        function startAutoRefresh() {
            refreshInterval = setInterval(() => {
                loadDashboardData();
            }, 60000); // Refresh every minute
        }
        
        function setupEventListeners() {
            // Task filter buttons
            document.querySelectorAll('input[name="taskFilter"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    // Filter tasks based on selection
                    console.log('Filter changed to:', this.id);
                    // Implementation for filtering can be added here
                });
            });
        }
        
        // Task actions
        function startTask(taskId) {
            console.log('Starting task:', taskId);
            // Implementation for starting task
            showNotification('Task started!', 'success');
        }
        
        function openTaskModal(taskId) {
            console.log('Opening task completion:', taskId);
            // Redirect to task completion page
            window.location.href = `task-completion.php?task_id=${taskId}`;
        }
        
        function viewTaskDetails(taskId) {
            console.log('Viewing task details:', taskId);
            // Implementation for viewing task details
            showNotification('Task details would open here', 'info');
        }
        
        function showCreateProcess() {
            console.log('Showing create process modal');
            // Implementation for create process modal
            showNotification('Create process modal would open here', 'info');
        }

        // Load notifications
        async function loadNotifications() {
            try {
                const response = await fetch('../ajax/notifications.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'get_notifications', company_id: COMPANY_ID, user_id: USER_ID })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    renderNotifications(data.notifications);
                    updateNotificationCount(data.notifications.filter(n => !n.is_read).length);
                }
                
            } catch (error) {
                console.error('Load notifications error:', error);
            }
        }

        // Render notifications
        function renderNotifications(notifications) {
            const container = document.getElementById('notifications-list');
            
            if (notifications.length === 0) {
                container.innerHTML = '<li><span class="dropdown-item-text text-muted">No notifications</span></li>';
                return;
            }
            
            container.innerHTML = notifications.map(notification => `
                <li>
                    <a class="dropdown-item ${notification.is_read ? '' : 'bg-light'}" href="#" 
                    onclick="markNotificationRead(${notification.id})">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">${notification.title}</h6>
                            <small class="text-muted">${formatTime(notification.created_at)}</small>
                        </div>
                        <p class="mb-1">${notification.message}</p>
                        <small class="text-muted">
                            <i class="fas fa-${getNotificationIcon(notification.type)} me-1"></i>
                            ${notification.type.replace('_', ' ')}
                        </small>
                    </a>
                </li>
            `).join('');
        }

        // Update notification count
        function updateNotificationCount(count) {
            const badge = document.getElementById('notification-count');
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
        }

        // Mark notification as read
        async function markNotificationRead(notificationId) {
            try {
                await fetch('../ajax/notifications.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'mark_read', notification_id: notificationId })
                });
                
                loadNotifications(); // Refresh notifications
                
            } catch (error) {
                console.error('Mark notification read error:', error);
            }
        }

        // Mark all notifications as read
        async function markAllRead() {
            // Implementation for marking all as read
            showNotification('All notifications marked as read', 'success');
            loadNotifications();
        }

        // Get notification icon
        function getNotificationIcon(type) {
            const icons = {
                'step_due': 'clock',
                'step_overdue': 'exclamation-triangle',
                'process_complete': 'check-circle',
                'issue_created': 'exclamation-circle',
                'issue_resolved': 'check',
                'process_started': 'play'
            };
            return icons[type] || 'bell';
        }

        // Format time
        function formatTime(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMinutes = Math.floor(diffMs / (1000 * 60));
            
            if (diffMinutes < 1) return 'Now';
            if (diffMinutes < 60) return `${diffMinutes}m ago`;
            if (diffMinutes < 1440) return `${Math.floor(diffMinutes/60)}h ago`;
            return date.toLocaleDateString();
        }

        // Start notification polling
        function startNotificationPolling() {
            loadNotifications(); // Load immediately
            notificationInterval = setInterval(loadNotifications, 30000); // Every 30 seconds
        }
        
        // Cleanup
        window.addEventListener('beforeunload', function() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        });
        
        console.log('✅ Process Dashboard JavaScript loaded');
    </script>
</body>
</html>