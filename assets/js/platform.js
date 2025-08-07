// Global variables (PHP tarafından set edilecek)
window.BRCPlatform = window.BRCPlatform || {
    baseUrl: '',
    adminId: null,
    adminRole: ''
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

// Company management functions
function createCompany(formData) {
    const data = Object.fromEntries(formData);
    data.action = 'create';
    
    makeAjaxRequest('platform/ajax/company-actions.php', data)
        .then(response => {
            if (response && response.success) {
                showAlert('Company created successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert(response?.message || 'Failed to create company', 'danger');
            }
        })
        .catch(error => {
            showAlert('Error creating company', 'danger');
        });
}

function updateCompany(companyId, formData) {
    const data = Object.fromEntries(formData);
    data.action = 'update';
    data.company_id = companyId;
    
    makeAjaxRequest('platform/ajax/company-actions.php', data)
        .then(response => {
            if (response && response.success) {
                showAlert('Company updated successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert(response?.message || 'Failed to update company', 'danger');
            }
        })
        .catch(error => {
            showAlert('Error updating company', 'danger');
        });
}

function deleteCompany(companyId) {
    if (confirm('Are you sure you want to suspend this company?')) {
        makeAjaxRequest('platform/ajax/company-actions.php', {
            action: 'delete',
            company_id: companyId
        })
        .then(response => {
            if (response && response.success) {
                showAlert('Company suspended successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert(response?.message || 'Failed to suspend company', 'danger');
            }
        })
        .catch(error => {
            showAlert('Error suspending company', 'danger');
        });
    }
}

// Module builder functions
function saveModule(moduleData) {
    makeAjaxRequest('platform/ajax/module-builder.php', {
        action: 'save',
        module_data: JSON.stringify(moduleData)
    })
    .then(response => {
        if (response && response.success) {
            showAlert('Module saved successfully!', 'success');
            if (response.module_id) {
                // Update UI with module ID if needed
                console.log('Module ID:', response.module_id);
            }
        } else {
            showAlert(response?.message || 'Failed to save module', 'danger');
        }
    })
    .catch(error => {
        showAlert('Error saving module', 'danger');
    });
}

function publishModule(moduleId) {
    if (confirm('Are you sure you want to publish this module? It will become available in the marketplace.')) {
        makeAjaxRequest('platform/ajax/module-builder.php', {
            action: 'publish',
            module_id: moduleId
        })
        .then(response => {
            if (response && response.success) {
                showAlert('Module published successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert(response?.message || 'Failed to publish module', 'danger');
            }
        })
        .catch(error => {
            showAlert('Error publishing module', 'danger');
        });
    }
}

// Dashboard functions
function loadDashboardStats() {
    makeAjaxRequest('platform/ajax/dashboard-stats.php', { action: 'get_stats' }, 'GET')
        .then(response => {
            if (response && response.success) {
                updateDashboardStats(response.data);
            }
        })
        .catch(error => {
            console.error('Error loading dashboard stats:', error);
        });
}

function updateDashboardStats(stats) {
    // Update stats on dashboard
    const elements = {
        'totalCompanies': stats.total_companies,
        'activeCompanies': stats.active_companies,
        'trialCompanies': stats.trial_companies,
        'suspendedCompanies': stats.suspended_companies,
        'totalRevenue': '$' + (stats.total_revenue || 0)
    };

    Object.keys(elements).forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = elements[id];
        }
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

// Real-time validation for subdomain
function validateSubdomain(input) {
    if (!input) return;
    
    const subdomain = input.value.toLowerCase().replace(/[^a-z0-9-]/g, '');
    input.value = subdomain;
    
    if (subdomain.length > 2) {
        // Check availability
        makeAjaxRequest('platform/ajax/company-actions.php', {
            action: 'check_subdomain',
            subdomain: subdomain
        }, 'GET')
        .then(response => {
            if (response && response.available) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            } else {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
            }
        })
        .catch(error => {
            console.error('Error checking subdomain:', error);
        });
    }
}

// Auto-save functionality
let autoSaveTimer = null;

function enableAutoSave(formSelector, saveCallback) {
    const form = document.querySelector(formSelector);
    if (!form) return;
    
    form.addEventListener('input', function() {
        // Clear existing timer
        if (autoSaveTimer) {
            clearTimeout(autoSaveTimer);
        }
        
        // Set new timer for 3 seconds
        autoSaveTimer = setTimeout(() => {
            if (typeof saveCallback === 'function') {
                saveCallback(new FormData(form));
            }
        }, 3000);
    });
}

// Initialize platform admin panel
document.addEventListener('DOMContentLoaded', function() {
    console.log('BRC Platform Admin Panel initialized');
    
    // Load dashboard stats if on dashboard page
    const currentPage = window.location.href;
    if (currentPage.includes('dashboard') || currentPage.includes('page=admin')) {
        loadDashboardStats();
    }
    
    // Initialize form validations
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form)) {
                e.preventDefault();
                showAlert('Please fill in all required fields.', 'warning');
            }
        });
    });
    
    // Initialize subdomain validation
    const subdomainInputs = document.querySelectorAll('input[name="subdomain"]');
    subdomainInputs.forEach(input => {
        input.addEventListener('input', () => validateSubdomain(input));
    });
    
    // Auto-refresh dashboard every 5 minutes
    setInterval(() => {
        if (document.visibilityState === 'visible' && 
            (currentPage.includes('dashboard') || currentPage.includes('page=admin'))) {
            loadDashboardStats();
        }
    }, 300000);
});

// Export functions for global use
window.BRCPlatformFunctions = {
    showAlert,
    makeAjaxRequest,
    createCompany,
    updateCompany,
    deleteCompany,
    saveModule,
    publishModule,
    validateForm,
    toggleSidebar,
    validateSubdomain
};