<?php
// platform/pages/templates.php
require_once __DIR__ . '/../../config/config.php';

// Check admin authentication
if (!isset($_SESSION['platform_admin_id'])) {
    header('Location: /platform/auth/login');
    exit;
}

$page_title = 'Module Templates';
include __DIR__ . '/../layout/header.php';
?>

<div class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">
                    <i class="fas fa-layer-group text-primary"></i>
                    Module Templates
                </h1>
                <p class="page-subtitle">Pre-built templates for rapid module development</p>
            </div>
            <div class="col-auto">
                <div class="btn-group">
                    <button class="btn btn-outline-primary" onclick="importTemplate()">
                        <i class="fas fa-upload"></i> Import Template
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTemplateModal">
                        <i class="fas fa-plus"></i> Create Template
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Template Categories -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="category-card active" data-category="all" onclick="filterByCategory('all')">
                <div class="category-icon bg-primary">
                    <i class="fas fa-th-large"></i>
                </div>
                <div class="category-content">
                    <h5>All Templates</h5>
                    <p>24 templates</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="category-card" data-category="risk" onclick="filterByCategory('risk')">
                <div class="category-icon bg-danger">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="category-content">
                    <h5>Risk Management</h5>
                    <p>8 templates</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="category-card" data-category="compliance" onclick="filterByCategory('compliance')">
                <div class="category-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="category-content">
                    <h5>Compliance</h5>
                    <p>6 templates</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="category-card" data-category="analytics" onclick="filterByCategory('analytics')">
                <div class="category-icon bg-info">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="category-content">
                    <h5>Analytics</h5>
                    <p>10 templates</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="search-box">
                        <input type="text" class="form-control" id="searchTemplates" placeholder="Search templates...">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="sortBy">
                        <option value="name">Sort by Name</option>
                        <option value="date">Sort by Date</option>
                        <option value="downloads">Sort by Downloads</option>
                        <option value="rating">Sort by Rating</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filterComplexity">
                        <option value="">All Complexity</option>
                        <option value="simple">Simple</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filterStatus">
                        <option value="">All Status</option>
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                        <option value="deprecated">Deprecated</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                        <i class="fas fa-times"></i> Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Templates Grid -->
    <div class="row" id="templatesGrid">
        <!-- BRC Risk Assessment Template -->
        <div class="col-lg-4 col-md-6 mb-4 template-card" data-category="risk" data-complexity="advanced">
            <div class="card template-item h-100">
                <div class="template-preview">
                    <img src="/assets/images/templates/brc-risk-assessment.png" alt="BRC Risk Assessment" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="preview-placeholder" style="display: none;">
                        <i class="fas fa-shield-alt"></i>
                        <span>Risk Assessment</span>
                    </div>
                    <div class="template-badges">
                        <span class="badge bg-danger">Risk Management</span>
                        <span class="badge bg-warning text-dark">Advanced</span>
                    </div>
                    <div class="template-actions">
                        <button class="btn btn-sm btn-light" onclick="previewTemplate(1)" title="Preview">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-light" onclick="useTemplate(1)" title="Use Template">
                            <i class="fas fa-play"></i>
                        </button>
                        <button class="btn btn-sm btn-light" onclick="editTemplate(1)" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="template-header">
                        <h5 class="template-title">BRC Risk Assessment</h5>
                        <span class="template-version">v2.1</span>
                    </div>
                    <p class="template-description">
                        Comprehensive 5x5 risk matrix with BRC compliance workflow, automated calculations, and detailed reporting capabilities.
                    </p>
                    <div class="template-stats">
                        <div class="stat-item">
                            <i class="fas fa-download text-primary"></i>
                            <span>1,234</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-star text-warning"></i>
                            <span>4.8</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-clock text-muted"></i>
                            <span>Updated 2 days ago</span>
                        </div>
                    </div>
                    <div class="template-components">
                        <small class="text-muted">Components:</small>
                        <div class="component-tags mt-1">
                            <span class="component-tag">Risk Matrix</span>
                            <span class="component-tag">Forms</span>
                            <span class="component-tag">Workflow</span>
                            <span class="component-tag">Reports</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Dashboard Template -->
        <div class="col-lg-4 col-md-6 mb-4 template-card" data-category="analytics" data-complexity="intermediate">
            <div class="card template-item h-100">
                <div class="template-preview">
                    <div class="preview-placeholder">
                        <i class="fas fa-chart-line"></i>
                        <span>Analytics Dashboard</span>
                    </div>
                    <div class="template-badges">
                        <span class="badge bg-info">Analytics</span>
                        <span class="badge bg-success">Intermediate</span>
                    </div>
                    <div class="template-actions">
                        <button class="btn btn-sm btn-light" onclick="previewTemplate(2)" title="Preview">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-light" onclick="useTemplate(2)" title="Use Template">
                            <i class="fas fa-play"></i>
                        </button>
                        <button class="btn btn-sm btn-light" onclick="editTemplate(2)" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="template-header">
                        <h5 class="template-title">Analytics Dashboard</h5>
                        <span class="template-version">v1.5</span>
                    </div>
                    <p class="template-description">
                        Interactive dashboard with KPI cards, charts, and real-time data visualization. Perfect for business intelligence modules.
                    </p>
                    <div class="template-stats">
                        <div class="stat-item">
                            <i class="fas fa-download text-primary"></i>
                            <span>987</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-star text-warning"></i>
                            <span>4.6</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-clock text-muted"></i>
                            <span>Updated 1 week ago</span>
                        </div>
                    </div>
                    <div class="template-components">
                        <small class="text-muted">Components:</small>
                        <div class="component-tags mt-1">
                            <span class="component-tag">KPI Cards</span>
                            <span class="component-tag">Charts</span>
                            <span class="component-tag">Filters</span>
                            <span class="component-tag">Export</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Simple Form Template -->
        <div class="col-lg-4 col-md-6 mb-4 template-card" data-category="compliance" data-complexity="simple">
            <div class="card template-item h-100">
                <div class="template-preview">
                    <div class="preview-placeholder">
                        <i class="fas fa-wpforms"></i>
                        <span>Simple Form</span>
                    </div>
                    <div class="template-badges">
                        <span class="badge bg-success">Compliance</span>
                        <span class="badge bg-primary">Simple</span>
                    </div>
                    <div class="template-actions">
                        <button class="btn btn-sm btn-light" onclick="previewTemplate(3)" title="Preview">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-light" onclick="useTemplate(3)" title="Use Template">
                            <i class="fas fa-play"></i>
                        </button>
                        <button class="btn btn-sm btn-light" onclick="editTemplate(3)" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="template-header">
                        <h5 class="template-title">Compliance Form</h5>
                        <span class="template-version">v1.2</span>
                    </div>
                    <p class="template-description">
                        Clean and simple form template with validation, file uploads, and submission tracking. Great starting point for any module.
                    </p>
                    <div class="template-stats">
                        <div class="stat-item">
                            <i class="fas fa-download text-primary"></i>
                            <span>756</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-star text-warning"></i>
                            <span>4.4</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-clock text-muted"></i>
                            <span>Updated 3 days ago</span>
                        </div>
                    </div>
                    <div class="template-components">
                        <small class="text-muted">Components:</small>
                        <div class="component-tags mt-1">
                            <span class="component-tag">Forms</span>
                            <span class="component-tag">Validation</span>
                            <span class="component-tag">File Upload</span>
                            <span class="component-tag">Table</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Audit Checklist Template -->
        <div class="col-lg-4 col-md-6 mb-4 template-card" data-category="compliance" data-complexity="intermediate">
            <div class="card template-item h-100">
                <div class="template-preview">
                    <div class="preview-placeholder">
                        <i class="fas fa-clipboard-check"></i>
                        <span>Audit Checklist</span>
                    </div>
                    <div class="template-badges">
                        <span class="badge bg-success">Compliance</span>
                        <span class="badge bg-success">Intermediate</span>
                    </div>
                    <div class="template-actions">
                        <button class="btn btn-sm btn-light" onclick="previewTemplate(4)" title="Preview">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-light" onclick="useTemplate(4)" title="Use Template">
                            <i class="fas fa-play"></i>
                        </button>
                        <button class="btn btn-sm btn-light" onclick="editTemplate(4)" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="template-header">
                        <h5 class="template-title">Audit Checklist</h5>
                        <span class="template-version">v1.8</span>
                    </div>
                    <p class="template-description">
                        Comprehensive audit checklist with scoring, comments, photo attachments, and automated compliance scoring.
                    </p>
                    <div class="template-stats">
                        <div class="stat-item">
                            <i class="fas fa-download text-primary"></i>
                            <span>654</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-star text-warning"></i>
                            <span>4.7</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-clock text-muted"></i>
                            <span>Updated 5 days ago</span>
                        </div>
                    </div>
                    <div class="template-components">
                        <small class="text-muted">Components:</small>
                        <div class="component-tags mt-1">
                            <span class="component-tag">Checklist</span>
                            <span class="component-tag">Scoring</span>
                            <span class="component-tag">Photos</span>
                            <span class="component-tag">Comments</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Visualization Template -->
        <div class="col-lg-4 col-md-6 mb-4 template-card" data-category="analytics" data-complexity="advanced">
            <div class="card template-item h-100">
                <div class="template-preview">
                    <div class="preview-placeholder">
                        <i class="fas fa-chart-pie"></i>
                        <span>Data Visualization</span>
                    </div>
                    <div class="template-badges">
                        <span class="badge bg-info">Analytics</span>
                        <span class="badge bg-warning text-dark">Advanced</span>
                    </div>
                    <div class="template-actions">
                        <button class="btn btn-sm btn-light" onclick="previewTemplate(5)" title="Preview">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-light" onclick="useTemplate(5)" title="Use Template">
                            <i class="fas fa-play"></i>
                        </button>
                        <button class="btn btn-sm btn-light" onclick="editTemplate(5)" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="template-header">
                        <h5 class="template-title">Data Visualization</h5>
                        <span class="template-version">v2.0</span>
                    </div>
                    <p class="template-description">
                        Advanced data visualization with multiple chart types, interactive filters, and real-time data binding capabilities.
                    </p>
                    <div class="template-stats">
                        <div class="stat-item">
                            <i class="fas fa-download text-primary"></i>
                            <span>432</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-star text-warning"></i>
                            <span>4.9</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-clock text-muted"></i>
                            <span>Updated 1 day ago</span>
                        </div>
                    </div>
                    <div class="template-components">
                        <small class="text-muted">Components:</small>
                        <div class="component-tags mt-1">
                            <span class="component-tag">Charts</span>
                            <span class="component-tag">Filters</span>
                            <span class="component-tag">Real-time</span>
                            <span class="component-tag">Export</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty Template -->
        <div class="col-lg-4 col-md-6 mb-4 template-card" data-category="all" data-complexity="simple">
            <div class="card template-item h-100">
                <div class="template-preview">
                    <div class="preview-placeholder">
                        <i class="fas fa-plus"></i>
                        <span>Blank Template</span>
                    </div>
                    <div class="template-badges">
                        <span class="badge bg-secondary">Starter</span>
                        <span class="badge bg-primary">Simple</span>
                    </div>
                    <div class="template-actions">
                        <button class="btn btn-sm btn-light" onclick="useTemplate(0)" title="Use Template">
                            <i class="fas fa-play"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="template-header">
                        <h5 class="template-title">Blank Canvas</h5>
                        <span class="template-version">v1.0</span>
                    </div>
                    <p class="template-description">
                        Start from scratch with a clean slate. Perfect for creating completely custom modules without any predefined structure.
                    </p>
                    <div class="template-stats">
                        <div class="stat-item">
                            <i class="fas fa-download text-primary"></i>
                            <span>2,156</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-star text-warning"></i>
                            <span>5.0</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-clock text-muted"></i>
                            <span>Always current</span>
                        </div>
                    </div>
                    <div class="template-components">
                        <small class="text-muted">Components:</small>
                        <div class="component-tags mt-1">
                            <span class="component-tag">Empty Canvas</span>
                            <span class="component-tag">Full Flexibility</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    <div class="empty-state" id="emptyState" style="display: none;">
        <div class="text-center py-5">
            <i class="fas fa-layer-group fa-4x text-muted mb-3"></i>
            <h4>No templates found</h4>
            <p class="text-muted">Try adjusting your search or filters, or create a new template.</p>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTemplateModal">
                <i class="fas fa-plus"></i> Create New Template
            </button>
        </div>
    </div>
