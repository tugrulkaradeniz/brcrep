<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRC Risk Assessment - Demo Company</title>
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

        .module-header {
            background: linear-gradient(135deg, var(--primary-color), #0056b3);
            color: white;
            padding: 2rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .module-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(45deg);
        }

        .module-header .module-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .component-container {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
        }

        .component-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--light-bg);
        }

        .component-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
            display: flex;
            align-items: center;
        }

        .component-title i {
            margin-right: 0.75rem;
            color: var(--primary-color);
        }

        /* Risk Matrix Styling */
        .risk-matrix {
            display: grid;
            grid-template-columns: auto repeat(5, 1fr);
            grid-template-rows: auto repeat(5, 1fr);
            gap: 1px;
            background: var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            margin: 1rem 0;
        }

        .risk-matrix .header-cell {
            background: var(--dark-color);
            color: white;
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .risk-matrix .label-cell {
            background: var(--light-bg);
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: var(--dark-color);
        }

        .risk-matrix .risk-cell {
            background: white;
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .risk-matrix .risk-cell:hover {
            transform: scale(1.05);
            z-index: 10;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .risk-matrix .risk-cell.low { background: #22c55e; }
        .risk-matrix .risk-cell.medium { background: #f59e0b; }
        .risk-matrix .risk-cell.high { background: #ef4444; }
        .risk-matrix .risk-cell.selected {
            box-shadow: 0 0 0 3px var(--primary-color);
            transform: scale(1.1);
        }

        /* Smart Form Styling */
        .smart-form {
            background: var(--light-bg);
            border-radius: 12px;
            padding: 2rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .form-group .required {
            color: var(--danger-color);
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 2px solid var(--border-color);
            padding: 0.75rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: var(--danger-color);
        }

        .invalid-feedback {
            display: block;
            color: var(--danger-color);
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        /* Status Tracker */
        .status-tracker {
            display: flex;
            justify-content: space-between;
            margin: 2rem 0;
            position: relative;
        }

        .status-tracker::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--border-color);
            z-index: 1;
        }

        .status-step {
            background: white;
            border: 3px solid var(--border-color);
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 2;
            transition: all 0.3s ease;
        }

        .status-step.completed {
            background: var(--success-color);
            border-color: var(--success-color);
            color: white;
        }

        .status-step.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            animation: pulse 2s infinite;
        }

        .status-step.pending {
            background: white;
            border-color: var(--border-color);
            color: var(--secondary-color);
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(0, 123, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
        }

        .step-label {
            position: absolute;
            top: 70px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--dark-color);
            text-align: center;
            white-space: nowrap;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
        }

        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #0056b3);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
        }

        .btn-outline-secondary {
            border: 2px solid var(--border-color);
            color: var(--secondary-color);
        }

        /* Risk Details Modal */
        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            background: var(--light-bg);
            border-bottom: 1px solid var(--border-color);
            border-radius: 16px 16px 0 0;
        }

        /* Risk Score Display */
        .risk-score-display {
            background: linear-gradient(135deg, var(--danger-color), #ff6b6b);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
            margin: 1rem 0;
        }

        .risk-score-number {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .risk-score-label {
            font-size: 1.25rem;
            font-weight: 600;
            opacity: 0.9;
        }

        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .dashboard-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .dashboard-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }

        .dashboard-card h6 {
            color: var(--secondary-color);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .dashboard-card h3 {
            color: var(--dark-color);
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .progress-thin {
            height: 4px;
            border-radius: 2px;
            margin-top: 1rem;
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
            <a class="nav-link" href="?page=marketplace">
                <i class="fas fa-store me-2"></i>Module Marketplace
            </a>
            <a class="nav-link" href="?page=modules">
                <i class="fas fa-puzzle-piece me-2"></i>My Modules
            </a>
            <div class="mt-3">
                <small class="text-white-50 px-3 text-uppercase">Active Modules</small>
                <a class="nav-link active" href="?module=brc_risk_assessment">
                    <i class="fas fa-shield-alt me-2"></i>Risk Assessment
                </a>
                <a class="nav-link" href="?module=quality_mgmt">
                    <i class="fas fa-award me-2"></i>Quality Management
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
        <!-- Module Header -->
        <div class="module-header">
            <div class="module-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h1 class="mb-2">BRC Risk Assessment</h1>
            <p class="mb-0 opacity-75">Comprehensive BRC-compliant risk assessment and management system</p>
            <div class="position-absolute top-0 end-0 p-3">
                <span class="badge bg-success fs-6">v2.1</span>
            </div>
        </div>

        <!-- Dashboard Overview -->
        <div class="dashboard-cards">
            <div class="dashboard-card">
                <div class="dashboard-card-icon bg-primary text-white">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h6>Total Risks</h6>
                <h3>47</h3>
                <div class="d-flex justify-content-between small text-muted">
                    <span>High: 8</span>
                    <span>Medium: 23</span>
                    <span>Low: 16</span>
                </div>
                <div class="progress progress-thin">
                    <div class="progress-bar bg-danger" style="width: 17%"></div>
                    <div class="progress-bar bg-warning" style="width: 49%"></div>
                    <div class="progress-bar bg-success" style="width: 34%"></div>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="dashboard-card-icon bg-success text-white">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h6>Completed Assessments</h6>
                <h3>34</h3>
                <small class="text-success">
                    <i class="fas fa-arrow-up"></i> +12% this month
                </small>
            </div>

            <div class="dashboard-card">
                <div class="dashboard-card-icon bg-warning text-white">
                    <i class="fas fa-clock"></i>
                </div>
                <h6>Pending Reviews</h6>
                <h3>8</h3>
                <small class="text-warning">
                    <i class="fas fa-exclamation"></i> 3 overdue
                </small>
            </div>

            <div class="dashboard-card">
                <div class="dashboard-card-icon bg-info text-white">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h6>Compliance Score</h6>
                <h3>94%</h3>
                <small class="text-success">
                    <i class="fas fa-arrow-up"></i> +2% this week
                </small>
            </div>
        </div>

        <!-- Risk Assessment Form -->
        <div class="component-container">
            <div class="component-header">
                <div class="component-title">
                    <i class="fas fa-edit"></i>
                    New Risk Assessment
                </div>
                <button class="btn btn-outline-primary btn-sm" onclick="loadTemplate()">
                    <i class="fas fa-download me-2"></i>Load Template
                </button>
            </div>

            <form id="riskAssessmentForm" class="smart-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>Risk Area <span class="required">*</span></label>
                        <select class="form-select" name="risk_area" required>
                            <option value="">Select Risk Area</option>
                            <option value="food_safety">Food Safety</option>
                            <option value="security">Security</option>
                            <option value="quality">Quality</option>
                            <option value="environment">Environment</option>
                            <option value="personnel">Personnel</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Risk Category <span class="required">*</span></label>
                        <select class="form-select" name="risk_category" required>
                            <option value="">Select Category</option>
                            <option value="biological">Biological</option>
                            <option value="chemical">Chemical</option>
                            <option value="physical">Physical</option>
                            <option value="allergenic">Allergenic</option>
                            <option value="operational">Operational</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Risk Description <span class="required">*</span></label>
                        <textarea class="form-control" name="risk_description" rows="3" placeholder="Describe the identified risk..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Location/Process</label>
                        <input type="text" class="form-control" name="location" placeholder="e.g., Production Line 1, Cold Storage">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Likelihood <span class="required">*</span></label>
                        <select class="form-select" name="likelihood" required onchange="calculateRisk()">
                            <option value="">Select Likelihood</option>
                            <option value="1">1 - Very Unlikely (< 1%)</option>
                            <option value="2">2 - Unlikely (1-10%)</option>
                            <option value="3">3 - Possible (10-50%)</option>
                            <option value="4">4 - Likely (50-90%)</option>
                            <option value="5">5 - Very Likely (> 90%)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Impact/Severity <span class="required">*</span></label>
                        <select class="form-select" name="impact" required onchange="calculateRisk()">
                            <option value="">Select Impact</option>
                            <option value="1">1 - Negligible</option>
                            <option value="2">2 - Minor</option>
                            <option value="3">3 - Moderate</option>
                            <option value="4">4 - Major</option>
                            <option value="5">5 - Catastrophic</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Existing Controls</label>
                        <textarea class="form-control" name="existing_controls" rows="2" placeholder="Describe current control measures..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Proposed Actions</label>
                        <textarea class="form-control" name="proposed_actions" rows="2" placeholder="Describe proposed mitigation actions..."></textarea>
                    </div>
                </div>
            </form>
        </div>

        <!-- Risk Matrix Display -->
        <div class="component-container">
            <div class="component-header">
                <div class="component-title">
                    <i class="fas fa-th"></i>
                    Risk Assessment Matrix
                </div>
                <div class="d-flex align-items-center">
                    <span class="text-muted me-3">Current Selection:</span>
                    <div id="currentRiskScore" class="badge bg-secondary">No selection</div>
                </div>
            </div>

            <div class="risk-matrix">
                <!-- Headers -->
                <div class="header-cell"></div>
                <div class="header-cell">1 - Negligible</div>
                <div class="header-cell">2 - Minor</div>
                <div class="header-cell">3 - Moderate</div>
                <div class="header-cell">4 - Major</div>
                <div class="header-cell">5 - Catastrophic</div>

                <!-- Row 5 - Very Likely -->
                <div class="label-cell">5 - Very Likely</div>
                <div class="risk-cell medium" data-risk="5" onclick="selectRisk(5, 1)">5</div>
                <div class="risk-cell high" data-risk="10" onclick="selectRisk(5, 2)">10</div>
                <div class="risk-cell high" data-risk="15" onclick="selectRisk(5, 3)">15</div>
                <div class="risk-cell high" data-risk="20" onclick="selectRisk(5, 4)">20</div>
                <div class="risk-cell high" data-risk="25" onclick="selectRisk(5, 5)">25</div>

                <!-- Row 4 - Likely -->
                <div class="label-cell">4 - Likely</div>
                <div class="risk-cell medium" data-risk="4" onclick="selectRisk(4, 1)">4</div>
                <div class="risk-cell high" data-risk="8" onclick="selectRisk(4, 2)">8</div>
                <div class="risk-cell high" data-risk="12" onclick="selectRisk(4, 3)">12</div>
                <div class="risk-cell high" data-risk="16" onclick="selectRisk(4, 4)">16</div>
                <div class="risk-cell high" data-risk="20" onclick="selectRisk(4, 5)">20</div>

                <!-- Row 3 - Possible -->
                <div class="label-cell">3 - Possible</div>
                <div class="risk-cell medium" data-risk="3" onclick="selectRisk(3, 1)">3</div>
                <div class="risk-cell medium" data-risk="6" onclick="selectRisk(3, 2)">6</div>
                <div class="risk-cell high" data-risk="9" onclick="selectRisk(3, 3)">9</div>
                <div class="risk-cell high" data-risk="12" onclick="selectRisk(3, 4)">12</div>
                <div class="risk-cell high" data-risk="15" onclick="selectRisk(3, 5)">15</div>

                <!-- Row 2 - Unlikely -->
                <div class="label-cell">2 - Unlikely</div>
                <div class="risk-cell low" data-risk="2" onclick="selectRisk(2, 1)">2</div>
                <div class="risk-cell medium" data-risk="4" onclick="selectRisk(2, 2)">4</div>
                <div class="risk-cell medium" data-risk="6" onclick="selectRisk(2, 3)">6</div>
                <div class="risk-cell high" data-risk="8" onclick="selectRisk(2, 4)">8</div>
                <div class="risk-cell high" data-risk="10" onclick="selectRisk(2, 5)">10</div>

                <!-- Row 1 - Very Unlikely -->
                <div class="label-cell">1 - Very Unlikely</div>
                <div class="risk-cell low" data-risk="1" onclick="selectRisk(1, 1)">1</div>
                <div class="risk-cell low" data-risk="2" onclick="selectRisk(1, 2)">2</div>
                <div class="risk-cell medium" data-risk="3" onclick="selectRisk(1, 3)">3</div>
                <div class="risk-cell medium" data-risk="4" onclick="selectRisk(1, 4)">4</div>
                <div class="risk-cell medium" data-risk="5" onclick="selectRisk(1, 5)">5</div>
            </div>

            <!-- Risk Score Display -->
            <div id="riskScoreDisplay" class="risk-score-display" style="display: none;">
                <div class="risk-score-number" id="riskScoreNumber">0</div>
                <div class="risk-score-label" id="riskScoreLabel">Risk Score</div>
                <div class="mt-2">
                    <small id="riskScoreDesc">Select a cell in the matrix above</small>
                </div>
            </div>
        </div>

        <!-- Workflow Status -->
        <div class="component-container">
            <div class="component-header">
                <div class="component-title">
                    <i class="fas fa-sitemap"></i>
                    Approval Workflow
                </div>
                <div class="text-muted small">Current Status: <strong>Draft</strong></div>
            </div>

            <div class="status-tracker">
                <div class="status-step completed">
                    <i class="fas fa-edit"></i>
                    <div class="step-label">Risk<br>Assessment</div>
                </div>
                <div class="status-step active">
                    <i class="fas fa-user-check"></i>
                    <div class="step-label">Supervisor<br>Review</div>
                </div>
                <div class="status-step pending">
                    <i class="fas fa-shield-alt"></i>
                    <div class="step-label">Safety<br>Manager</div>
                </div>
                <div class="status-step pending">
                    <i class="fas fa-check-circle"></i>
                    <div class="step-label">Final<br>Approval</div>
                </div>
                <div class="status-step pending">
                    <i class="fas fa-archive"></i>
                    <div class="step-label">Archive</div>
                </div>
            </div>

            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Next Action:</strong> Submit for supervisor review. Assigned to: <strong>John Smith</strong>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button type="button" class="btn btn-outline-secondary" onclick="saveDraft()">
                <i class="fas fa-save me-2"></i>Save Draft
            </button>
            <button type="button" class="btn btn-outline-primary" onclick="previewAssessment()">
                <i class="fas fa-eye me-2"></i>Preview
            </button>
            <button type="button" class="btn btn-primary" onclick="submitForReview()">
                <i class="fas fa-paper-plane me-2"></i>Submit for Review
            </button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedRiskScore = 0;
        let selectedLikelihood = 0;
        let selectedImpact = 0;

        // Calculate risk score based on form inputs
        function calculateRisk() {
            const likelihood = parseInt(document.querySelector('select[name="likelihood"]').value) || 0;
            const impact = parseInt(document.querySelector('select[name="impact"]').value) || 0;
            
            if (likelihood && impact) {
                selectedLikelihood = likelihood;
                selectedImpact = impact;
                selectRisk(likelihood, impact);
            }
        }

        // Select risk cell in matrix
        function selectRisk(likelihood, impact) {
            // Clear previous selection
            document.querySelectorAll('.risk-cell').forEach(cell => {
                cell.classList.remove('selected');
            });
            
            // Calculate risk score
            const riskScore = likelihood * impact;
            selectedRiskScore = riskScore;
            
            // Find and select the cell
            const targetCell = document.querySelector(`[data-risk="${riskScore}"]`);
            if (targetCell) {
                targetCell.classList.add('selected');
            }
            
            // Update form values
            document.querySelector('select[name="likelihood"]').value = likelihood;
            document.querySelector('select[name="impact"]').value = impact;
            
            // Update risk score display
            updateRiskScoreDisplay(riskScore);
            
            // Update current selection badge
            const riskLevel = getRiskLevel(riskScore);
            const badge = document.getElementById('currentRiskScore');
            badge.textContent = `Risk Score: ${riskScore} (${riskLevel})`;
            badge.className = `badge bg-${getRiskLevelColor(riskLevel)}`;
        }

        // Update risk score display
        function updateRiskScoreDisplay(score) {
            const display = document.getElementById('riskScoreDisplay');
            const numberEl = document.getElementById('riskScoreNumber');
            const labelEl = document.getElementById('riskScoreLabel');
            const descEl = document.getElementById('riskScoreDesc');
            
            if (score > 0) {
                display.style.display = 'block';
                numberEl.textContent = score;
                
                const level = getRiskLevel(score);
                labelEl.textContent = `${level} Risk`;
                descEl.textContent = getRiskDescription(score);
                
                // Update background color
                display.className = `risk-score-display bg-${getRiskLevelColor(level)}`;
            } else {
                display.style.display = 'none';
            }
        }

        // Get risk level based on score
        function getRiskLevel(score) {
            if (score >= 15) return 'High';
            if (score >= 6) return 'Medium';
            return 'Low';
        }

        // Get risk level color
        function getRiskLevelColor(level) {
            switch (level) {
                case 'High': return 'danger';
                case 'Medium': return 'warning';
                case 'Low': return 'success';
                default: return 'secondary';
            }
        }

        // Get risk description
        function getRiskDescription(score) {
            if (score >= 20) return 'Extreme risk - Immediate action required';
            if (score >= 15) return 'High risk - Urgent action required';
            if (score >= 10) return 'Significant risk - Action required';
            if (score >= 6) return 'Moderate risk - Monitor and review';
            if (score >= 3) return 'Low risk - Acceptable with controls';
            return 'Very low risk - Minimal controls required';
        }

        // Save draft function
        function saveDraft() {
            const formData = new FormData(document.getElementById('riskAssessmentForm'));
            formData.append('action', 'save_draft');
            formData.append('risk_score', selectedRiskScore);
            formData.append('likelihood', selectedLikelihood);
            formData.append('impact', selectedImpact);
            
            // Simulate save
            console.log('Saving draft:', Object.fromEntries(formData));
            
            // Show success message
            showAlert('Draft saved successfully!', 'success');
        }

        // Submit for review function
        function submitForReview() {
            // Validate form
            const form = document.getElementById('riskAssessmentForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            if (selectedRiskScore === 0) {
                showAlert('Please complete the risk assessment matrix.', 'warning');
                return;
            }
            
            if (confirm('Are you sure you want to submit this risk assessment for review?')) {
                const formData = new FormData(form);
                formData.append('action', 'submit_for_review');
                formData.append('risk_score', selectedRiskScore);
                formData.append('likelihood', selectedLikelihood);
                formData.append('impact', selectedImpact);
                
                // Simulate submission
                console.log('Submitting for review:', Object.fromEntries(formData));
                
                // Update workflow status
                updateWorkflowStatus();
                
                showAlert('Risk assessment submitted for review successfully!', 'success');
            }
        }

        // Preview assessment
        function previewAssessment() {
            // Create preview modal or redirect
            console.log('Opening preview...');
            showAlert('Preview functionality would open here', 'info');
        }

        // Load template
        function loadTemplate() {
            if (confirm('This will replace the current form data. Continue?')) {
                // Load template data
                document.querySelector('select[name="risk_area"]').value = 'food_safety';
                document.querySelector('select[name="risk_category"]').value = 'biological';
                document.querySelector('textarea[name="risk_description"]').value = 'Potential bacterial contamination in raw material storage area';
                document.querySelector('input[name="location"]').value = 'Cold Storage Room A';
                document.querySelector('select[name="likelihood"]').value = '3';
                document.querySelector('select[name="impact"]').value = '4';
                document.querySelector('textarea[name="existing_controls"]').value = 'Temperature monitoring, regular cleaning schedule';
                document.querySelector('textarea[name="proposed_actions"]').value = 'Install additional temperature sensors, increase cleaning frequency';
                
                // Calculate risk
                calculateRisk();
                
                showAlert('Template loaded successfully!', 'success');
            }
        }

        // Update workflow status
        function updateWorkflowStatus() {
            const steps = document.querySelectorAll('.status-step');
            steps[1].classList.remove('active');
            steps[1].classList.add('completed');
            steps[2].classList.add('active');
            
            // Update next action
            const alert = document.querySelector('.alert-info');
            alert.innerHTML = `
                <i class="fas fa-info-circle me-2"></i>
                <strong>Next Action:</strong> Safety manager review. Assigned to: <strong>Sarah Wilson</strong>
            `;
        }

        // Show alert message
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(alertDiv);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Form validation
        document.getElementById('riskAssessmentForm').addEventListener('input', function(e) {
            const target = e.target;
            if (target.required && target.value.trim() === '') {
                target.classList.add('is-invalid');
            } else {
                target.classList.remove('is-invalid');
            }
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            console.log('BRC Risk Assessment Module loaded');
            
            // Auto-save every 2 minutes
            setInterval(() => {
                const form = document.getElementById('riskAssessmentForm');
                const formData = new FormData(form);
                
                // Check if form has any data
                let hasData = false;
                for (let [key, value] of formData.entries()) {
                    if (value.trim() !== '') {
                        hasData = true;
                        break;
                    }
                }
                
                if (hasData) {
                    console.log('Auto-saving draft...');
                    // Here you would make an AJAX call to save
                }
            }, 120000); // 2 minutes
        });
    </script>
</body>
</html>