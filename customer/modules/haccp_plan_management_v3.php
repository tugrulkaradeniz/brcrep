<?php
// HACCP Plan Management Module v3 - Enhanced Risk Assessment
// File: customer/modules/haccp_plan_management_v3.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in (fixed session variable names)
if (!isset($_SESSION['company_user_id']) || !isset($_SESSION['company_id'])) {
    header('Location: ?page=login');
    exit;
}

// Get company info for styling
$companyId = $_SESSION['company_id'];
$userId = $_SESSION['company_user_id'];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=brcload_platform;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    $companyStmt = $pdo->prepare("SELECT name, theme_color FROM companies WHERE id = ?");
    $companyStmt->execute([$companyId]);
    $company = $companyStmt->fetch();
} catch (PDOException $e) {
    $company = ['name' => 'Company', 'theme_color' => '#2c5aa0'];
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HACCP Plan Management v3 - <?php echo htmlspecialchars($company['name']); ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/brcproject/assets/css/customer.css" rel="stylesheet">
    <style>
        :root {
            --company-color: <?php echo $company['theme_color'] ?? '#2c5aa0'; ?>;
        }
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            min-height: 100vh;
        }
        .module-container { 
            background: rgba(255,255,255,0.95); 
            border-radius: 20px; 
            margin: 20px 0; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .module-header { 
            background: linear-gradient(135deg, #28a745, #20c997); 
            color: white; 
            border-radius: 20px 20px 0 0; 
            padding: 2rem; 
        }
        .section-card { 
            background: rgba(255,255,255,0.9); 
            border-radius: 15px; 
            padding: 1.5rem; 
            margin-bottom: 1.5rem; 
            border: 1px solid #e9ecef; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .risk-matrix { 
            border-collapse: collapse; 
            width: 100%; 
            max-width: 600px;
        }
        .risk-matrix td { 
            border: 1px solid #333; 
            padding: 12px; 
            text-align: center; 
            font-weight: bold; 
            min-width: 50px;
        }
        .risk-low { background: #28a745; color: white; }
        .risk-medium { background: #ffc107; color: black; }
        .risk-high { background: #dc3545; color: white; }
        .brc-badge { 
            background: linear-gradient(135deg, #28a745, #20c997); 
            color: white; 
            border-radius: 12px; 
            padding: 5px 12px; 
            font-size: 0.8rem; 
            font-weight: 600;
        }
        .btn-submit { 
            background: linear-gradient(135deg, #28a745, #20c997); 
            border: none; 
            border-radius: 25px; 
            color: white; 
            padding: 12px 24px;
            font-weight: 600;
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, #218838, #1ea085);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(40, 167, 69, 0.3);
        }
        .workflow-step { 
            background: #f8f9fa; 
            border-left: 4px solid #007bff; 
            padding: 15px; 
            margin: 10px 0; 
            border-radius: 0 8px 8px 0;
        }
        .workflow-step.completed { 
            border-left-color: #28a745; 
            background: #d4edda; 
        }
        .workflow-step.current { 
            border-left-color: #ffc107; 
            background: #fff3cd; 
        }
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-3px);
        }
        .nav-tabs .nav-link {
            border-radius: 15px 15px 0 0;
            font-weight: 600;
        }
        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border-color: transparent;
        }
        .alert {
            border-radius: 15px;
            border: none;
        }
        .table {
            border-radius: 15px;
            overflow: hidden;
        }
        .btn {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <?php include 'customer/layout/header.php'; ?>
    
    <div class="container-fluid">
        <div class="module-container">
            <div class="module-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="display-5 fw-bold mb-2">
                            <i class="fas fa-shield-check me-3"></i>HACCP Plan Management v3
                        </h1>
                        <p class="lead mb-0">Comprehensive HACCP risk assessment and compliance management for <?php echo htmlspecialchars($company['name']); ?></p>
                    </div>
                    <div class="text-end">
                        <span class="brc-badge">BRC Clause 2.1</span>
                        <div class="mt-2">
                            <small class="opacity-75">Version 3.0 | Food Safety Plan</small>
                        </div>
                        <div class="mt-3">
                            <a href="?page=marketplace" class="btn btn-light btn-sm me-2">
                                <i class="fas fa-store me-1"></i>Marketplace
                            </a>
                            <a href="?page=modules" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-th-large me-1"></i>My Modules
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4">
                <!-- Enhanced Stats Dashboard -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stats-card border-start border-primary border-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="text-primary fw-bold mb-1">12</h3>
                                    <p class="text-muted mb-0">Total Assessments</p>
                                </div>
                                <i class="fas fa-clipboard-list fa-2x text-primary opacity-75"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stats-card border-start border-danger border-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="text-danger fw-bold mb-1">3</h3>
                                    <p class="text-muted mb-0">High Risk</p>
                                </div>
                                <i class="fas fa-exclamation-triangle fa-2x text-danger opacity-75"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stats-card border-start border-warning border-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="text-warning fw-bold mb-1">5</h3>
                                    <p class="text-muted mb-0">Medium Risk</p>
                                </div>
                                <i class="fas fa-clock fa-2x text-warning opacity-75"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stats-card border-start border-success border-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="text-success fw-bold mb-1">4</h3>
                                    <p class="text-muted mb-0">Low Risk</p>
                                </div>
                                <i class="fas fa-check-circle fa-2x text-success opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Tabs Navigation -->
                <ul class="nav nav-tabs mb-4" id="moduleTab">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#newAssessment">
                            <i class="fas fa-plus-circle me-2"></i>New Risk Assessment
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#riskMatrix">
                            <i class="fas fa-th me-2"></i>Risk Matrix
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#existingRisks">
                            <i class="fas fa-list me-2"></i>Risk Register
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#reports">
                            <i class="fas fa-chart-bar me-2"></i>Reports & Analytics
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Enhanced New Risk Assessment Form -->
                    <div class="tab-pane fade show active" id="newAssessment">
                        <div class="section-card">
                            <div class="d-flex align-items-center mb-4">
                                <div class="me-3">
                                    <div class="bg-primary rounded-3 p-3">
                                        <i class="fas fa-clipboard-list text-white fa-2x"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="mb-1">HACCP Risk Assessment Form</h4>
                                    <p class="text-muted mb-0">Complete the hazard analysis for your process step according to BRC standards</p>
                                </div>
                            </div>
                            
                            <form id="riskAssessmentForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-cog text-primary me-2"></i>Process Step *
                                            </label>
                                            <select class="form-select form-select-lg" name="process_step" required>
                                                <option value="">Select process step...</option>
                                                <option value="receiving">Raw Material Receiving</option>
                                                <option value="storage">Cold Storage</option>
                                                <option value="preparation">Food Preparation</option>
                                                <option value="cooking">Cooking/Heat Treatment</option>
                                                <option value="cooling">Cooling</option>
                                                <option value="packaging">Packaging</option>
                                                <option value="dispatch">Dispatch</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-exclamation-triangle text-warning me-2"></i>Hazard Type *
                                            </label>
                                            <select class="form-select form-select-lg" name="hazard_type" required>
                                                <option value="">Select hazard type...</option>
                                                <option value="biological">🦠 Biological (Bacteria, Virus, Parasite)</option>
                                                <option value="chemical">⚗️ Chemical (Cleaning agents, Allergens)</option>
                                                <option value="physical">🔧 Physical (Metal, Glass, Plastic)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-file-alt text-info me-2"></i>Hazard Description *
                                    </label>
                                    <textarea class="form-control form-control-lg" name="hazard_description" rows="4" 
                                              placeholder="Describe the specific hazard, its source, and potential consequences in detail..." required></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-chart-line text-success me-2"></i>Likelihood (1-5) *
                                            </label>
                                            <input type="range" class="form-range mb-2" name="likelihood" min="1" max="5" value="3" 
                                                   oninput="updateLikelihoodValue(this.value)">
                                            <div class="d-flex justify-content-between small">
                                                <span class="text-muted">1 (Very Rare)</span>
                                                <span class="fw-bold text-primary" id="likelihoodValue">3 (Possible)</span>
                                                <span class="text-muted">5 (Almost Certain)</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-bolt text-danger me-2"></i>Severity (1-5) *
                                            </label>
                                            <input type="range" class="form-range mb-2" name="severity" min="1" max="5" value="3"
                                                   oninput="updateSeverityValue(this.value)">
                                            <div class="d-flex justify-content-between small">
                                                <span class="text-muted">1 (Minor)</span>
                                                <span class="fw-bold text-primary" id="severityValue">3 (Moderate)</span>
                                                <span class="text-muted">5 (Catastrophic)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info border-0 shadow-sm">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calculator fa-2x text-info me-3"></i>
                                        <div>
                                            <h5 class="mb-1">Risk Calculation</h5>
                                            <p class="mb-2">
                                                <strong>Risk Score:</strong> <span id="riskScore" class="badge bg-warning fs-6">9</span>
                                                <span id="riskLevel" class="badge bg-warning fs-6 ms-2">Medium Risk</span>
                                            </p>
                                            <small class="text-muted">Risk Score = Likelihood × Severity</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-shield-alt text-success me-2"></i>Existing Control Measures
                                    </label>
                                    <textarea class="form-control form-control-lg" name="control_measures" rows="4"
                                              placeholder="List current preventive measures, monitoring procedures, and controls in place..."></textarea>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-crosshairs text-danger me-2"></i>Critical Control Point Decision
                                    </label>
                                    <div class="mt-3">
                                        <div class="form-check form-check-lg mb-2">
                                            <input class="form-check-input" type="radio" name="ccp_decision" value="yes" id="ccpYes">
                                            <label class="form-check-label fw-bold text-danger" for="ccpYes">
                                                <i class="fas fa-check-circle me-2"></i>Yes - This is a Critical Control Point
                                            </label>
                                        </div>
                                        <div class="form-check form-check-lg">
                                            <input class="form-check-input" type="radio" name="ccp_decision" value="no" id="ccpNo" checked>
                                            <label class="form-check-label fw-bold text-muted" for="ccpNo">
                                                <i class="fas fa-times-circle me-2"></i>No - This is not a Critical Control Point
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-3">
                                    <button type="submit" class="btn btn-submit btn-lg px-5">
                                        <i class="fas fa-save me-2"></i>Save Risk Assessment
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-lg px-4" onclick="clearForm()">
                                        <i class="fas fa-times me-2"></i>Clear Form
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Enhanced Workflow Status -->
                        <div class="section-card">
                            <h5 class="mb-4">
                                <i class="fas fa-route text-primary me-2"></i>HACCP Approval Workflow
                            </h5>
                            <div class="workflow-step completed">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-3 fa-lg"></i>
                                    <div>
                                        <strong>Step 1: Risk Assessment Entry</strong>
                                        <br><small class="text-muted">Completed by Quality Team • <?= date('M d, Y H:i') ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="workflow-step current">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-clock text-warning me-3 fa-lg"></i>
                                    <div>
                                        <strong>Step 2: Technical Review</strong>
                                        <br><small class="text-muted">Pending Food Safety Manager approval</small>
                                    </div>
                                </div>
                            </div>
                            <div class="workflow-step">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-circle text-muted me-3 fa-lg"></i>
                                    <div>
                                        <strong>Step 3: Final Approval</strong>
                                        <br><small class="text-muted">HACCP Team Leader review</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Risk Matrix -->
                    <div class="tab-pane fade" id="riskMatrix">
                        <div class="section-card">
                            <div class="d-flex align-items-center mb-4">
                                <div class="me-3">
                                    <div class="bg-danger rounded-3 p-3">
                                        <i class="fas fa-th text-white fa-2x"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="mb-1">HACCP Risk Matrix (5x5)</h4>
                                    <p class="text-muted mb-0">Visual representation of all assessed risks with BRC compliance levels</p>
                                </div>
                            </div>
                            
                            <div class="text-center mb-4">
                                <table class="risk-matrix mx-auto shadow-lg">
                                    <tr>
                                        <td rowspan="6" class="align-middle bg-light text-dark">
                                            <strong>LIKELIHOOD</strong>
                                        </td>
                                        <td class="risk-high text-center fw-bold">5</td>
                                        <td class="risk-medium">5</td>
                                        <td class="risk-medium">10</td>
                                        <td class="risk-high">15</td>
                                        <td class="risk-high">20</td>
                                        <td class="risk-high">25</td>
                                    </tr>
                                    <tr>
                                        <td class="risk-medium text-center fw-bold">4</td>
                                        <td class="risk-low">4</td>
                                        <td class="risk-medium">8</td>
                                        <td class="risk-medium">12</td>
                                        <td class="risk-high">16</td>
                                        <td class="risk-high">20</td>
                                    </tr>
                                    <tr>
                                        <td class="risk-medium text-center fw-bold">3</td>
                                        <td class="risk-low">3</td>
                                        <td class="risk-low">6</td>
                                        <td class="risk-medium">9 ●</td>
                                        <td class="risk-medium">12</td>
                                        <td class="risk-high">15</td>
                                    </tr>
                                    <tr>
                                        <td class="risk-low text-center fw-bold">2</td>
                                        <td class="risk-low">2</td>
                                        <td class="risk-low">4</td>
                                        <td class="risk-low">6</td>
                                        <td class="risk-medium">8</td>
                                        <td class="risk-medium">10</td>
                                    </tr>
                                    <tr>
                                        <td class="risk-low text-center fw-bold">1</td>
                                        <td class="risk-low">1</td>
                                        <td class="risk-low">2</td>
                                        <td class="risk-low">3</td>
                                        <td class="risk-low">4</td>
                                        <td class="risk-low">5</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light"></td>
                                        <td class="bg-light fw-bold">1</td>
                                        <td class="bg-light fw-bold">2</td>
                                        <td class="bg-light fw-bold">3</td>
                                        <td class="bg-light fw-bold">4</td>
                                        <td class="bg-light fw-bold">5</td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="bg-light text-center">
                                            <strong>SEVERITY</strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-4">
                                    <div class="alert alert-success border-0">
                                        <h6 class="fw-bold"><i class="fas fa-check-circle me-2"></i>Low Risk (1-8)</h6>
                                        <p class="mb-0">Acceptable risk level - Routine monitoring required</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="alert alert-warning border-0">
                                        <h6 class="fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Medium Risk (9-15)</h6>
                                        <p class="mb-0">Requires control measures - Enhanced monitoring</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="alert alert-danger border-0">
                                        <h6 class="fw-bold"><i class="fas fa-crosshairs me-2"></i>High Risk (16-25)</h6>
                                        <p class="mb-0">Critical Control Point required - Immediate action</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Existing Risks -->
                    <div class="tab-pane fade" id="existingRisks">
                        <div class="section-card">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="bg-info rounded-3 p-3">
                                            <i class="fas fa-list text-white fa-2x"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="mb-1">Current Risk Register</h4>
                                        <p class="text-muted mb-0">Comprehensive list of all identified hazards and control measures</p>
                                    </div>
                                </div>
                                <button class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Add New Risk
                                </button>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Risk ID</th>
                                            <th>Process Step</th>
                                            <th>Hazard Description</th>
                                            <th>Type</th>
                                            <th>Risk Score</th>
                                            <th>CCP</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong class="text-primary">RA-001</strong></td>
                                            <td>Raw Material Receiving</td>
                                            <td>Salmonella contamination in poultry</td>
                                            <td><span class="badge bg-danger">Biological</span></td>
                                            <td><span class="badge bg-danger fs-6">20</span></td>
                                            <td><i class="fas fa-check-circle text-success fa-lg"></i></td>
                                            <td><span class="badge bg-success">Approved</span></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-info" title="Download Report">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong class="text-primary">RA-002</strong></td>
                                            <td>Cooking Process</td>
                                            <td>Insufficient heat treatment</td>
                                            <td><span class="badge bg-danger">Biological</span></td>
                                            <td><span class="badge bg-danger fs-6">16</span></td>
                                            <td><i class="fas fa-check-circle text-success fa-lg"></i></td>
                                            <td><span class="badge bg-warning">Pending Review</span></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong class="text-primary">RA-003</strong></td>
                                            <td>Packaging Line</td>
                                            <td>Metal contamination from equipment</td>
                                            <td><span class="badge bg-info">Physical</span></td>
                                            <td><span class="badge bg-warning fs-6">12</span></td>
                                            <td><i class="fas fa-times-circle text-danger fa-lg"></i></td>
                                            <td><span class="badge bg-success">Approved</span></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Reports -->
                    <div class="tab-pane fade" id="reports">
                        <div class="section-card">
                            <div class="d-flex align-items-center mb-4">
                                <div class="me-3">
                                    <div class="bg-warning rounded-3 p-3">
                                        <i class="fas fa-chart-bar text-white fa-2x"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="mb-1">HACCP Reports & Analytics</h4>
                                    <p class="text-muted mb-0">Comprehensive reporting suite for BRC compliance and audit preparation</p>
                                </div>
                            </div>
                            
                            <div class="row g-4">
                                <div class="col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body text-center p-4">
                                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                                <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                            </div>
                                            <h5 class="card-title">Risk Assessment Report</h5>
                                            <p class="card-text text-muted">Complete HACCP analysis with risk matrix and CCP documentation</p>
                                            <div class="mt-auto">
                                                <button class="btn btn-danger w-100" onclick="generateReport('pdf')">
                                                    <i class="fas fa-download me-2"></i>Generate PDF Report
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body text-center p-4">
                                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                                <i class="fas fa-crosshairs fa-2x text-success"></i>
                                            </div>
                                            <h5 class="card-title">CCP Summary</h5>
                                            <p class="card-text text-muted">Critical Control Points list with monitoring procedures</p>
                                            <div class="mt-auto">
                                                <button class="btn btn-success w-100" onclick="generateReport('ccp')">
                                                    <i class="fas fa-download me-2"></i>Export CCP List
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body text-center p-4">
                                            <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                                <i class="fas fa-chart-line fa-2x text-info"></i>
                                            </div>
                                            <h5 class="card-title">Trend Analysis</h5>
                                            <p class="card-text text-muted">Risk trends over time with predictive analytics</p>
                                            <div class="mt-auto">
                                                <button class="btn btn-info w-100" onclick="viewDashboard()">
                                                    <i class="fas fa-eye me-2"></i>View Dashboard
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Reports Row -->
                            <div class="row g-4 mt-2">
                                <div class="col-lg-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-calendar-alt fa-2x text-primary me-3"></i>
                                                <div>
                                                    <h6 class="card-title mb-1">Compliance Calendar</h6>
                                                    <p class="card-text text-muted mb-0">Schedule and track all HACCP review dates</p>
                                                </div>
                                                <button class="btn btn-outline-primary ms-auto">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-users fa-2x text-warning me-3"></i>
                                                <div>
                                                    <h6 class="card-title mb-1">Training Records</h6>
                                                    <p class="card-text text-muted mb-0">HACCP team competency and training matrix</p>
                                                </div>
                                                <button class="btn btn-outline-warning ms-auto">
                                                    <i class="fas fa-external-link-alt"></i>
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
    </div>

    <?php include 'customer/layout/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enhanced risk calculation functions
        function updateLikelihoodValue(value) {
            const labels = ['', 'Very Rare', 'Rare', 'Possible', 'Likely', 'Almost Certain'];
            document.getElementById('likelihoodValue').textContent = value + ' (' + labels[value] + ')';
            calculateRisk();
        }

        function updateSeverityValue(value) {
            const labels = ['', 'Minor', 'Moderate', 'Major', 'Severe', 'Catastrophic'];
            document.getElementById('severityValue').textContent = value + ' (' + labels[value] + ')';
            calculateRisk();
        }

        function calculateRisk() {
            const likelihood = document.querySelector('input[name="likelihood"]').value;
            const severity = document.querySelector('input[name="severity"]').value;
            const riskScore = likelihood * severity;
            
            document.getElementById('riskScore').textContent = riskScore;
            
            let level, color;
            if (riskScore <= 8) {
                level = 'Low Risk';
                color = 'bg-success';
            } else if (riskScore <= 15) {
                level = 'Medium Risk';
                color = 'bg-warning';
            } else {
                level = 'High Risk';
                color = 'bg-danger';
            }
            
            const riskLevelElement = document.getElementById('riskLevel');
            riskLevelElement.textContent = level;
            riskLevelElement.className = 'badge fs-6 ms-2 ' + color;
        }

        function clearForm() {
            document.getElementById('riskAssessmentForm').reset();
            calculateRisk();
        }

        // Enhanced form submission with better UX
        document.getElementById('riskAssessmentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving Assessment...';
            submitBtn.disabled = true;
            
            // Simulate API call
            setTimeout(() => {
                // Show success message
                showNotification('Risk assessment saved successfully!', 'success');
                
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                // Clear form
                clearForm();
                
                // Switch to existing risks tab to show the new entry
                // document.querySelector('a[href="#existingRisks"]').click();
            }, 2000);
        });

        // Report generation functions
        function generateReport(type) {
            let message;
            switch(type) {
                case 'pdf':
                    message = 'Generating comprehensive PDF risk assessment report...';
                    break;
                case 'ccp':
                    message = 'Exporting Critical Control Points summary...';
                    break;
                default:
                    message = 'Generating report...';
            }
            showNotification(message, 'info');
        }

        function viewDashboard() {
            showNotification('Opening risk trend analytics dashboard...', 'info');
        }

        // Notification system
        function showNotification(message, type = 'info') {
            const alertClass = `alert-${type}`;
            const notification = document.createElement('div');
            notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            calculateRisk();
            
            // Add smooth transitions
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function() {
                    setTimeout(() => {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }, 100);
                });
            });
            
            console.log('HACCP Plan Management v3 module loaded successfully');
        });
    </script>
</body>
</html>