</div>

<!-- Create Template Modal -->
<div class="modal fade" id="createTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createTemplateForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Template Name *</label>
                                <input type="text" class="form-control" name="template_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Category *</label>
                                <select class="form-select" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="risk">Risk Management</option>
                                    <option value="compliance">Compliance</option>
                                    <option value="analytics">Analytics</option>
                                    <option value="workflow">Workflow</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Complexity Level</label>
                                <select class="form-select" name="complexity">
                                    <option value="simple">Simple</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Version</label>
                                <input type="text" class="form-control" name="version" value="1.0" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Components (comma-separated)</label>
                        <input type="text" class="form-control" name="components" placeholder="Forms, Charts, Tables, Workflow">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Template Source</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="source" id="fromScratch" value="scratch" checked>
                            <label class="btn btn-outline-primary" for="fromScratch">From Scratch</label>
                            
                            <input type="radio" class="btn-check" name="source" id="fromExisting" value="existing">
                            <label class="btn btn-outline-primary" for="fromExisting">Copy Existing</label>
                            
                            <input type="radio" class="btn-check" name="source" id="fromModule" value="module">
                            <label class="btn btn-outline-primary" for="fromModule">From Module</label>
                        </div>
                    </div>
                    <div id="existingTemplateSelect" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Select Template to Copy</label>
                            <select class="form-select" name="existing_template_id">
                                <option value="">Choose template...</option>
                                <option value="1">BRC Risk Assessment</option>
                                <option value="2">Analytics Dashboard</option>
                                <option value="3">Compliance Form</option>
                            </select>
                        </div>
                    </div>
                    <div id="moduleSelect" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Select Module to Convert</label>
                            <select class="form-select" name="module_id">
                                <option value="">Choose module...</option>
                                <!-- Will be populated dynamically -->
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Template</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.category-card {
    background: white;
    border: 2px solid #e3e6f0;
    border-radius: 12px;
    padding: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.category-card:hover {
    border-color: #667eea;
    transform: translateY(-2px);
}

.category-card.active {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
}

.category-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}

