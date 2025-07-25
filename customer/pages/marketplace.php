<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Module Marketplace - Demo Company</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --dark-color: #1a1d20;
            --light-bg: #f8f9fa;
            --border-color: #dee2e6;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Inter', sans-serif;
        }

        .sidebar {
            background: linear-gradient(135deg, var(--dark-color) 0%, #2c3034 100%);
            min-height: 100vh;
            width: 260px;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
        }

        .sidebar .company-header {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.875rem 1.5rem;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }

        .main-content {
            margin-left: 260px;
            padding: 2rem;
        }

        .page-header {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }

        .filter-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            height: fit-content;
            position: sticky;
            top: 2rem;
        }

        .module-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            height: 100%;
        }

        .module-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1);
        }

        .module-card .card-header {
            background: linear-gradient(135deg, var(--primary-color), #0056b3);
            color: white;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .module-card .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(45deg);
        }

        .module-card .category-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .module-card .module-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }

        .module-card .card-body {
            padding: 1.5rem;
        }

        .price-tag {
            background: linear-gradient(135deg, var(--success-color), #20c997);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 1rem 0;
        }

        .feature-list li {
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
        }

        .feature-list li i {
            color: var(--success-color);
            margin-right: 0.5rem;
            width: 16px;
        }

        .btn-subscribe {
            background: linear-gradient(135deg, var(--primary-color), #0056b3);
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-subscribe:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
        }

        .btn-subscribed {
            background: var(--success-color);
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            width: 100%;
        }

        .search-bar {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            padding: 0.75rem 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .search-bar:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .filter-group {
            margin-bottom: 1.5rem;
        }

        .filter-group h6 {
            margin-bottom: 0.75rem;
            color: var(--dark-color);
            font-weight: 600;
        }

        .form-check {
            margin-bottom: 0.5rem;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .module-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .module-stats .stat {
            text-align: center;
        }

        .module-stats .stat-number {
            font-weight: 600;
            color: var(--primary-color);
        }

        .module-stats .stat-label {
            font-size: 0.75rem;
            color: var(--secondary-color);
        }

        .module-preview-modal .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .module-preview-modal .modal-header {
            background: linear-gradient(135deg, var(--primary-color), #0056b3);
            color: white;
            border-radius: 16px 16px 0 0;
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
        <div class="company-header">
            <div class="company-logo-small bg-primary text-white rounded-3 d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 48px; height: 48px; font-weight: bold;">DC</div>
            <h6 class="text-white mb-0">Demo Company</h6>
            <small class="text-white-50">demo.brcload.com</small>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link" href="?page=dashboard">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
            <a class="nav-link active" href="?page=marketplace">
                <i class="fas fa-store me-2"></i>Module Marketplace
            </a>
            <a class="nav-link" href="?page=modules">
                <i class="fas fa-puzzle-piece me-2"></i>My Modules
            </a>
            <div class="mt-3">
                <small class="text-white-50 px-3 text-uppercase">Active Modules</small>
                <a class="nav-link" href="?module=brc_risk_assessment">
                    <i class="fas fa-shield-alt me-2"></i>Risk Assessment
                </a>
            </div>
            <hr class="my-3 text-white-50">
            <a class="nav-link" href="?page=users">
                <i class="fas fa-users me-2"></i>Team Members
            </a>
            <a class="nav-link" href="?page=settings">
                <i class="fas fa-cog me-2"></i>Settings
            </a>
            <a class="nav-link" href="?page=logout">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="mb-2">Module Marketplace</h2>
                    <p class="text-muted mb-0">Discover and subscribe to powerful BRC compliance modules</p>
                </div>
                <div class="col-auto">
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control search-bar" placeholder="Search modules..." id="searchInput">
                        <span class="input-group-text bg-white border-start-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3">
                <div class="filter-card">
                    <h5 class="mb-3">Filters</h5>
                    
                    <div class="filter-group">
                        <h6>Category</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="brc" checked>
                            <label class="form-check-label" for="brc">BRC Compliance</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="quality">
                            <label class="form-check-label" for="quality">Quality Management</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="safety">
                            <label class="form-check-label" for="safety">Safety & Health</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="audit">
                            <label class="form-check-label" for="audit">Audit Management</label>
                        </div>
                    </div>

                    <div class="filter-group">
                        <h6>Price Range</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="free">
                            <label class="form-check-label" for="free">Free</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="under50">
                            <label class="form-check-label" for="under50">Under $50/month</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="50to100">
                            <label class="form-check-label" for="50to100">$50 - $100/month</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="over100">
                            <label class="form-check-label" for="over100">Over $100/month</label>
                        </div>
                    </div>

                    <div class="filter-group">
                        <h6>Module Type</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="base">
                            <label class="form-check-label" for="base">Base Modules</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="extension">
                            <label class="form-check-label" for="extension">Extensions</label>
                        </div>
                    </div>

                    <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                        <i class="fas fa-times me-2"></i>Clear Filters
                    </button>
                </div>
            </div>

            <!-- Modules Grid -->
            <div class="col-lg-9">
                <div class="row g-4" id="modulesGrid">
                    <!-- BRC Risk Assessment Module -->
                    <div class="col-md-6 col-xl-4">
                        <div class="module-card">
                            <div class="card-header">
                                <div class="category-badge">BRC Compliance</div>
                                <div class="module-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <h5 class="mb-0">BRC Risk Assessment</h5>
                                <small class="opacity-75">Complete risk management solution</small>
                            </div>
                            <div class="card-body">
                                <div class="price-tag">$99.99/month</div>
                                <p class="text-muted">Comprehensive BRC-compliant risk assessment module with automated workflows and compliance tracking.</p>
                                
                                <ul class="feature-list">
                                    <li><i class="fas fa-check"></i> Risk Matrix Calculator</li>
                                    <li><i class="fas fa-check"></i> Compliance Reporting</li>
                                    <li><i class="fas fa-check"></i> Automated Workflows</li>
                                    <li><i class="fas fa-check"></i> Team Collaboration</li>
                                </ul>

                                <div class="module-stats">
                                    <div class="stat">
                                        <div class="stat-number">4.9</div>
                                        <div class="stat-label">Rating</div>
                                    </div>
                                    <div class="stat">
                                        <div class="stat-number">247</div>
                                        <div class="stat-label">Users</div>
                                    </div>
                                    <div class="stat">
                                        <div class="stat-number">v2.1</div>
                                        <div class="stat-label">Version</div>
                                    </div>
                                </div>

                                <div class="mt-3 d-grid gap-2">
                                    <button class="btn btn-subscribed text-white" disabled>
                                        <i class="fas fa-check me-2"></i>Subscribed
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm" onclick="previewModule('brc_risk_assessment')">
                                        <i class="fas fa-eye me-2"></i>Preview
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quality Management Module -->
                    <div class="col-md-6 col-xl-4">
                        <div class="module-card">
                            <div class="card-header" style="background: linear-gradient(135deg, #28a745, #20c997);">
                                <div class="category-badge">Quality Management</div>
                                <div class="module-icon">
                                    <i class="fas fa-award"></i>
                                </div>
                                <h5 class="mb-0">Quality Management</h5>
                                <small class="opacity-75">ISO 9001 compliant quality system</small>
                            </div>
                            <div class="card-body">
                                <div class="price-tag">$79.99/month</div>
                                <p class="text-muted">Complete quality management system with document control, non-conformity tracking, and audit management.</p>
                                
                                <ul class="feature-list">
                                    <li><i class="fas fa-check"></i> Document Control</li>
                                    <li><i class="fas fa-check"></i> NCR Management</li>
                                    <li><i class="fas fa-check"></i> Internal Audits</li>
                                    <li><i class="fas fa-check"></i> CAPA Tracking</li>
                                </ul>

                                <div class="module-stats">
                                    <div class="stat">
                                        <div class="stat-number">4.7</div>
                                        <div class="stat-label">Rating</div>
                                    </div>
                                    <div class="stat">
                                        <div class="stat-number">189</div>
                                        <div class="stat-label">Users</div>
                                    </div>
                                    <div class="stat">
                                        <div class="stat-number">v1.8</div>
                                        <div class="stat-label">Version</div>
                                    </div>
                                </div>

                                <div class="mt-3 d-grid gap-2">
                                    <button class="btn btn-subscribe text-white" onclick="subscribeModule('quality_mgmt')">
                                        <i class="fas fa-plus me-2"></i>Subscribe
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm" onclick="previewModule('quality_mgmt')">
                                        <i class="fas fa-eye me-2"></i>Preview
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Safety Management Module -->
                    <div class="col-md-6 col-xl-4">
                        <div class="module-card">
                            <div class="card-header" style="background: linear-gradient(135deg, #dc3545, #fd7e14);">
                                <div class="category-badge">Safety & Health</div>
                                <div class="module-icon">
                                    <i class="fas fa-hard-hat"></i>
                                </div>
                                <h5 class="mb-0">Safety Management</h5>
                                <small class="opacity-75">OHSAS 18001 safety management</small>
                            </div>
                            <div class="card-body">
                                <div class="price-tag">$89.99/month</div>
                                <p class="text-muted">Comprehensive safety management system with incident tracking, safety training, and compliance monitoring.</p>
                                
                                <ul class="feature-list">
                                    <li><i class="fas fa-check"></i> Incident Management</li>
                                    <li><i class="fas fa-check"></i> Safety Training</li>
                                    <li><i class="fas fa-check"></i> PPE Tracking</li>
                                    <li><i class="fas fa-check"></i> Emergency Procedures</li>
                                </ul>

                                <div class="module-stats">
                                    <div class="stat">
                                        <div class="stat-number">4.8</div>
                                        <div class="stat-label">Rating</div>
                                    </div>
                                    <div class="stat">
                                        <div class="stat-number">156</div>
                                        <div class="stat-label">Users</div>
                                    </div>
                                    <div class="stat">
                                        <div class="stat-number">v1.5</div>
                                        <div class="stat-label">Version</div>
                                    </div>
                                </div>

                                <div class="mt-3 d-grid gap-2">
                                    <button class="btn btn-subscribe text-white" onclick="subscribeModule('safety_mgmt')">
                                        <i class="fas fa-plus me-2"></i>Subscribe
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm" onclick="previewModule('safety_mgmt')">
                                        <i class="fas fa-eye me-2"></i>Preview
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Audit Management Module -->
                    <div class="col-md-6 col-xl-4">
                        <div class="module-card">
                            <div class="card-header" style="background: linear-gradient(135deg, #6f42c1, #e83e8c);">
                                <div class="category-badge">Audit Management</div>
                                <div class="module-icon">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                                <h5 class="mb-0">Audit Management</h5>
                                <small class="opacity-75">Internal & external audit system</small>
                            </div>
                            <div class="card-body">
                                <div class="price-tag">$69.99/month</div>
                                <p class="text-muted">Complete audit management system with planning, execution, and follow-up capabilities for all audit types.</p>
                                
                                <ul class="feature-list">
                                    <li><i class="fas fa-check"></i> Audit Planning</li>
                                    <li><i class="fas fa-check"></i> Finding Tracking</li>
                                    <li><i class="fas fa-check"></i> Corrective Actions</li>
                                    <li><i class="fas fa-check"></i> Compliance Reports</li>
                                </ul>

                                <div class="module-stats">
                                    <div class="stat">
                                        <div class="stat-number">4.6</div>
                                        <div class="stat-label">Rating</div>
                                    </div>
                                    <div class="stat">
                                        <div class="stat-number">134</div>
                                        <div class="stat-label">Users</div>
                                    </div>
                                    <div class="stat">
                                        <div class="stat-number">v1.3</div>
                                        <div class="stat-label">Version</div>
                                    </div>
                                </div>

                                <div class="mt-3 d-grid gap-2">
                                    <button class="btn btn-subscribe text-white" onclick="subscribeModule('audit_mgmt')">
                                        <i class="fas fa-plus me-2"></i>Subscribe
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm" onclick="previewModule('audit_mgmt')">
                                        <i class="fas fa-eye me-2"></i>Preview
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Document Control Module -->
                    <div class="col-md-6 col-xl-4">
                        <div class="module-card">
                            <div class="card-header" style="background: linear-gradient(135deg, #17a2b8, #6610f2);">
                                <div class="category-badge">Document Control</div>
                                <div class="module-icon">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <h5 class="mb-0">Document Control</h5>
                                <small class="opacity-75">Centralized document management</small>
                            </div>
                            <div class="card-body">
                                <div class="price-tag">$49.99/month</div>
                                <p class="text-muted">Advanced document control system with version management, approval workflows, and distribution tracking.</p>
                                
                                <ul class="feature-list">
                                    <li><i class="fas fa-check"></i> Version Control</li>
                                    <li><i class="fas fa-check"></i> Approval Workflows</li>
                                    <li><i class="fas fa-check"></i> Distribution Lists</li>
                                    <li><i class="fas fa-check"></i> Archive Management</li>
                                </ul>

                                <div class="module-stats">
                                    <div class="stat">
                                        <div class="stat-number">4.4</div>
                                        <div class="stat-label">Rating</div>
                                    </div>
                                    <div class="stat">
                                        <div class="stat-number">203</div>
                                        <div class="stat-label">Users</div>
                                    </div>
                                    <div class="stat">
                                        <div class="stat-number">v2.0</div>
                                        <div class="stat-label">Version</div>
                                    </div>
                                </div>

                                <div class="mt-3 d-grid gap-2">
                                    <button class="btn btn-subscribe text-white" onclick="subscribeModule('document_control')">
                                        <i class="fas fa-plus me-2"></i>Subscribe
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm" onclick="previewModule('document_control')">
                                        <i class="fas fa-eye me-2"></i>Preview
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Training Management Module -->
                    <div class="col-md-6 col-xl-4">
                        <div class="module-card">
                            <div class="card-header" style="background: linear-gradient(135deg, #fd7e14, #ffc107);">
                                <div class="category-badge">Training & Development</div>
                                <div class="module-icon">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <h5 class="mb-0">Training Management</h5>
                                <small class="opacity-75">Employee training & competency</small>
                            </div>
                            <div class="card-body">
                                <div class="price-tag">$59.99/month</div>
                                <p class="text-muted">Complete training management system with course creation, tracking, and competency assessment capabilities.</p>
                                
                                <ul class="feature-list">
                                    <li><i class="fas fa-check"></i> Course Management</li>
                                    <li><i class="fas fa-check"></i> Competency Matrix</li>
                                    <li><i class="fas fa-check"></i> Training Records</li>
                                    <li><i class="fas fa-check"></i> Certification Tracking</li>
                                </ul>

                                <div class="module-stats">
                                    <div class="stat">
                                        <div class="stat-number">4.5</div>
                                        <div class="stat-label">Rating</div>
                                    </div>
                                    <div class="stat">
                                        <div class="stat-number">178</div>
                                        <div class="stat-label">Users</div>
                                    </div>
                                    <div class="stat">
                                        <div class="stat-number">v1.7</div>
                                        <div class="stat-label">Version</div>
                                    </div>
                                </div>

                                <div class="mt-3 d-grid gap-2">
                                    <button class="btn btn-subscribe text-white" onclick="subscribeModule('training_mgmt')">
                                        <i class="fas fa-plus me-2"></i>Subscribe
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm" onclick="previewModule('training_mgmt')">
                                        <i class="fas fa-eye me-2"></i>Preview
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Module Preview Modal -->
    <div class="modal fade module-preview-modal" id="modulePreviewModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modulePreviewTitle">Module Preview</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modulePreviewBody">
                    <!-- Dynamic content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="subscribeFromPreview">Subscribe Now</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const moduleCards = document.querySelectorAll('.module-card');
            
            moduleCards.forEach(card => {
                const title = card.querySelector('h5').textContent.toLowerCase();
                const description = card.querySelector('.text-muted').textContent.toLowerCase();
                const category = card.querySelector('.category-badge').textContent.toLowerCase();
                
                const isVisible = title.includes(searchTerm) || 
                                description.includes(searchTerm) || 
                                category.includes(searchTerm);
                
                card.closest('.col-md-6').style.display = isVisible ? '' : 'none';
            });
        });

        // Filter functionality
        function applyFilters() {
            const checkedCategories = Array.from(document.querySelectorAll('input[type="checkbox"]:checked'))
                                          .map(cb => cb.id);
            
            // Implement filter logic here
            console.log('Applied filters:', checkedCategories);
        }

        function clearFilters() {
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
            document.querySelectorAll('.col-md-6').forEach(col => col.style.display = '');
        }

        // Subscribe to module
        function subscribeModule(moduleCode) {
            // Show loading state
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Subscribing...';
            btn.disabled = true;

            // Simulate API call
            setTimeout(() => {
                // Success - update button
                btn.className = 'btn btn-subscribed text-white';
                btn.innerHTML = '<i class="fas fa-check me-2"></i>Subscribed';
                btn.disabled = true;
                
                // Show success message
                alert('Successfully subscribed to module!');
                
                // Refresh sidebar to show new module
                updateSidebar(moduleCode);
            }, 2000);
        }

        // Preview module
        function previewModule(moduleCode) {
            const modal = new bootstrap.Modal(document.getElementById('modulePreviewModal'));
            const moduleData = getModuleData(moduleCode);
            
            document.getElementById('modulePreviewTitle').textContent = moduleData.name + ' - Preview';
            document.getElementById('modulePreviewBody').innerHTML = generatePreviewContent(moduleData);
            
            modal.show();
        }

        // Get module data (simulated)
        function getModuleData(moduleCode) {
            const modules = {
                'brc_risk_assessment': {
                    name: 'BRC Risk Assessment',
                    description: 'Complete BRC-compliant risk assessment module',
                    screenshots: ['screenshot1.jpg', 'screenshot2.jpg'],
                    features: ['Risk Matrix Calculator', 'Compliance Reporting', 'Automated Workflows']
                },
                'quality_mgmt': {
                    name: 'Quality Management',
                    description: 'ISO 9001 compliant quality system',
                    screenshots: ['quality1.jpg', 'quality2.jpg'],
                    features: ['Document Control', 'NCR Management', 'Internal Audits']
                }
            };
            
            return modules[moduleCode] || {};
        }

        // Generate preview content
        function generatePreviewContent(moduleData) {
            return `
                <div class="row">
                    <div class="col-md-8">
                        <h6>Module Screenshots</h6>
                        <div class="bg-light rounded p-4 text-center mb-4">
                            <i class="fas fa-image fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Interactive demo would be displayed here</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h6>Key Features</h6>
                        <ul class="list-unstyled">
                            ${moduleData.features?.map(feature => `<li><i class="fas fa-check text-success me-2"></i>${feature}</li>`).join('') || ''}
                        </ul>
                    </div>
                </div>
            `;
        }

        // Update sidebar with new module
        function updateSidebar(moduleCode) {
            const moduleNames = {
                'quality_mgmt': 'Quality Management',
                'safety_mgmt': 'Safety Management',
                'audit_mgmt': 'Audit Management'
            };
            
            const moduleIcons = {
                'quality_mgmt': 'fas fa-award',
                'safety_mgmt': 'fas fa-hard-hat',
                'audit_mgmt': 'fas fa-clipboard-check'
            };
            
            if (moduleNames[moduleCode]) {
                const activeModulesSection = document.querySelector('.sidebar nav .mt-3');
                const newModuleLink = document.createElement('a');
                newModuleLink.className = 'nav-link';
                newModuleLink.href = `?module=${moduleCode}`;
                newModuleLink.innerHTML = `<i class="${moduleIcons[moduleCode]} me-2"></i>${moduleNames[moduleCode]}`;
                
                activeModulesSection.appendChild(newModuleLink);
            }
        }

        // Add event listeners to filter checkboxes
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', applyFilters);
        });
    </script>
</body>
</html>