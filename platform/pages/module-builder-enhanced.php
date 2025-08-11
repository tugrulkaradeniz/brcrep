<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Module Builder - Create HACCP Management Module</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Inter', sans-serif;
        }
        
        .module-builder {
            display: grid;
            grid-template-columns: 300px 1fr 350px;
            gap: 1rem;
            height: 100vh;
            padding: 1rem;
        }
        
        .component-library {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow-y: auto;
        }
        
        .canvas-area {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: relative;
            overflow-y: auto;
        }
        
        .properties-panel {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow-y: auto;
        }
        
        .component-item {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 1rem;
            margin: 0.5rem 0;
            cursor: grab;
            transition: all 0.3s ease;
        }
        
        .component-item:hover {
            border-color: #007bff;
            background: rgba(0,123,255,0.05);
        }
        
        .component-item:active {
            cursor: grabbing;
        }
        
        .canvas {
            min-height: 600px;
            border: 2px dashed #e9ecef;
            border-radius: 10px;
            padding: 2rem;
            position: relative;
        }
        
        .canvas.drag-over {
            border-color: #007bff;
            background: rgba(0,123,255,0.05);
        }
        
        .dropped-component {
            background: white;
            border: 2px solid #007bff;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1rem 0;
            position: relative;
            cursor: pointer;
        }
        
        .dropped-component:hover {
            box-shadow: 0 4px 15px rgba(0,123,255,0.2);
        }
        
        .dropped-component.selected {
            border-color: #28a745;
            box-shadow: 0 0 0 3px rgba(40,167,69,0.25);
        }
        
        .component-controls {
            position: absolute;
            top: 10px;
            right: 10px;
            display: none;
        }
        
        .dropped-component:hover .component-controls {
            display: block;
        }
        
        .module-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 1.5rem;
            border-radius: 15px 15px 0 0;
            margin: -2rem -2rem 2rem -2rem;
        }
        
        .component-category {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
        
        .canvas-grid {
            background-image: 
                linear-gradient(to right, #f1f3f4 1px, transparent 1px),
                linear-gradient(to bottom, #f1f3f4 1px, transparent 1px);
            background-size: 20px 20px;
        }
        
        /* Component Styles in Canvas */
        .canvas .brc-label {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            display: inline-block;
            margin: 0.5rem 0;
        }
        
        .canvas .brc-smart-form {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid #e9ecef;
        }
        
        .canvas .timeline-container {
            position: relative;
            padding: 2rem 0;
        }
        
        .canvas .timeline-line {
            position: absolute;
            left: 30px;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(to bottom, #007bff, #28a745);
            border-radius: 2px;
        }
        
        .canvas .timeline-item {
            position: relative;
            padding-left: 80px;
            margin: 1rem 0;
        }
        
        .module-tabs {
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 2rem;
        }
        
        .module-tab {
            background: none;
            border: none;
            padding: 1rem 1.5rem;
            color: #6c757d;
            cursor: pointer;
            border-bottom: 3px solid transparent;
        }
        
        .module-tab.active {
            color: #007bff;
            border-bottom-color: #007bff;
        }
        
        .save-module {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="module-builder">
        <!-- Component Library Panel -->
        <div class="component-library">
            <h5 class="mb-3">
                <i class="fas fa-cubes text-primary me-2"></i>Component Library
            </h5>
            
            <!-- Display Components -->
            <div class="component-category">
                <h6 class="text-muted mb-2">📊 Display Components</h6>
                
                <div class="component-item" draggable="true" data-component="label">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-tag text-success me-2"></i>
                        <div>
                            <strong>Label</strong>
                            <br><small class="text-muted">Status badges & tags</small>
                        </div>
                    </div>
                </div>
                
                <div class="component-item" draggable="true" data-component="kpi-cards">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chart-line text-info me-2"></i>
                        <div>
                            <strong>KPI Cards</strong>
                            <br><small class="text-muted">Metrics dashboard</small>
                        </div>
                    </div>
                </div>
                
                <div class="component-item" draggable="true" data-component="risk-matrix">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-th text-danger me-2"></i>
                        <div>
                            <strong>Risk Matrix</strong>
                            <br><small class="text-muted">5x5 risk assessment</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Input Components -->
            <div class="component-category">
                <h6 class="text-muted mb-2">📝 Input Components</h6>
                
                <div class="component-item" draggable="true" data-component="smart-form">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-wpforms text-primary me-2"></i>
                        <div>
                            <strong>Smart Form</strong>
                            <br><small class="text-muted">Dynamic forms</small>
                        </div>
                    </div>
                </div>
                
                <div class="component-item" draggable="true" data-component="file-upload">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-cloud-upload-alt text-warning me-2"></i>
                        <div>
                            <strong>File Upload</strong>
                            <br><small class="text-muted">Document management</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Components -->
            <div class="component-category">
                <h6 class="text-muted mb-2">⚡ Action Components</h6>
                
                <div class="component-item" draggable="true" data-component="timeline">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-route text-purple me-2"></i>
                        <div>
                            <strong>Timeline</strong>
                            <br><small class="text-muted">Process workflow</small>
                        </div>
                    </div>
                </div>
                
                <div class="component-item" draggable="true" data-component="edit-area">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-edit text-secondary me-2"></i>
                        <div>
                            <strong>Edit Area</strong>
                            <br><small class="text-muted">Inline editing</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Canvas Area -->
        <div class="canvas-area">
            <div class="module-header">
                <h4 class="mb-2">
                    <i class="fas fa-shield-check me-2"></i>HACCP Management Module
                </h4>
                <p class="mb-0 opacity-75">Drag components from the library to build your module</p>
            </div>
            
            <!-- Module Tabs -->
            <div class="module-tabs">
                <button class="module-tab active" data-tab="overview">Overview</button>
                <button class="module-tab" data-tab="assessment">Risk Assessment</button>
                <button class="module-tab" data-tab="workflow">Workflow</button>
                <button class="module-tab" data-tab="reports">Reports</button>
            </div>
            
            <!-- Canvas -->
            <div class="canvas canvas-grid" id="moduleCanvas">
                <div class="text-center text-muted">
                    <i class="fas fa-mouse-pointer fa-3x mb-3 opacity-50"></i>
                    <h5>Drop Components Here</h5>
                    <p>Drag components from the library to start building your module</p>
                </div>
            </div>
        </div>
        
        <!-- Properties Panel -->
        <div class="properties-panel">
            <h5 class="mb-3">
                <i class="fas fa-cogs text-warning me-2"></i>Properties
            </h5>
            
            <div id="module-properties">
                <div class="mb-4">
                    <h6>Module Information</h6>
                    <div class="mb-3">
                        <label class="form-label">Module Name</label>
                        <input type="text" class="form-control" value="HACCP Management Module" id="moduleName">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" rows="3" id="moduleDescription">Comprehensive HACCP compliance management system</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" id="moduleCategory">
                            <option>Food Safety & Compliance</option>
                            <option>Quality Management</option>
                            <option>Risk Assessment</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price (Monthly)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" value="299" id="modulePrice">
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h6>Module Settings</h6>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" checked id="enableNotifications">
                        <label class="form-check-label" for="enableNotifications">
                            Enable Notifications
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" checked id="enableExports">
                        <label class="form-check-label" for="enableExports">
                            Enable Exports
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="requireApproval">
                        <label class="form-check-label" for="requireApproval">
                            Require Approval Workflow
                        </label>
                    </div>
                </div>
            </div>
            
            <div id="component-properties" style="display: none;">
                <div class="mb-4">
                    <h6>Component Properties</h6>
                    <div id="componentPropsContainer">
                        <!-- Component-specific properties will appear here -->
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <h6>Module Preview</h6>
                <div class="alert alert-info">
                    <small>
                        <strong>Components:</strong> <span id="componentCount">0</span><br>
                        <strong>Estimated Size:</strong> <span id="moduleSize">0 KB</span><br>
                        <strong>Load Time:</strong> <span id="loadTime">0.1s</span>
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Save Module Button -->
    <div class="save-module">
        <button class="btn btn-success btn-lg" onclick="saveModule()">
            <i class="fas fa-save me-2"></i>Save & Publish Module
        </button>
    </div>
    
    <!-- Component Templates (Hidden) -->
    <div style="display: none;">
        <!-- Label Component Template -->
        <div id="label-template">
            <div class="mb-3">
                <span class="brc-label">BRC Compliant</span>
                <span class="brc-label warning">Under Review</span>
                <span class="brc-label danger">Critical Risk</span>
            </div>
        </div>
        
        <!-- Smart Form Template -->
        <div id="smart-form-template">
            <div class="brc-smart-form">
                <h5><i class="fas fa-clipboard-list text-primary me-2"></i>HACCP Assessment Form</h5>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Process Step</label>
                        <select class="form-select">
                            <option>Raw Material Receiving</option>
                            <option>Cold Storage</option>
                            <option>Food Preparation</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Assessment Date</label>
                        <input type="date" class="form-control">
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary">Save Assessment</button>
                </div>
            </div>
        </div>
        
        <!-- Timeline Template -->
        <div id="timeline-template">
            <div class="timeline-container">
                <div class="timeline-line"></div>
                <div class="timeline-item">
                    <div class="timeline-marker completed" style="position: absolute; left: 22px; top: 0; width: 20px; height: 20px; border-radius: 50%; background: #28a745; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.7rem;">
                        <i class="fas fa-check"></i>
                    </div>
                    <div style="background: white; border-radius: 10px; padding: 1rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        <h6>HACCP Plan Created</h6>
                        <small class="text-muted">January 8, 2025</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- KPI Cards Template -->
        <div id="kpi-cards-template">
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-center border-0 bg-primary text-white">
                        <div class="card-body">
                            <h3>98%</h3>
                            <small>Compliance Rate</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-0 bg-success text-white">
                        <div class="card-body">
                            <h3>156</h3>
                            <small>Completed</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-0 bg-warning text-white">
                        <div class="card-body">
                            <h3>23</h3>
                            <small>Pending</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-0 bg-danger text-white">
                        <div class="card-body">
                            <h3>5</h3>
                            <small>Critical</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        let draggedComponent = null;
        let selectedComponent = null;
        let componentCounter = 0;
        
        // Drag and Drop functionality
        document.querySelectorAll('.component-item').forEach(item => {
            item.addEventListener('dragstart', (e) => {
                draggedComponent = e.target.dataset.component;
                e.target.style.opacity = '0.5';
            });
            
            item.addEventListener('dragend', (e) => {
                e.target.style.opacity = '1';
            });
        });
        
        const canvas = document.getElementById('moduleCanvas');
        
        canvas.addEventListener('dragover', (e) => {
            e.preventDefault();
            canvas.classList.add('drag-over');
        });
        
        canvas.addEventListener('dragleave', (e) => {
            canvas.classList.remove('drag-over');
        });
        
        canvas.addEventListener('drop', (e) => {
            e.preventDefault();
            canvas.classList.remove('drag-over');
            
            if (draggedComponent) {
                addComponentToCanvas(draggedComponent, e.clientX, e.clientY);
                draggedComponent = null;
            }
        });
        
        function addComponentToCanvas(componentType, x, y) {
            const template = document.getElementById(componentType + '-template');
            if (!template) return;
            
            componentCounter++;
            
            const componentWrapper = document.createElement('div');
            componentWrapper.className = 'dropped-component';
            componentWrapper.dataset.componentType = componentType;
            componentWrapper.dataset.componentId = 'comp_' + componentCounter;
            
            componentWrapper.innerHTML = `
                <div class="component-controls">
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editComponent(this.parentElement.parentElement)">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteComponent(this.parentElement.parentElement)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="component-header-mini">
                    <small class="text-muted">
                        <i class="fas fa-grip-vertical me-1"></i>
                        ${componentType.charAt(0).toUpperCase() + componentType.slice(1).replace('-', ' ')} Component
                    </small>
                </div>
                ${template.innerHTML}
            `;
            
            componentWrapper.addEventListener('click', () => selectComponent(componentWrapper));
            
            // Remove empty state if it exists
            const emptyState = canvas.querySelector('.text-center.text-muted');
            if (emptyState) {
                emptyState.remove();
            }
            
            canvas.appendChild(componentWrapper);
            updateModuleStats();
            showNotification(`${componentType} component added to module`, 'success');
        }
        
        function selectComponent(component) {
            // Remove previous selection
            document.querySelectorAll('.dropped-component').forEach(comp => {
                comp.classList.remove('selected');
            });
            
            // Select new component
            component.classList.add('selected');
            selectedComponent = component;
            
            showComponentProperties(component.dataset.componentType);
        }
        
        function showComponentProperties(componentType) {
            const moduleProps = document.getElementById('module-properties');
            const componentProps = document.getElementById('component-properties');
            const propsContainer = document.getElementById('componentPropsContainer');
            
            moduleProps.style.display = 'none';
            componentProps.style.display = 'block';
            
            // Component-specific properties
            let propsHTML = '';
            
            switch(componentType) {
                case 'label':
                    propsHTML = `
                        <div class="mb-3">
                            <label class="form-label">Label Text</label>
                            <input type="text" class="form-control" value="BRC Compliant">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Label Style</label>
                            <select class="form-select">
                                <option>Success (Green)</option>
                                <option>Warning (Yellow)</option>
                                <option>Danger (Red)</option>
                                <option>Info (Blue)</option>
                            </select>
                        </div>
                    `;
                    break;
                    
                case 'smart-form':
                    propsHTML = `
                        <div class="mb-3">
                            <label class="form-label">Form Title</label>
                            <input type="text" class="form-control" value="HACCP Assessment Form">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Number of Fields</label>
                            <input type="number" class="form-control" value="6" min="1" max="20">
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" checked>
                            <label class="form-check-label">Enable Validation</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" checked>
                            <label class="form-check-label">Auto-save</label>
                        </div>
                    `;
                    break;
                    
                case 'timeline':
                    propsHTML = `
                        <div class="mb-3">
                            <label class="form-label">Timeline Title</label>
                            <input type="text" class="form-control" value="HACCP Approval Process">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Number of Steps</label>
                            <input type="number" class="form-control" value="4" min="2" max="10">
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" checked>
                            <label class="form-check-label">Show Timestamps</label>
                        </div>
                    `;
                    break;
                    
                default:
                    propsHTML = `
                        <div class="mb-3">
                            <label class="form-label">Component Title</label>
                            <input type="text" class="form-control" value="Component">
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" checked>
                            <label class="form-check-label">Visible</label>
                        </div>
                    `;
            }
            
            propsContainer.innerHTML = propsHTML;
        }
        
        function editComponent(component) {
            showNotification('Component edit mode activated', 'info');
            selectComponent(component);
        }
        
        function deleteComponent(component) {
            if (confirm('Are you sure you want to delete this component?')) {
                component.remove();
                updateModuleStats();
                showNotification('Component deleted', 'warning');
                
                // Show module properties again
                document.getElementById('module-properties').style.display = 'block';
                document.getElementById('component-properties').style.display = 'none';
            }
        }
        
        function updateModuleStats() {
            const components = document.querySelectorAll('.dropped-component').length;
            document.getElementById('componentCount').textContent = components;
            document.getElementById('moduleSize').textContent = Math.round(components * 15.5) + ' KB';
            document.getElementById('loadTime').textContent = (0.1 + components * 0.05).toFixed(1) + 's';
        }
        
        function saveModule() {
            const moduleName = document.getElementById('moduleName').value;
            const moduleDescription = document.getElementById('moduleDescription').value;
            const moduleCategory = document.getElementById('moduleCategory').value;
            const modulePrice = document.getElementById('modulePrice').value;
            
            const components = Array.from(document.querySelectorAll('.dropped-component')).map(comp => ({
                id: comp.dataset.componentId,
                type: comp.dataset.componentType,
                html: comp.innerHTML
            }));
            
            const moduleData = {
                name: moduleName,
                description: moduleDescription,
                category: moduleCategory,
                price: modulePrice,
                components: components,
                createdAt: new Date().toISOString()
            };
            
            console.log('Saving module:', moduleData);
            
            // Simulate save process
            const btn = event.target;
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving Module...';
            btn.disabled = true;
            
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-check me-2"></i>Module Saved!';
                btn.className = 'btn btn-success btn-lg';
                
                showNotification('HACCP Management Module saved and published to marketplace!', 'success');
                
                setTimeout(() => {
                    // Redirect to marketplace or module list
                    showNotification('Redirecting to marketplace...', 'info');
                }, 1500);
            }, 2000);
        }
        
        // Tab functionality
        document.querySelectorAll('.module-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.module-tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                // In real implementation, this would switch canvas content
                showNotification(`Switched to ${tab.dataset.tab} tab`, 'info');
            });
        });
        
        // Click outside to deselect component
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.dropped-component') && !e.target.closest('.properties-panel')) {
                document.querySelectorAll('.dropped-component').forEach(comp => {
                    comp.classList.remove('selected');
                });
                
                document.getElementById('module-properties').style.display = 'block';
                document.getElementById('component-properties').style.display = 'none';
            }
        });
        
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
        
        // Initialize
        updateModuleStats();
    </script>
</body>
</html>