.category-content h5 {
    margin: 0;
    font-weight: 600;
    color: #5a5c69;
}

.category-content p {
    margin: 0;
    color: #858796;
    font-size: 0.875rem;
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

.template-item {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid #e3e6f0;
}

.template-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.3);
}

.template-preview {
    position: relative;
    height: 200px;
    overflow: hidden;
    background: #f8f9fc;
}

.template-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preview-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-size: 3rem;
}

.preview-placeholder span {
    font-size: 1rem;
    margin-top: 10px;
    font-weight: 500;
}

.template-badges {
    position: absolute;
    top: 10px;
    left: 10px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.template-actions {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex;
    gap: 8px;
}

.template-preview:hover .template-actions {
    opacity: 1;
}

.template-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.template-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    flex: 1;
}

.template-version {
    background: #e9ecef;
    color: #6c757d;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.template-description {
    color: #6c757d;
    font-size: 0.875rem;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.template-stats {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    font-size: 0.875rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 4px;
    color: #6c757d;
}

.template-components {
    border-top: 1px solid #e3e6f0;
    padding-top: 1rem;
}

.component-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}

.component-tag {
    background: #f8f9fc;
    color: #5a5c69;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    border: 1px solid #e3e6f0;
}

.empty-state {
    background: white;
    border-radius: 12px;
    border: 2px dashed #dee2e6;
    margin: 2rem 0;
}

