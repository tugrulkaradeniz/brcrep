<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRC Component Library - Module Builder Components</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', sans-serif;
            padding: 2rem 0;
        }
        
        .component-demo {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .component-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        
        .copy-code-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
        }
        
        .component-preview {
            position: relative;
            border: 2px dashed #e9ecef;
            border-radius: 10px;
            padding: 2rem;
            margin: 1rem 0;
            min-height: 200px;
        }
        
        pre {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            overflow-x: auto;
            position: relative;
        }
        
        /* Component Styles */
        .brc-label {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            display: inline-block;
            margin: 0.5rem 0;
        }
        
        .brc-label.warning {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: black;
        }
        
        .brc-label.danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }
        
        .brc-smart-form {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid #e9ecef;
        }
        
        .form-section {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1rem 0;
            border-left: 4px solid #007bff;
        }
        
        .timeline-container {
            position: relative;
            padding: 2rem 0;
        }
        
        .timeline-line {
            position: absolute;
            left: 30px;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(to bottom, #007bff, #28a745);
            border-radius: 2px;
        }
        
        .timeline-item {
            position: relative;
            padding-left: 80px;
            margin: 2rem 0;
        }
        
        .timeline-marker {
            position: absolute;
            left: 20px;
            top: 0;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.8rem;
            z-index: 2;
        }
        
        .timeline-marker.completed {
            background: #28a745;
        }
        
        .timeline-marker.current {
            background: #ffc107;
            color: black;
        }
        
        .timeline-marker.pending {
            background: #6c757d;
        }
        
        .timeline-content {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-left: 4px solid #007bff;
        }
        
        .edit-component {
            background: #f8f9fa;
            border: 2px dashed #007bff;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .edit-component:hover {
            background: rgba(0,123,255,0.05);
            border-color: #0056b3;
        }
        
        .edit-component.editing {
            border-style: solid;
            background: white;
            text-align: left;
        }
        
        .editable-content {
            min-height: 100px;
            padding: 1rem;
            border-radius: 5px;
            border: 1px solid #e9ecef;
            background: white;
            outline: none;
        }
        
        .editable-content:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center text-white mb-5">
            <h1 class="display-4 fw-bold">🧩 BRC Component Library</h1>
            <p class="lead">Professional business components ready for Module Builder</p>
        </div>

        <!-- 1. LABEL COMPONENT -->
        <div class="component-demo">
            <div class="component-header">
                <h3><i class="fas fa-tag me-2"></i>Label Component</h3>
                <p class="mb-0">Customizable labels for sections, statuses, and categories</p>
            </div>
            
            <div class="component-preview">
                <button class="btn btn-outline-secondary btn-sm copy-code-btn" onclick="copyCode('label-code')">
                    <i class="fas fa-copy me-1"></i>Copy Code
                </button>
                
                <div class="mb-3">
                    <span class="brc-label">BRC Compliant</span>
                    <span class="brc-label warning">Under Review</span>
                    <span class="brc-label danger">Critical Risk</span>
                </div>
                
                <div class="mb-3">
                    <span class="badge bg-primary fs-6 me-2">HACCP Plan</span>
                    <span class="badge bg-success fs-6 me-2">Approved</span>
                    <span class="badge bg-warning fs-6 me-2">Pending</span>
                </div>
            </div>
            
            <pre id="label-code"><code>&lt;!-- BRC Label Component --&gt;
&lt;span class="brc-label"&gt;BRC Compliant&lt;/span&gt;
&lt;span class="brc-label warning"&gt;Under Review&lt;/span&gt;
&lt;span class="brc-label danger"&gt;Critical Risk&lt;/span&gt;

&lt;!-- Standard Badges --&gt;
&lt;span class="badge bg-primary fs-6"&gt;HACCP Plan&lt;/span&gt;
&lt;span class="badge bg-success fs-6"&gt;Approved&lt;/span&gt;

&lt;style&gt;
.brc-label {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    display: inline-block;
    margin: 0.5rem 0;
}
.brc-label.warning { background: linear-gradient(135deg, #ffc107, #fd7e14); color: black; }
.brc-label.danger { background: linear-gradient(135deg, #dc3545, #c82333); }
&lt;/style&gt;</code></pre>
        </div>

        <!-- 2. FORM COMPONENT -->
        <div class="component-demo">
            <div class="component-header">
                <h3><i class="fas fa-wpforms me-2"></i>Smart Form Component</h3>
                <p class="mb-0">Dynamic forms with validation, conditional fields, and auto-save</p>
            </div>
            
            <div class="component-preview">
                <button class="btn btn-outline-secondary btn-sm copy-code-btn" onclick="copyCode('form-code')">
                    <i class="fas fa-copy me-1"></i>Copy Code
                </button>
                
                <div class="brc-smart-form">
                    <h5><i class="fas fa-clipboard-list text-primary me-2"></i>HACCP Assessment Form</h5>
                    
                    <div class="form-section">
                        <h6>Basic Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Process Step *</label>
                                <select class="form-select" required>
                                    <option value="">Select process...</option>
                                    <option>Raw Material Receiving</option>
                                    <option>Cold Storage</option>
                                    <option>Food Preparation</option>
                                    <option>Cooking/Heat Treatment</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Assessment Date</label>
                                <input type="date" class="form-control" value="2025-01-10">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h6>Risk Assessment</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Hazard Description</label>
                            <textarea class="form-control" rows="3" placeholder="Describe the specific hazard and its potential impact..."></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Likelihood (1-5)</label>
                                <input type="range" class="form-range" min="1" max="5" value="3" oninput="updateRiskScore()">
                                <small class="text-muted">Current: 3 (Possible)</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Severity (1-5)</label>
                                <input type="range" class="form-range" min="1" max="5" value="4" oninput="updateRiskScore()">
                                <small class="text-muted">Current: 4 (Severe)</small>
                            </div>
                        </div>
                        <div class="alert alert-warning mt-3">
                            <strong>Risk Score: <span id="riskScore">12</span></strong> - Medium Risk
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Assessment
                        </button>
                        <button class="btn btn-outline-secondary">
                            <i class="fas fa-eye me-2"></i>Preview
                        </button>
                        <button class="btn btn-outline-info">
                            <i class="fas fa-file-export me-2"></i>Export
                        </button>
                    </div>
                </div>
            </div>
            
            <pre id="form-code"><code>&lt;!-- BRC Smart Form Component --&gt;
&lt;div class="brc-smart-form"&gt;
    &lt;h5&gt;&lt;i class="fas fa-clipboard-list text-primary me-2"&gt;&lt;/i&gt;HACCP Assessment Form&lt;/h5&gt;
    
    &lt;div class="form-section"&gt;
        &lt;h6&gt;Basic Information&lt;/h6&gt;
        &lt;div class="row"&gt;
            &lt;div class="col-md-6"&gt;
                &lt;label class="form-label fw-bold"&gt;Process Step *&lt;/label&gt;
                &lt;select class="form-select" required&gt;
                    &lt;option value=""&gt;Select process...&lt;/option&gt;
                    &lt;option&gt;Raw Material Receiving&lt;/option&gt;
                    &lt;option&gt;Cold Storage&lt;/option&gt;
                &lt;/select&gt;
            &lt;/div&gt;
            &lt;div class="col-md-6"&gt;
                &lt;label class="form-label fw-bold"&gt;Assessment Date&lt;/label&gt;
                &lt;input type="date" class="form-control"&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    &lt;/div&gt;
    
    &lt;div class="d-flex gap-2"&gt;
        &lt;button class="btn btn-primary"&gt;Save Assessment&lt;/button&gt;
        &lt;button class="btn btn-outline-secondary"&gt;Preview&lt;/button&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
        </div>

        <!-- 3. TIMELINE COMPONENT -->
        <div class="component-demo">
            <div class="component-header">
                <h3><i class="fas fa-route me-2"></i>Timeline Component</h3>
                <p class="mb-0">Visual timeline for processes, approvals, and project milestones</p>
            </div>
            
            <div class="component-preview">
                <button class="btn btn-outline-secondary btn-sm copy-code-btn" onclick="copyCode('timeline-code')">
                    <i class="fas fa-copy me-1"></i>Copy Code
                </button>
                
                <div class="timeline-container">
                    <div class="timeline-line"></div>
                    
                    <div class="timeline-item">
                        <div class="timeline-marker completed">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="mb-2">HACCP Plan Created</h6>
                            <p class="mb-1">Initial risk assessment completed by Quality Team</p>
                            <small class="text-muted">January 8, 2025 - 14:30</small>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-marker completed">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="mb-2">Manager Review</h6>
                            <p class="mb-1">Reviewed and approved by Food Safety Manager</p>
                            <small class="text-muted">January 9, 2025 - 09:15</small>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-marker current">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="mb-2">Technical Review</h6>
                            <p class="mb-1">Pending technical approval from HACCP Team Leader</p>
                            <small class="text-muted">In Progress</small>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-marker pending">
                            <i class="fas fa-circle"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="mb-2">Final Approval</h6>
                            <p class="mb-1">Final sign-off and documentation</p>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <pre id="timeline-code"><code>&lt;!-- BRC Timeline Component --&gt;
&lt;div class="timeline-container"&gt;
    &lt;div class="timeline-line"&gt;&lt;/div&gt;
    
    &lt;div class="timeline-item"&gt;
        &lt;div class="timeline-marker completed"&gt;
            &lt;i class="fas fa-check"&gt;&lt;/i&gt;
        &lt;/div&gt;
        &lt;div class="timeline-content"&gt;
            &lt;h6 class="mb-2"&gt;HACCP Plan Created&lt;/h6&gt;
            &lt;p class="mb-1"&gt;Initial risk assessment completed&lt;/p&gt;
            &lt;small class="text-muted"&gt;January 8, 2025&lt;/small&gt;
        &lt;/div&gt;
    &lt;/div&gt;
    
    &lt;div class="timeline-item"&gt;
        &lt;div class="timeline-marker current"&gt;
            &lt;i class="fas fa-clock"&gt;&lt;/i&gt;
        &lt;/div&gt;
        &lt;div class="timeline-content"&gt;
            &lt;h6 class="mb-2"&gt;Technical Review&lt;/h6&gt;
            &lt;p class="mb-1"&gt;Pending approval&lt;/p&gt;
            &lt;small class="text-muted"&gt;In Progress&lt;/small&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
        </div>

        <!-- 4. EDIT COMPONENT -->
        <div class="component-demo">
            <div class="component-header">
                <h3><i class="fas fa-edit me-2"></i>Edit Component</h3>
                <p class="mb-0">Inline editing with auto-save and version control</p>
            </div>
            
            <div class="component-preview">
                <button class="btn btn-outline-secondary btn-sm copy-code-btn" onclick="copyCode('edit-code')">
                    <i class="fas fa-copy me-1"></i>Copy Code
                </button>
                
                <div class="edit-component" id="editComponent1" onclick="startEdit('editComponent1')">
                    <div class="editable-content" contenteditable="false">
                        <h5>HACCP Control Measures</h5>
                        <p>Temperature monitoring at critical control points must be conducted every 2 hours during production. All readings must be recorded in the temperature log and any deviations exceeding ±2°C must trigger immediate corrective action.</p>
                        <ul>
                            <li>Monitor temperature every 2 hours</li>
                            <li>Record all readings</li>
                            <li>Report deviations immediately</li>
                        </ul>
                    </div>
                    <div class="edit-controls" style="display: none;">
                        <button class="btn btn-success btn-sm me-2" onclick="saveEdit('editComponent1')">
                            <i class="fas fa-save me-1"></i>Save
                        </button>
                        <button class="btn btn-secondary btn-sm" onclick="cancelEdit('editComponent1')">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                    </div>
                    <div class="edit-hint text-muted">
                        <i class="fas fa-edit me-1"></i>Click to edit this content
                    </div>
                </div>
                
                <div class="mt-3">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-lightbulb me-2"></i>Edit Component Features</h6>
                        <ul class="mb-0">
                            <li>Click anywhere to start editing</li>
                            <li>Auto-save after 5 seconds of inactivity</li>
                            <li>Version history tracking</li>
                            <li>Rich text formatting support</li>
                            <li>User permission controls</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <pre id="edit-code"><code>&lt;!-- BRC Edit Component --&gt;
&lt;div class="edit-component" onclick="startEdit(this)"&gt;
    &lt;div class="editable-content" contenteditable="false"&gt;
        &lt;h5&gt;HACCP Control Measures&lt;/h5&gt;
        &lt;p&gt;Temperature monitoring at critical control points...&lt;/p&gt;
    &lt;/div&gt;
    &lt;div class="edit-controls" style="display: none;"&gt;
        &lt;button class="btn btn-success btn-sm" onclick="saveEdit(this)"&gt;Save&lt;/button&gt;
        &lt;button class="btn btn-secondary btn-sm" onclick="cancelEdit(this)"&gt;Cancel&lt;/button&gt;
    &lt;/div&gt;
    &lt;div class="edit-hint text-muted"&gt;
        &lt;i class="fas fa-edit me-1"&gt;&lt;/i&gt;Click to edit
    &lt;/div&gt;
&lt;/div&gt;

&lt;script&gt;
function startEdit(element) {
    element.classList.add('editing');
    element.querySelector('.editable-content').contentEditable = true;
    element.querySelector('.edit-controls').style.display = 'block';
    element.querySelector('.edit-hint').style.display = 'none';
}
&lt;/script&gt;</code></pre>
        </div>

        <!-- Component Usage Guide -->
        <div class="component-demo">
            <div class="component-header">
                <h3><i class="fas fa-rocket me-2"></i>Module Builder Integration</h3>
                <p class="mb-0">How to use these components in your modules</p>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h5>1. Platform Admin</h5>
                    <ol>
                        <li>Go to Module Builder</li>
                        <li>Create new module "HACCP Management"</li>
                        <li>Drag components from library</li>
                        <li>Configure properties</li>
                        <li>Publish to marketplace</li>
                    </ol>
                </div>
                <div class="col-md-6">
                    <h5>2. Customer Usage</h5>
                    <ol>
                        <li>Browse marketplace</li>
                        <li>Subscribe to module</li>
                        <li>Module appears in their panel</li>
                        <li>Use components for their business</li>
                        <li>Generate reports & exports</li>
                    </ol>
                </div>
            </div>
            
            <div class="alert alert-success mt-4">
                <h6><i class="fas fa-check-circle me-2"></i>Ready for Module Builder!</h6>
                <p class="mb-0">These components are now ready to be used in the Platform Admin Module Builder. Each component has proper CSS classes, JavaScript functionality, and responsive design.</p>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Copy code functionality
        function copyCode(elementId) {
            const codeElement = document.getElementById(elementId);
            const code = codeElement.textContent;
            
            navigator.clipboard.writeText(code).then(() => {
                showNotification('Component code copied to clipboard!', 'success');
            });
        }

        // Risk score calculation for form demo
        function updateRiskScore() {
            const likelihood = document.querySelector('input[type="range"]:nth-of-type(1)').value;
            const severity = document.querySelector('input[type="range"]:nth-of-type(2)').value;
            const score = likelihood * severity;
            
            document.getElementById('riskScore').textContent = score;
            
            // Update risk level
            const alertBox = document.querySelector('.alert-warning');
            if (score <= 8) {
                alertBox.className = 'alert alert-success mt-3';
                alertBox.innerHTML = `<strong>Risk Score: ${score}</strong> - Low Risk`;
            } else if (score <= 15) {
                alertBox.className = 'alert alert-warning mt-3';
                alertBox.innerHTML = `<strong>Risk Score: ${score}</strong> - Medium Risk`;
            } else {
                alertBox.className = 'alert alert-danger mt-3';
                alertBox.innerHTML = `<strong>Risk Score: ${score}</strong> - High Risk`;
            }
        }

        // Edit component functionality
        function startEdit(elementId) {
            const element = document.getElementById(elementId);
            const content = element.querySelector('.editable-content');
            const controls = element.querySelector('.edit-controls');
            const hint = element.querySelector('.edit-hint');
            
            element.classList.add('editing');
            content.contentEditable = true;
            content.focus();
            controls.style.display = 'block';
            hint.style.display = 'none';
            
            // Store original content for cancel functionality
            element.dataset.originalContent = content.innerHTML;
        }

        function saveEdit(elementId) {
            const element = document.getElementById(elementId);
            const content = element.querySelector('.editable-content');
            const controls = element.querySelector('.edit-controls');
            const hint = element.querySelector('.edit-hint');
            
            element.classList.remove('editing');
            content.contentEditable = false;
            controls.style.display = 'none';
            hint.style.display = 'block';
            
            showNotification('Content saved successfully!', 'success');
            
            // In real implementation, save to database here
            console.log('Saving content:', content.innerHTML);
        }

        function cancelEdit(elementId) {
            const element = document.getElementById(elementId);
            const content = element.querySelector('.editable-content');
            const controls = element.querySelector('.edit-controls');
            const hint = element.querySelector('.edit-hint');
            
            // Restore original content
            content.innerHTML = element.dataset.originalContent;
            
            element.classList.remove('editing');
            content.contentEditable = false;
            controls.style.display = 'none';
            hint.style.display = 'block';
            
            showNotification('Changes cancelled', 'warning');
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
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 4000);
        }

        // Initialize timeline animations
        document.addEventListener('DOMContentLoaded', function() {
            // Add subtle animations to timeline items
            const timelineItems = document.querySelectorAll('.timeline-item');
            timelineItems.forEach((item, index) => {
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'translateX(0)';
                }, index * 200);
            });
        });
    </script>
</body>
</html>