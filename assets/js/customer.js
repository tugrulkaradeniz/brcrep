// Global variables (Set by PHP)
window.BRCCustomer = window.BRCCustomer || {
    baseUrl: '',
    companyId: null,
    userId: null,
    userRole: ''
};

// Utility functions
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

function makeAjaxRequest(url, data, method = 'POST') {
    const options = {
        method: method,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    };

    if (method === 'POST') {
        options.headers['Content-Type'] = 'application/x-www-form-urlencoded';
        options.body = new URLSearchParams(data);
    } else if (method === 'GET' && data) {
        url += '?' + new URLSearchParams(data);
    }

    return fetch(url, options)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .catch(error => {
            console.error('AJAX Error:', error);
            showAlert('An error occurred. Please try again.', 'danger');
            throw error;
        });
}

// Module subscription functions
function subscribeToModule(moduleId) {
    if (!confirm('Are you sure you want to subscribe to this module?')) {
        return;
    }

    makeAjaxRequest('customer/ajax/module-actions.php', {
        action: 'subscribe',
        module_id: moduleId
    })
    .then(response => {
        if (response && response.success) {
            const moduleName = response.module_name || 'module';
            showAlert(`Successfully subscribed to ${moduleName}!`, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(response?.message || 'Failed to subscribe to module', 'danger');
        }
    })
    .catch(error => {
        showAlert('Error subscribing to module', 'danger');
    });
}

function unsubscribeFromModule(moduleId) {
    if (!confirm('Are you sure you want to unsubscribe from this module?')) {
        return;
    }

    makeAjaxRequest('customer/ajax/module-actions.php', {
        action: 'unsubscribe',
        module_id: moduleId
    })
    .then(response => {
        if (response && response.success) {
            showAlert('Successfully unsubscribed from module.', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(response?.message || 'Failed to unsubscribe from module', 'danger');
        }
    })
    .catch(error => {
        showAlert('Error unsubscribing from module', 'danger');
    });
}

// Data management functions
function saveData(moduleId, dataType, dataId, dataContent) {
    return makeAjaxRequest('customer/ajax/data-actions.php', {
        action: 'save_data',
        module_id: moduleId,
        data_type: dataType,
        data_id: dataId,
        data_content: JSON.stringify(dataContent)
    })
    .then(response => {
        if (response && response.success) {
            showAlert('Data saved successfully!', 'success');
            return response;
        } else {
            showAlert(response?.message || 'Failed to save data', 'danger');
            throw new Error('Save failed');
        }
    })
    .catch(error => {
        showAlert('Error saving data', 'danger');
        throw error;
    });
}

function loadData(moduleId, dataType, dataId = null) {
    const params = {
        action: 'get_data',
        module_id: moduleId,
        data_type: dataType
    };
    
    if (dataId) {
        params.data_id = dataId;
    }
    
    return makeAjaxRequest('customer/ajax/data-actions.php', params, 'GET');
}

// Dashboard functions
function loadDashboardData() {
    // Load recent activities
    loadData('dashboard', 'recent_activities')
        .then(response => {
            if (response && response.success) {
                updateRecentActivities(response.data);
            }
        })
        .catch(error => {
            console.error('Error loading dashboard data:', error);
        });
}

function updateRecentActivities(activities) {
    const container = document.getElementById('recentActivities');
    if (container && activities) {
        // Update recent activities display
        console.log('Updating recent activities:', activities);
        // Implementation would go here
    }
}

// Module-specific functions
function openModule(moduleCode) {
    window.location.href = `?module=${moduleCode}`;
}

function createNewRecord(moduleCode, recordType) {
    window.location.href = `?module=${moduleCode}&action=new&type=${recordType}`;
}

// Risk Assessment specific functions
function calculateRiskScore(likelihood, impact) {
    return likelihood * impact;
}

function getRiskLevel(score) {
    if (score >= 15) return 'High';
    if (score >= 6) return 'Medium';
    return 'Low';
}

function getRiskLevelColor(level) {
    const colors = {
        'High': 'danger',
        'Medium': 'warning',
        'Low': 'success'
    };
    return colors[level] || 'secondary';
}

// Auto-save functionality
let autoSaveTimer = null;

function enableAutoSave(formId, moduleId, dataType, dataId) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    form.addEventListener('input', function() {
        // Clear existing timer
        if (autoSaveTimer) {
            clearTimeout(autoSaveTimer);
        }
        
        // Set new timer for 2 seconds
        autoSaveTimer = setTimeout(() => {
            const formData = new FormData(form);
            const dataContent = Object.fromEntries(formData);
            
            // Save data
            saveData(moduleId, dataType, dataId, dataContent)
                .then(() => {
                    showAutoSaveIndicator();
                })
                .catch(error => {
                    console.error('Auto-save failed:', error);
                });
        }, 2000);
    });
}

function showAutoSaveIndicator() {
    let indicator = document.getElementById('autoSaveIndicator');
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'autoSaveIndicator';
        indicator.className = 'position-fixed bottom-0 end-0 m-3 alert alert-success fade';
        indicator.innerHTML = '<i class="fas fa-check me-2"></i>Auto-saved';
        document.body.appendChild(indicator);
    }
    
    indicator.classList.add('show');
    
    setTimeout(() => {
        indicator.classList.remove('show');
    }, 2000);
}