.card {
    border: 1px solid #e3e6f0;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}
</style>

<script>
let currentCategory = 'all';

// Filter by category
function filterByCategory(category) {
    currentCategory = category;
    
    // Update active category
    document.querySelectorAll('.category-card').forEach(card => {
        card.classList.remove('active');
        if (card.dataset.category === category) {
            card.classList.add('active');
        }
    });
    
    filterTemplates();
}

// Filter templates
function filterTemplates() {
    const searchTerm = document.getElementById('searchTemplates').value.toLowerCase();
    const complexity = document.getElementById('filterComplexity').value;
    const status = document.getElementById('filterStatus').value;
    
    const templates = document.querySelectorAll('.template-card');
    let visibleCount = 0;
    
    templates.forEach(template => {
        const templateCategory = template.dataset.category;
        const templateComplexity = template.dataset.complexity;
        const templateTitle = template.querySelector('.template-title').textContent.toLowerCase();
        const templateDesc = template.querySelector('.template-description').textContent.toLowerCase();
        
        const matchesCategory = currentCategory === 'all' || templateCategory === currentCategory;
        const matchesSearch = templateTitle.includes(searchTerm) || templateDesc.includes(searchTerm);
        const matchesComplexity = !complexity || templateComplexity === complexity;
        // Status filtering would require additional data attributes
        
        if (matchesCategory && matchesSearch && matchesComplexity) {
            template.style.display = '';
            visibleCount++;
        } else {
            template.style.display = 'none';
        }
    });
    
    // Show/hide empty state
    document.getElementById('emptyState').style.display = visibleCount === 0 ? 'block' : 'none';
}

// Search functionality
document.getElementById('searchTemplates').addEventListener('input', filterTemplates);
document.getElementById('filterComplexity').addEventListener('change', filterTemplates);
document.getElementById('filterStatus').addEventListener('change', filterTemplates);

// Sort functionality
document.getElementById('sortBy').addEventListener('change', function() {
    const sortBy = this.value;
    const container = document.getElementById('templatesGrid');
    const templates = Array.from(container.querySelectorAll('.template-card'));
    
    templates.sort((a, b) => {
        let aValue, bValue;
        
        switch(sortBy) {
            case 'name':
                aValue = a.querySelector('.template-title').textContent;
                bValue = b.querySelector('.template-title').textContent;
                return aValue.localeCompare(bValue);
            case 'downloads':
                aValue = parseInt(a.querySelector('.stat-item span').textContent.replace(',', ''));
                bValue = parseInt(b.querySelector('.stat-item span').textContent.replace(',', ''));
                return bValue - aValue; // Descending
            case 'rating':
                aValue = parseFloat(a.querySelectorAll('.stat-item span')[1].textContent);
                bValue = parseFloat(b.querySelectorAll('.stat-item span')[1].textContent);
                return bValue - aValue; // Descending
            default: // date
                return 0; // Would need date data
        }
    });
    
    templates.forEach(template => container.appendChild(template));
});

// Template actions
function previewTemplate(id) {
    window.open(`/platform/templates/preview/${id}`, '_blank');
}

function useTemplate(id) {
    if (id === 0) {
        // Blank template
        window.location.href = '/platform/module-builder';
    } else {
        window.location.href = `/platform/module-builder?template=${id}`;
    }
}

function editTemplate(id) {
    window.location.href = `/platform/templates/edit/${id}`;
}

function importTemplate() {
    // Create file input dynamically
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.json,.zip';
    input.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('template_file', file);
            formData.append('action', 'import_template');
            
            fetch('/platform/ajax/templates.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Template imported successfully!', 'success');
                    location.reload();
                } else {
                    showNotification(data.message || 'Error importing template', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error importing template', 'error');
            });
        }
    };
    input.click();
}

