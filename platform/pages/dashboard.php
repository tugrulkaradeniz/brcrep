<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Management - BRC Load Platform</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --dark-color: #1e293b;
            --light-bg: #f8fafc;
            --border-color: #e2e8f0;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Inter', sans-serif;
        }

        .sidebar {
            background: linear-gradient(135deg, var(--dark-color) 0%, #334155 100%);
            min-height: 100vh;
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar .logo {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .logo h4 {
            color: white;
            margin: 0;
            font-weight: 700;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1.5rem;
            margin: 0.25rem 0;
            border-radius: 0;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }

        .main-content {
            margin-left: 250px;
            padding: 2rem;
            min-height: 100vh;
        }

        .header {
            background: white;
            padding: 1rem 2rem;
            margin: -2rem -2rem 2rem -2rem;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stats-card .icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stats-card.primary .icon {
            background: linear-gradient(135deg, var(--primary-color), #3b82f6);
            color: white;
        }

        .stats-card.success .icon {
            background: linear-gradient(135deg, var(--success-color), #10b981);
            color: white;
        }

        .stats-card.warning .icon {
            background: linear-gradient(135deg, var(--warning-color), #f59e0b);
            color: white;
        }

        .stats-card.danger .icon {
            background: linear-gradient(135deg, var(--danger-color), #ef4444);
            color: white;
        }

        .table-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
        }

        .table-card .card-header {
            background: var(--light-bg);
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .badge {
            font-weight: 500;
            padding: 0.5rem 0.75rem;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #3b82f6);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 0.75rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            background: var(--light-bg);
            border-bottom: 1px solid var(--border-color);
            border-radius: 12px 12px 0 0;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <h4><i class="fas fa-cubes me-2"></i>BRC Load</h4>
            <small class="text-white-50">Platform Admin</small>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link" href="?page=dashboard">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
            <a class="nav-link active" href="?page=companies">
                <i class="fas fa-building me-2"></i>Companies
            </a>
            <a class="nav-link" href="?page=modules">
                <i class="fas fa-puzzle-piece me-2"></i>Modules
            </a>
            <a class="nav-link" href="?page=module-builder">
                <i class="fas fa-tools me-2"></i>Module Builder
            </a>
            <a class="nav-link" href="?page=marketplace">
                <i class="fas fa-store me-2"></i>Marketplace
            </a>
            <a class="nav-link" href="?page=analytics">
                <i class="fas fa-chart-bar me-2"></i>Analytics
            </a>
            <a class="nav-link" href="?page=settings">
                <i class="fas fa-cog me-2"></i>Settings
            </a>
            <hr class="text-white-50 my-3">
            <a class="nav-link" href="?page=logout">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0">Company Management</h3>
                    <p class="text-muted mb-0">Manage customer companies and their subscriptions</p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCompanyModal">
                    <i class="fas fa-plus me-2"></i>Add New Company
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card primary">
                    <div class="icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h5 class="mb-1">Total Companies</h5>
                    <h3 class="mb-0" id="totalCompanies">24</h3>
                    <small class="text-muted">+3 this month</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card success">
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h5 class="mb-1">Active</h5>
                    <h3 class="mb-0" id="activeCompanies">18</h3>
                    <small class="text-muted">75% of total</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card warning">
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h5 class="mb-1">Trial</h5>
                    <h3 class="mb-0" id="trialCompanies">4</h3>
                    <small class="text-muted">Convert to paid</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card danger">
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h5 class="mb-1">Suspended</h5>
                    <h3 class="mb-0" id="suspendedCompanies">2</h3>
                    <small class="text-muted">Needs attention</small>
                </div>
            </div>
        </div>

        <!-- Companies Table -->
        <div class="table-card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">All Companies</h5>
                    </div>
                    <div class="col-auto">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search companies..." id="searchInput">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Company</th>
                            <th>Subdomain</th>
                            <th>Contact</th>
                            <th>Plan</th>
                            <th>Status</th>
                            <th>Users</th>
                            <th>Modules</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="companiesTableBody">
                        <!-- Demo Data -->
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        DC
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Demo Company Ltd.</h6>
                                        <small class="text-muted">demo@company.com</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="https://demo.brcload.com" target="_blank" class="text-decoration-none">
                                    demo.brcload.com <i class="fas fa-external-link-alt ms-1 small"></i>
                                </a>
                            </td>
                            <td>
                                <div>
                                    <div>John Doe</div>
                                    <small class="text-muted">john@democompany.com</small>
                                </div>
                            </td>
                            <td><span class="badge bg-success">Premium</span></td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>12</td>
                            <td>3</td>
                            <td>2024-01-15</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary" onclick="editCompany(1)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" onclick="viewCompany(1)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="suspendCompany(1)">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-warning text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        TC
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Test Corp</h6>
                                        <small class="text-muted">contact@testcorp.com</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="https://test.brcload.com" target="_blank" class="text-decoration-none">
                                    test.brcload.com <i class="fas fa-external-link-alt ms-1 small"></i>
                                </a>
                            </td>
                            <td>
                                <div>
                                    <div>Jane Smith</div>
                                    <small class="text-muted">jane@testcorp.com</small>
                                </div>
                            </td>
                            <td><span class="badge bg-primary">Basic</span></td>
                            <td><span class="badge bg-warning">Trial</span></td>
                            <td>3</td>
                            <td>1</td>
                            <td>2024-07-20</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary" onclick="editCompany(2)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" onclick="viewCompany(2)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="suspendCompany(2)">
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

    <!-- Add Company Modal -->
    <div class="modal fade" id="addCompanyModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Company</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addCompanyForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Company Name *</label>
                                    <input type="text" class="form-control" name="company_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Subdomain *</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="subdomain" required>
                                        <span class="input-group-text">.brcload.com</span>
                                    </div>
                                    <small class="text-muted">Only lowercase letters, numbers, and hyphens</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Contact Name *</label>
                                    <input type="text" class="form-control" name="contact_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Contact Email *</label>
                                    <input type="email" class="form-control" name="contact_email" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Contact Phone</label>
                                    <input type="tel" class="form-control" name="contact_phone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Plan Type</label>
                                    <select class="form-select" name="plan_type">
                                        <option value="basic">Basic ($29/month)</option>
                                        <option value="premium">Premium ($79/month)</option>
                                        <option value="enterprise">Enterprise ($199/month)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status">
                                        <option value="trial">Trial (30 days)</option>
                                        <option value="active">Active</option>
                                        <option value="suspended">Suspended</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Max Users</label>
                                    <input type="number" class="form-control" name="max_users" value="5" min="1">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Billing Address</label>
                            <textarea class="form-control" name="billing_address" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Company</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const tableRows = document.querySelectorAll('#companiesTableBody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Form submission
        document.getElementById('addCompanyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Simulate API call
            console.log('Creating company:', Object.fromEntries(formData));
            
            // Here you would make an AJAX call to create the company
            // fetch('/platform/ajax/company-actions.php', {
            //     method: 'POST',
            //     body: formData
            // })
            
            // For demo, just show success message
            alert('Company created successfully!');
            bootstrap.Modal.getInstance(document.getElementById('addCompanyModal')).hide();
            this.reset();
        });

        // Action functions
        function editCompany(id) {
            // Implement edit functionality
            console.log('Edit company:', id);
        }

        function viewCompany(id) {
            // Implement view functionality
            console.log('View company:', id);
        }

        function suspendCompany(id) {
            if (confirm('Are you sure you want to suspend this company?')) {
                // Implement suspend functionality
                console.log('Suspend company:', id);
            }
        }

        // Real-time subdomain validation
        document.querySelector('input[name="subdomain"]').addEventListener('input', function(e) {
            let value = e.target.value.toLowerCase().replace(/[^a-z0-9-]/g, '');
            e.target.value = value;
            
            // Check availability (you would implement this with AJAX)
            if (value.length > 2) {
                // Simulate availability check
                console.log('Checking availability for:', value);
            }
        });
    </script>
</body>
</html>