// Search functionality
function enableSearch(inputId, targetSelector) {
    const searchInput = document.getElementById(inputId);
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const targets = document.querySelectorAll(targetSelector);
        
        targets.forEach(target => {
            const text = target.textContent.toLowerCase();
            const parent = target.closest('tr, .card, .item');
            if (parent) {
                parent.style.display = text.includes(searchTerm) ? '' : 'none';
            }
        });
    });
}

// Form validation
function validateForm(form) {
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        const value = input.value.trim();
        if (!value) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
        }
    });
    
    return isValid;
}

// Sidebar toggle for mobile
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.toggle('show');
    }
}

// Notification functions
function markNotificationAsRead(notificationId) {
    makeAjaxRequest('customer/ajax/notifications.php', {
        action: 'mark_read',
        notification_id: notificationId
    })
    .then(response => {
        if (response && response.success) {
            // Update notification badge
            updateNotificationBadge();
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

function updateNotificationBadge() {
    makeAjaxRequest('customer/ajax/notifications.php', {
        action: 'get_unread_count'
    }, 'GET')
    .then(response => {
        if (response && response.success) {
            const badge = document.querySelector('.notification-badge');
            if (badge) {
                badge.textContent = response.count;
                badge.style.display = response.count > 0 ? 'block' : 'none';
            }
        }
    })
    .catch(error => {
        console.error('Error updating notification badge:', error);
    });
}

// Initialize customer panel
document.addEventListener('DOMContentLoaded', function() {
    console.log('BRC Customer Panel initialized');
    
    // Load dashboard data if on dashboard page
    const currentPage = window.location.href;
    if (currentPage.includes('dashboard')) {
        loadDashboardData();
    }
    
    // Enable search on tables
    enableSearch('searchInput', 'tbody tr');
    
    // Initialize forms validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form)) {
                e.preventDefault();
                showAlert('Please fill in all required fields.', 'warning');
            }
        });
    });
    
    // Auto-refresh every 30 seconds
    setInterval(() => {
        if (document.visibilityState === 'visible' && currentPage.includes('dashboard')) {
            loadDashboardData();
        }
    }, 30000);
});

// Export functions for global use
window.BRCCustomerFunctions = {
    showAlert,
    makeAjaxRequest,
    subscribeToModule,
    unsubscribeFromModule,
    saveData,
    loadData,
    openModule,
    createNewRecord,
    calculateRiskScore,
    getRiskLevel,
    getRiskLevelColor,
    enableAutoSave,
    enableSearch,
    validateForm,
    toggleSidebar
};