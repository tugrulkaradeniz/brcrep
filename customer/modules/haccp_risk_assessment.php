<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HACCP Risk Assessment - Working Module</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .module-container { background: rgba(255,255,255,0.95); border-radius: 20px; margin: 20px 0; }
        .module-header { background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; border-radius: 20px 20px 0 0; padding: 1.5rem; }
        .section-card { background: rgba(255,255,255,0.9); border-radius: 15px; padding: 1.5rem; margin-bottom: 1.5rem; border: 1px solid #e9ecef; }
        .risk-matrix { border-collapse: collapse; width: 100%; }
        .risk-matrix td { border: 1px solid #333; padding: 8px; text-align: center; font-weight: bold; }
        .risk-low { background: #28a745; color: white; }
        .risk-medium { background: #ffc107; color: black; }
        .risk-high { background: #dc3545; color: white; }
        .brc-badge { background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; border-radius: 12px; padding: 3px 8px; font-size: 0.75rem; }
        .btn-submit { background: linear-gradient(135deg, #28a745, #20c997); border: none; border-radius: 25px; color: white; }
        .workflow-step { background: #f8f9fa; border-left: 4px solid #007bff; padding: 15px; margin: 10px 0; }
        .workflow-step.completed { border-left-color: #28a745; background: #d4edda; }
        .workflow-step.current { border-left-color: #ffc107; background: #fff3cd; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="module-container">
            <div class="module-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2><i class="fas fa-shield-check me-3"></i>HACCP Risk Assessment</h2>
                        <p class="mb-0">Systematic hazard identification and risk evaluation</p>
                    </div>
                    <div class="text-end">
                        <span class="brc-badge">BRC Clause 2.1</span>
                        <div class="mt-2">
                            <small>Version 3.0 | Food Safety Plan</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4">
                <!-- Quick Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-center border-0 bg-primary text-white">
                            <div class="card-body">
                                <h3>12</h3>
                                <small>Total Assessments</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-0 bg-danger text-white">
                            <div class="card-body">
                                <h3>3</h3>
                                <small>High Risk</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-0 bg-warning text-white">
                            <div class="card-body">
                                <h3>5</h3>
                                <small>Medium Risk</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-0 bg-success text-white">
                            <div class="card-body">
                                <h3>4</h3>
                                <small>Low Risk</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs Navigation -->
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
                            <i class="fas fa-list me-2"></i>Existing Risks
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#reports">
                            <i class="fas fa-chart-bar me-2"></i>Reports
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- New Risk Assessment Form -->
                    <div class="tab-pane fade show active" id="newAssessment">
                        <div class="section-card">
                            <h4><i class="fas fa-clipboard-list text-danger me-2"></i>HACCP Risk Assessment Form</h4>
                            <p class="text-muted">Complete the hazard analysis for your process step</p>
                            
                            <form id="riskAssessmentForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Process Step *</strong></label>
                                            <select class="form-select" name="process_step" required>
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
                                            <label class="form-label"><strong>Hazard Type *</strong></label>
                                            <select class="form-select" name="hazard_type" required>
                                                <option value="">Select hazard type...</option>
                                                <option value="biological">Biological (Bacteria, Virus, Parasite)</option>
                                                <option value="chemical">Chemical (Cleaning agents, Allergens)</option>
                                                <option value="physical">Physical (Metal, Glass, Plastic)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><strong>Hazard Description *</strong></label>
                                    <textarea class="form-control" name="hazard_description" rows="3" 
                                              placeholder="Describe the specific hazard, its source, and potential consequences..." required></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Likelihood (1-5) *</strong></label>
                                            <input type="range" class="form-range" name="likelihood" min="1" max="5" value="3" 
                                                   oninput="updateLikelihoodValue(this.value)">
                                            <div class="d-flex justify-content-between">
                                                <small>1 (Very Rare)</small>
                                                <small><strong id="likelihoodValue">3 (Possible)</strong></small>
                                                <small>5 (Almost Certain)</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Severity (1-5) *</strong></label>
                                            <input type="range" class="form-range" name="severity" min="1" max="5" value="3"
                                                   oninput="updateSeverityValue(this.value)">
                                            <div class="d-flex justify-content-between">
                                                <small>1 (Minor)</small>
                                                <small><strong id="severityValue">3 (Moderate)</strong></small>
                                                <small>5 (Catastrophic)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <strong>Risk Score:</strong> <span id="riskScore" class="badge bg-warning">9</span>
                                    <span id="riskLevel" class="badge bg-warning">Medium Risk</span>
                                    <div class="mt-2">
                                        <small>Risk Score = Likelihood × Severity</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><strong>Existing Control Measures</strong></label>
                                    <textarea class="form-control" name="control_measures" rows="3"
                                              placeholder="List current preventive measures, monitoring procedures, and controls in place..."></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><strong>Critical Control Point Decision</strong></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ccp_decision" value="yes" id="ccpYes">
                                        <label class="form-check-label" for="ccpYes">
                                            <strong>Yes</strong> - This is a Critical Control Point
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ccp_decision" value="no" id="ccpNo" checked>
                                        <label class="form-check-label" for="ccpNo">
                                            <strong>No</strong> - This is not a Critical Control Point
                                        </label>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-submit px-4">
                                        <i class="fas fa-save me-2"></i>Save Risk Assessment
                                    </button>
                                    <button type="button" class="btn btn-secondary px-4">
                                        <i class="fas fa-times me-2"></i>Clear Form
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Workflow Status -->
                        <div class="section-card">
                            <h5><i class="fas fa-route text-primary me-2"></i>Approval Workflow</h5>
                            <div class="workflow-step completed">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <strong>Step 1: Risk Assessment Entry</strong> - Completed by Quality Team
                            </div>
                            <div class="workflow-step current">
                                <i class="fas fa-clock text-warning me-2"></i>
                                <strong>Step 2: Technical Review</strong> - Pending Food Safety Manager approval
                            </div>
                            <div class="workflow-step">
                                <i class="fas fa-circle text-muted me-2"></i>
                                <strong>Step 3: Final Approval</strong> - HACCP Team Leader review
                            </div>
                        </div>
                    </div>

                    <!-- Risk Matrix -->
                    <div class="tab-pane fade" id="riskMatrix">
                        <div class="section-card">
                            <h4><i class="fas fa-th text-danger me-2"></i>HACCP Risk Matrix (5x5)</h4>
                            <p class="text-muted">Visual representation of all assessed risks</p>
                            
                            <div class="table-responsive">
                                <table class="risk-matrix mx-auto">
                                    <tr>
                                        <td rowspan="6" class="align-middle bg-light"><strong>LIKELIHOOD</strong></td>
                                        <td class="risk-high">5</td>
                                        <td class="risk-medium">5</td>
                                        <td class="risk-medium">10</td>
                                        <td class="risk-high">15</td>
                                        <td class="risk-high">20</td>
                                        <td class="risk-high">25</td>
                                    </tr>
                                    <tr>
                                        <td class="risk-medium">4</td>
                                        <td class="risk-low">4</td>
                                        <td class="risk-medium">8</td>
                                        <td class="risk-medium">12</td>
                                        <td class="risk-high">16</td>
                                        <td class="risk-high">20</td>
                                    </tr>
                                    <tr>
                                        <td class="risk-medium">3</td>
                                        <td class="risk-low">3</td>
                                        <td class="risk-low">6</td>
                                        <td class="risk-medium">9 ●</td>
                                        <td class="risk-medium">12</td>
                                        <td class="risk-high">15</td>
                                    </tr>
                                    <tr>
                                        <td class="risk-low">2</td>
                                        <td class="risk-low">2</td>
                                        <td class="risk-low">4</td>
                                        <td class="risk-low">6</td>
                                        <td class="risk-medium">8</td>
                                        <td class="risk-medium">10</td>
                                    </tr>
                                    <tr>
                                        <td class="risk-low">1</td>
                                        <td class="risk-low">1</td>
                                        <td class="risk-low">2</td>
                                        <td class="risk-low">3</td>
                                        <td class="risk-low">4</td>
                                        <td class="risk-low">5</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td><strong>1</strong></td>
                                        <td><strong>2</strong></td>
                                        <td><strong>3</strong></td>
                                        <td><strong>4</strong></td>
                                        <td><strong>5</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="bg-light"><strong>SEVERITY</strong></td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="alert alert-success">
                                        <strong>Low Risk (1-8)</strong><br>
                                        Acceptable risk level
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="alert alert-warning">
                                        <strong>Medium Risk (9-15)</strong><br>
                                        Requires control measures
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="alert alert-danger">
                                        <strong>High Risk (16-25)</strong><br>
                                        Critical Control Point required
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Existing Risks -->
                    <div class="tab-pane fade" id="existingRisks">
                        <div class="section-card">
                            <h4><i class="fas fa-list text-danger me-2"></i>Current Risk Register</h4>
                            
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Process Step</th>
                                            <th>Hazard</th>
                                            <th>Type</th>
                                            <th>Risk Score</th>
                                            <th>CCP</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>RA-001</strong></td>
                                            <td>Raw Material Receiving</td>
                                            <td>Salmonella contamination</td>
                                            <td><span class="badge bg-danger">Biological</span></td>
                                            <td><span class="badge bg-danger">20</span></td>
                                            <td><i class="fas fa-check-circle text-success"></i></td>
                                            <td><span class="badge bg-success">Approved</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>RA-002</strong></td>
                                            <td>Cooking</td>
                                            <td>Insufficient heat treatment</td>
                                            <td><span class="badge bg-danger">Biological</span></td>
                                            <td><span class="badge bg-danger">16</span></td>
                                            <td><i class="fas fa-check-circle text-success"></i></td>
                                            <td><span class="badge bg-warning">Pending Review</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>RA-003</strong></td>
                                            <td>Packaging</td>
                                            <td>Metal contamination</td>
                                            <td><span class="badge bg-info">Physical</span></td>
                                            <td><span class="badge bg-warning">12</span></td>
                                            <td><i class="fas fa-times-circle text-danger"></i></td>
                                            <td><span class="badge bg-success">Approved</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Reports -->
                    <div class="tab-pane fade" id="reports">
                        <div class="section-card">
                            <h4><i class="fas fa-chart-bar text-danger me-2"></i>HACCP Reports & Analytics</h4>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                                            <h5>Risk Assessment Report</h5>
                                            <p class="text-muted">Complete HACCP analysis with matrix</p>
                                            <button class="btn btn-danger">
                                                <i class="fas fa-download me-2"></i>Generate PDF
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="fas fa-crosshairs fa-3x text-warning mb-3"></i>
                                            <h5>CCP Summary</h5>
                                            <p class="text-muted">Critical Control Points list</p>
                                            <button class="btn btn-warning">
                                                <i class="fas fa-download me-2"></i>Export Excel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="fas fa-chart-line fa-3x text-info mb-3"></i>
                                            <h5>Trend Analysis</h5>
                                            <p class="text-muted">Risk trends over time</p>
                                            <button class="btn btn-info">
                                                <i class="fas fa-eye me-2"></i>View Dashboard
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <h6>Risk Distribution Chart</h6>
                                <canvas id="riskChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
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
            riskLevelElement.className = 'badge ' + color;
        }

        // Form submission
        document.getElementById('riskAssessmentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Simulate saving
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
            submitBtn.disabled = true;
            
            setTimeout(() => {
                alert('Risk assessment saved successfully!\n\nSubmitted for Food Safety Manager review.');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                // Clear form
                this.reset();
                calculateRisk();
            }, 2000);
        });

        // Initialize chart
        const ctx = document.getElementById('riskChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Low Risk', 'Medium Risk', 'High Risk'],
                datasets: [{
                    data: [4, 5, 3],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Initialize risk calculation
        calculateRisk();
    </script>
</body>
</html>