// Create template form
document.getElementById('createTemplateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'create_template');
    
    fetch('/platform/ajax/templates.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Template created successfully!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('createTemplateModal')).hide();
            if (data.template_id) {
                window.location.href = `/platform/module-builder?template=${data.template_id}`;
            } else {
                location.reload();
            }
        } else {
            showNotification(data.message || 'Error creating template', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error creating template', 'error');
    });
});

// Source selection handling
document.querySelectorAll('input[name="source"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const existingSelect = document.getElementById('existingTemplateSelect');
        const moduleSelect = document.getElementById('moduleSelect');
        
        existingSelect.style.display = 'none';
        moduleSelect.style.display = 'none';
        
        if (this.value === 'existing') {
            existingSelect.style.display = 'block';
        } else if (this.value === 'module') {
            moduleSelect.style.display = 'block';
            loadAvailableModules();
        }
    });
});

function loadAvailableModules() {
    fetch('/platform/ajax/templates.php?action=get_modules')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.querySelector('select[name="module_id"]');
                select.innerHTML = '<option value="">Choose module...</option>';
                data.modules.forEach(module => {
                    select.innerHTML += `<option value="${module.id}">${module.name}</option>`;
                });
            }
        })
        .catch(console.error);
}

function clearFilters() {
    document.getElementById('searchTemplates').value = '';
    document.getElementById('sortBy').value = 'name';
    document.getElementById('filterComplexity').value = '';
    document.getElementById('filterStatus').value = '';
    filterByCategory('all');
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