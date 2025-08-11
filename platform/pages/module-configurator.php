<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HACCP Risk Assessment - Module Configuration</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .config-container { background: rgba(255,255,255,0.95); border-radius: 20px; margin: 20px 0; }
        .config-header { background: linear-gradient(135deg, #2c5aa0, #667eea); color: white; border-radius: 20px 20px 0 0; padding: 1.5rem; }
        .config-section { background: rgba(255,255,255,0.8); border-radius: 15px; padding: 2rem; margin-bottom: 2rem; }
        .drag-area { border: 2px dashed #007bff; border-radius: 10px; padding: 30px; text-align: center; margin: 15px 0; }
        .field-item { background: white; border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin: 10px 0; cursor: move; }
        .field-preview { background: #f8f9fa; border-radius: 8px; padding: 15px; margin: 10px 0; }
        .btn-gradient { background: linear-gradient(135deg, #2c5aa0, #667eea); border: none; border-radius: 25px; color: white; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="config-container">
            <div class="config-header">
                <h2><i class="fas fa-cogs me-3"></i>HACCP Risk Assessment Module Configuration</h2>
                <p class="mb-0">Design the risk assessment workflow and forms that customers will use</p>
            </div>

            <div class="p-4">
                <!-- Module Settings -->
                <div class="config-section">
                    <h4><i class="fas fa-sliders-h text-primary me-2"></i>Module Settings</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Module Name</label>
                            <input type="text" class="form-control" value="HACCP Risk Assessment v3.0" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">BRC Compliance</label>
                            <input type="text" class="form-control" value="Clause 2.1 - Food Safety Plan" readonly>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <label class="form-label">Usage Scenario</label>
                            <textarea class="form-control" rows="2" readonly>Customers will use this module to systematically identify food safety hazards, assess risks, and determine critical control points in their HACCP plan.</textarea>
                        </div>
                    </div>
                </div>

                <!-- Risk Assessment Form Designer -->
                <div class="config-section">
                    <h4><i class="fas fa-wpforms text-primary me-2"></i>Risk Assessment Form Design</h4>
                    <p class="text-muted">Design the form that customers will use to enter risk data</p>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <h6>Available Fields</h6>
                            <div class="available-fields">
                                <div class="field-item" draggable="true" data-type="process_step">
                                    <i class="fas fa-cog me-2"></i><strong>Process Step</strong><br>
                                    <small>Production stage being assessed</small>
                                </div>
                                <div class="field-item" draggable="true" data-type="hazard_type">
                                    <i class="fas fa-exclamation-triangle me-2"></i><strong>Hazard Type</strong><br>
                                    <small>Biological, Chemical, Physical</small>
                                </div>
                                <div class="field-item" draggable="true" data-type="hazard_description">
                                    <i class="fas fa-edit me-2"></i><strong>Hazard Description</strong><br>
                                    <small>Detailed hazard description</small>
                                </div>
                                <div class="field-item" draggable="true" data-type="likelihood">
                                    <i class="fas fa-percentage me-2"></i><strong>Likelihood (1-5)</strong><br>
                                    <small>Probability of occurrence</small>
                                </div>
                                <div class="field-item" draggable="true" data-type="severity">
                                    <i class="fas fa-thermometer-half me-2"></i><strong>Severity (1-5)</strong><br>
                                    <small>Impact if hazard occurs</small>
                                </div>
                                <div class="field-item" draggable="true" data-type="control_measures">
                                    <i class="fas fa-shield-alt me-2"></i><strong>Control Measures</strong><br>
                                    <small>Existing preventive controls</small>
                                </div>
                                <div class="field-item" draggable="true" data-type="ccp_decision">
                                    <i class="fas fa-crosshairs me-2"></i><strong>CCP Decision</strong><br>
                                    <small>Is this a Critical Control Point?</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <h6>Form Layout</h6>
                            <div class="drag-area" id="formBuilder">
                                <i class="fas fa-mouse-pointer fa-2x mb-3 text-muted"></i>
                                <p class="text-muted">Drag fields here to build the risk assessment form</p>
                                <p class="text-muted"><small>This is what customers will see when they open the Risk Assessment module</small></p>
                            </div>
                            
                            <div class="form-preview mt-4" id="formPreview" style="display: none;">
                                <h6>Customer Form Preview:</h6>
                                <div class="field-preview">
                                    <div class="bg-white p-3 border rounded">
                                        <h5 class="text-primary"><i class="fas fa-clipboard-list me-2"></i>HACCP Risk Assessment Form</h5>
                                        <div id="previewFields"></div>
                                        <button class="btn btn-primary btn-sm mt-3">
                                            <i class="fas fa-save me-1"></i>Save Risk Assessment
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Risk Matrix Configuration -->
                <div class="config-section">
                    <h4><i class="fas fa-th text-primary me-2"></i>Risk Matrix Configuration</h4>
                    <p class="text-muted">Configure how risks will be visualized for customers</p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Matrix Size</label>
                            <select class="form-select">
                                <option>5x5 (Recommended for BRC)</option>
                                <option>3x3 (Simple)</option>
                                <option>4x4 (Intermediate)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Risk Categories</label>
                            <div class="d-flex gap-2">
                                <span class="badge bg-success">Low (1-8)</span>
                                <span class="badge bg-warning">Medium (9-15)</span>
                                <span class="badge bg-danger">High (16-25)</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label class="form-label">Customer Matrix Preview:</label>
                        <div class="bg-white p-3 border rounded">
                            <table class="table table-bordered text-center">
                                <tr>
                                    <td rowspan="6" class="align-middle bg-light"><strong>Likelihood</strong></td>
                                    <td class="bg-danger text-white">5</td>
                                    <td class="bg-warning">5</td>
                                    <td class="bg-warning">10</td>
                                    <td class="bg-danger text-white">15</td>
                                    <td class="bg-danger text-white">20</td>
                                    <td class="bg-danger text-white">25</td>
                                </tr>
                                <tr>
                                    <td class="bg-warning">4</td>
                                    <td class="bg-success text-white">4</td>
                                    <td class="bg-warning">8</td>
                                    <td class="bg-warning">12</td>
                                    <td class="bg-danger text-white">16</td>
                                    <td class="bg-danger text-white">20</td>
                                </tr>
                                <tr>
                                    <td class="bg-warning">3</td>
                                    <td class="bg-success text-white">3</td>
                                    <td class="bg-success text-white">6</td>
                                    <td class="bg-warning">9</td>
                                    <td class="bg-warning">12</td>
                                    <td class="bg-danger text-white">15</td>
                                </tr>
                                <tr>
                                    <td class="bg-success text-white">2</td>
                                    <td class="bg-success text-white">2</td>
                                    <td class="bg-success text-white">4</td>
                                    <td class="bg-success text-white">6</td>
                                    <td class="bg-warning">8</td>
                                    <td class="bg-warning">10</td>
                                </tr>
                                <tr>
                                    <td class="bg-success text-white">1</td>
                                    <td class="bg-success text-white">1</td>
                                    <td class="bg-success text-white">2</td>
                                    <td class="bg-success text-white">3</td>
                                    <td class="bg-success text-white">4</td>
                                    <td class="bg-success text-white">5</td>
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
                                    <td colspan="7" class="bg-light"><strong>Severity</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Workflow Configuration -->
                <div class="config-section">
                    <h4><i class="fas fa-route text-primary me-2"></i>Approval Workflow</h4>
                    <p class="text-muted">Define the approval process for risk assessments</p>
                    
                    <div class="workflow-steps">
                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-2">
                            <div>
                                <strong>1. Risk Assessment Entry</strong><br>
                                <small>Quality team enters hazard data</small>
                            </div>
                            <i class="fas fa-arrow-right text-primary"></i>
                        </div>
                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-2">
                            <div>
                                <strong>2. Technical Review</strong><br>
                                <small>Food safety manager reviews</small>
                            </div>
                            <i class="fas fa-arrow-right text-primary"></i>
                        </div>
                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-2">
                            <div>
                                <strong>3. Final Approval</strong><br>
                                <small>HACCP team leader approves</small>
                            </div>
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                    </div>
                </div>

                <!-- Reports Configuration -->
                <div class="config-section">
                    <h4><i class="fas fa-chart-bar text-primary me-2"></i>Customer Reports</h4>
                    <p class="text-muted">Configure what reports customers will receive</p>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-pdf fa-2x text-danger mb-2"></i>
                                    <h6>Risk Assessment Report</h6>
                                    <small>Complete HACCP risk analysis with matrix</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-crosshairs fa-2x text-warning mb-2"></i>
                                    <h6>CCP Summary</h6>
                                    <small>List of identified Critical Control Points</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-line fa-2x text-info mb-2"></i>
                                    <h6>Trend Analysis</h6>
                                    <small>Risk trends over time</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Configuration -->
                <div class="text-center">
                    <button class="btn btn-gradient btn-lg px-5">
                        <i class="fas fa-save me-2"></i>Save Module Configuration
                    </button>
                    <button class="btn btn-success btn-lg px-5 ms-3">
                        <i class="fas fa-eye me-2"></i>Preview Customer View
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        let formFields = [];
        const formBuilder = document.getElementById('formBuilder');
        const formPreview = document.getElementById('formPreview');
        const previewFields = document.getElementById('previewFields');

        // Drag and drop functionality
        document.querySelectorAll('.field-item').forEach(item => {
            item.addEventListener('dragstart', function(e) {
                e.dataTransfer.setData('text/plain', this.dataset.type);
            });
        });

        formBuilder.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = '#007bff';
            this.style.backgroundColor = 'rgba(0, 123, 255, 0.1)';
        });

        formBuilder.addEventListener('dragleave', function(e) {
            this.style.borderColor = '#007bff';
            this.style.backgroundColor = 'transparent';
        });

        formBuilder.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = '#007bff';
            this.style.backgroundColor = 'transparent';
            
            const fieldType = e.dataTransfer.getData('text/plain');
            addFieldToForm(fieldType);
        });

        function addFieldToForm(fieldType) {
            const fieldConfig = getFieldConfig(fieldType);
            formFields.push(fieldConfig);
            updateFormPreview();
        }

        function getFieldConfig(type) {
            const configs = {
                'process_step': { name: 'Process Step', type: 'select', options: ['Receiving', 'Storage', 'Preparation', 'Cooking', 'Cooling', 'Packaging'] },
                'hazard_type': { name: 'Hazard Type', type: 'select', options: ['Biological', 'Chemical', 'Physical'] },
                'hazard_description': { name: 'Hazard Description', type: 'textarea' },
                'likelihood': { name: 'Likelihood (1-5)', type: 'range', min: 1, max: 5 },
                'severity': { name: 'Severity (1-5)', type: 'range', min: 1, max: 5 },
                'control_measures': { name: 'Control Measures', type: 'textarea' },
                'ccp_decision': { name: 'Critical Control Point?', type: 'radio', options: ['Yes', 'No'] }
            };
            return configs[type] || { name: 'Unknown Field', type: 'text' };
        }

        function updateFormPreview() {
            if (formFields.length === 0) {
                formPreview.style.display = 'none';
                return;
            }

            formPreview.style.display = 'block';
            previewFields.innerHTML = formFields.map(field => {
                let html = `<div class="mb-3">
                    <label class="form-label"><strong>${field.name}</strong></label>`;
                
                if (field.type === 'select') {
                    html += `<select class="form-select">`;
                    html += field.options.map(opt => `<option>${opt}</option>`).join('');
                    html += `</select>`;
                } else if (field.type === 'textarea') {
                    html += `<textarea class="form-control" rows="3"></textarea>`;
                } else if (field.type === 'range') {
                    html += `<input type="range" class="form-range" min="${field.min}" max="${field.max}">
                            <div class="d-flex justify-content-between">
                                <small>${field.min}</small>
                                <small>${field.max}</small>
                            </div>`;
                } else if (field.type === 'radio') {
                    html += field.options.map(opt => 
                        `<div class="form-check">
                            <input class="form-check-input" type="radio" name="${field.name}">
                            <label class="form-check-label">${opt}</label>
                        </div>`
                    ).join('');
                } else {
                    html += `<input type="text" class="form-control">`;
                }
                
                html += `</div>`;
                return html;
            }).join('');

            // Auto-calculate risk score preview
            if (formFields.some(f => f.name.includes('Likelihood')) && formFields.some(f => f.name.includes('Severity'))) {
                previewFields.innerHTML += `
                    <div class="alert alert-info">
                        <strong>Risk Score:</strong> <span class="badge bg-warning">Auto-calculated (Likelihood × Severity)</span>
                    </div>`;
            }
        }

        // Show initial drag message
        formBuilder.innerHTML = `
            <i class="fas fa-mouse-pointer fa-2x mb-3 text-muted"></i>
            <p class="text-muted">Drag fields here to build the risk assessment form</p>
            <p class="text-muted"><small>This is what customers will see when they open the Risk Assessment module</small></p>
        `;
    </script>
</body>
</html>