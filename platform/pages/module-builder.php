<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Module Builder - BRC Load Platform</title>
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
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
        }

        .builder-layout {
            display: flex;
            height: 100vh;
        }

        /* Top Toolbar */
        .top-toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: white;
            border-bottom: 1px solid var(--border-color);
            z-index: 1100;
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .toolbar-left {
            display: flex;
            align-items: center;
        }

        .toolbar-logo {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-right: 2rem;
        }

        .toolbar-actions {
            display: flex;
            gap: 0.5rem;
        }

        /* Sidebar - Component Library */
        .component-library {
            width: 280px;
            background: white;
            border-right: 1px solid var(--border-color);
            padding-top: 70px;
            overflow-y: auto;
            height: 100vh;
        }

        .library-section {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .library-section h6 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .component-item {
            background: var(--light-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            cursor: grab;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .component-item:hover {
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
        }

        .component-item:active {
            cursor: grabbing;
        }

        .component-item .icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            font-size: 0.875rem;
        }

        .component-item .icon.display {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }

        .component-item .icon.input {
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
        }

        .component-item .icon.action {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
        }

        .component-item .icon.layout {
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            color: white;
        }

        .component-info h6 {
            margin: 0;
            font-size: 0.875rem;
            color: var(--dark-color);
            text-transform: none;
            font-weight: 500;
        }

        .component-info small {
            color: var(--secondary-color);
            font-size: 0.75rem;
        }

        /* Main Canvas */
        .canvas-area {
            flex: 1;
            padding-top: 70px;
            display: flex;
            flex-direction: column;
        }

        .canvas-toolbar {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .canvas-content {
            flex: 1;
            overflow: auto;
            padding: 2rem;
            background: #fafbfc;
            position: relative;
        }

        .canvas-grid {
            background-image: 
                linear-gradient(rgba(0, 0, 0, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 0, 0, 0.05) 1px, transparent 1px);
            background-size: 20px 20px;
            min-height: 800px;
            border-radius: 12px;
            background-color: white;
            position: relative;
            border: 2px dashed var(--border-color);
        }

        .canvas-grid.drag-over {
            border-color: var(--primary-color);
            background-color: rgba(37, 99, 235, 0.05);
        }

        .empty-canvas {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: var(--secondary-color);
        }

        .empty-canvas i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Dropped Components */
        .dropped-component {
            position: absolute;
            background: white;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 1rem;
            min-width: 200px;
            cursor: move;
            transition: all 0.3s ease;
        }

        .dropped-component:hover {
            border-color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
        }

        .dropped-component.selected {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
        }

        .component-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .component-title {
            font-weight: 600;
            color: var(--dark-color);
            font-size: 0.875rem;
        }

        .component-actions {
            display: flex;
            gap: 0.25rem;
        }

        .component-actions button {
            width: 24px;
            height: 24px;
            border: none;
            background: none;
            color: var(--secondary-color);
            border-radius: 4px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .component-actions button:hover {
            background: var(--light-bg);
            color: var(--dark-color);
        }

        /* Properties Panel */
        .properties-panel {
            width: 320px;
            background: white;
            border-left: 1px solid var(--border-color);
            padding-top: 70px;
            overflow-y: auto;
            height: 100vh;
        }

        .properties-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .properties-content {
            padding: 1.5rem;
        }

        .property-group {
            margin-bottom: 1.5rem;
        }

        .property-group label {
            display: block;
            font-weight: 500;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .property-group .form-control,
        .property-group .form-select {
            border-radius: 6px;
            border: 1px solid var(--border-color);
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        .property-group .form-control:focus,
        .property-group .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        /* Tabs */
        .nav-tabs {
            border-bottom: 1px solid var(--border-color);
        }

        .nav-tabs .nav-link {
            border: none;
            color: var(--secondary-color);
            font-weight: 500;
            padding: 0.75rem 1rem;
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            background: none;
            border-bottom: 2px solid var(--primary-color);
        }

        /* Buttons */
        .btn {
            border-radius: 6px;
            font-weight: 500;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #3b82f6);
            border: none;
        }

        .btn-outline-primary {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline-secondary {
            border-color: var(--border-color);
            color: var(--secondary-color);
        }

        /* Preview Mode */
        .preview-mode .component-library,
        .preview-mode .properties-panel {
            display: none;
        }

        .preview-mode .canvas-area {
            width: 100%;
        }

        .preview-mode .dropped-component {
            border: none;
            cursor: default;
        }

        .preview-mode .component-header {
            display: none;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .component-library {
                width: 250px;
            }
            .properties-panel {
                width: 280px;
            }
        }

        @media (max-width: 992px) {
            .component-library,
            .properties-panel {
                position: fixed;
                height: 100vh;
                z-index: 1050;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .component-library.show,
            .properties-panel.show {
                transform: translateX(0);
            }

            .canvas-area {
                width: 100%;
            }
        }

        /* Component Templates */
        .risk-matrix-template {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 2px;
            margin-top: 1rem;
        }

        .risk-cell {
            aspect-ratio: 1;
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
        }

        .risk-cell.low { background: #22c55e; }
        .risk-cell.medium { background: #f59e0b; }
        .risk-cell.high { background: #ef4444; }

        .form-preview {
            background: var(--light-bg);
            border-radius: 6px;
            padding: 1rem;
            margin-top: 1rem;
        }

        .workflow-step {
            background: var(--light-bg);
            border-radius: 6px;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: between;
        }

        .workflow-step .step-number {
            width: 24px;
            height: 24px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            margin-right: 0.75rem;
        }
    </style>
</head>
<body>
    <div class="builder-layout" id="builderLayout">
        <!-- Top Toolbar -->
        <div class="top-toolbar">
            <div class="toolbar-left">
                <div class="toolbar-logo">
                    <i class="fas fa-cubes me-2"></i>BRC Load Builder
                </div>
                <div class="input-group" style="width: 300px;">
                    <input type="text" class="form-control" placeholder="Module Name" value="New BRC Module" id="moduleName">
                </div>
            </div>
            <div class="toolbar-actions">
                <button class="btn btn-outline-secondary me-2" onclick="togglePreview()">
                    <i class="fas fa-eye me-1"></i>Preview
                </button>
                <button class="btn btn-outline-primary me-2" onclick="saveModule()">
                    <i class="fas fa-save me-1"></i>Save Draft
                </button>
                <button class="btn btn-primary" onclick="publishModule()">
                    <i class="fas fa-rocket me-1"></i>Publish
                </button>
            </div>
        </div>

        <!-- Component Library Sidebar -->
        <div class="component-library" id="componentLibrary">
            <!-- Display Components -->
            <div class="library-section">
                <h6>Display Components</h6>
                
                <div class="component-item" draggable="true" data-component="risk-matrix">
                    <div class="icon display">
                        <i class="fas fa-th"></i>
                    </div>
                    <div class="component-info">
                        <h6>Risk Matrix</h6>
                        <small>5x5 risk assessment matrix</small>
                    </div>
                </div>

                <div class="component-item" draggable="true" data-component="chart">
                    <div class="icon display">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="component-info">
                        <h6>Chart</h6>
                        <small>Data visualization charts</small>
                    </div>
                </div>

                <div class="component-item" draggable="true" data-component="status-tracker">
                    <div class="icon display">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="component-info">
                        <h6>Status Tracker</h6>
                        <small>Progress tracking display</small>
                    </div>
                </div>

                <div class="component-item" draggable="true" data-component="kpi-card">
                    <div class="icon display">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <div class="component-info">
                        <h6>KPI Card</h6>
                        <small>Key metrics display</small>
                    </div>
                </div>
            </div>

            <!-- Input Components -->
            <div class="library-section">
                <h6>Input Components</h6>
                
                <div class="component-item" draggable="true" data-component="smart-form">
                    <div class="icon input">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="component-info">
                        <h6>Smart Form</h6>
                        <small>Dynamic form builder</small>
                    </div>
                </div>

                <div class="component-item" draggable="true" data-component="file-upload">
                    <div class="icon input">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div class="component-info">
                        <h6>File Upload</h6>
                        <small>Document upload widget</small>
                    </div>
                </div>

                <div class="component-item" draggable="true" data-component="date-picker">
                    <div class="icon input">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="component-info">
                        <h6>Date Picker</h6>
                        <small>Date/time selection</small>
                    </div>
                </div>

                <div class="component-item" draggable="true" data-component="signature-pad">
                    <div class="icon input">
                        <i class="fas fa-signature"></i>
                    </div>
                    <div class="component-info">
                        <h6>Signature Pad</h6>
                        <small>Digital signature capture</small>
                    </div>
                </div>
            </div>

            <!-- Action Components -->
            <div class="library-section">
                <h6>Action Components</h6>
                
                <div class="component-item" draggable="true" data-component="approval-flow">
                    <div class="icon action">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="component-info">
                        <h6>Approval Flow</h6>
                        <small>Multi-step approval process</small>
                    </div>
                </div>

                <div class="component-item" draggable="true" data-component="notification">
                    <div class="icon action">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="component-info">
                        <h6>Notifications</h6>
                        <small>Alert and notification system</small>
                    </div>
                </div>

                <div class="component-item" draggable="true" data-component="report-generator">
                    <div class="icon action">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="component-info">
                        <h6>Report Generator</h6>
                        <small>Automated report creation</small>
                    </div>
                </div>
            </div>

            <!-- Layout Components -->
            <div class="library-section">
                <h6>Layout Components</h6>
                
                <div class="component-item" draggable="true" data-component="dashboard-grid">
                    <div class="icon layout">
                        <i class="fas fa-th-large"></i>
                    </div>
                    <div class="component-info">
                        <h6>Dashboard Grid</h6>
                        <small>Responsive grid layout</small>
                    </div>
                </div>

                <div class="component-item" draggable="true" data-component="card-container">
                    <div class="icon layout">
                        <i class="fas fa-window-maximize"></i>
                    </div>
                    <div class="component-info">
                        <h6>Card Container</h6>
                        <small>Content card wrapper</small>
                    </div>
                </div>

                <div class="component-item" draggable="true" data-component="tab-panel">
                    <div class="icon layout">
                        <i class="fas fa-folder"></i>
                    </div>
                    <div class="component-info">
                        <h6>Tab Panel</h6>
                        <small>Tabbed content organizer</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Canvas Area -->
        <div class="canvas-area">
            <div class="canvas-toolbar">
                <div class="d-flex align-items-center">
                    <div class="btn-group me-3" role="group">
                        <input type="radio" class="btn-check" name="viewMode" id="design" checked>
                        <label class="btn btn-outline-secondary" for="design">
                            <i class="fas fa-paint-brush me-1"></i>Design
                        </label>
                        
                        <input type="radio" class="btn-check" name="viewMode" id="workflow">
                        <label class="btn btn-outline-secondary" for="workflow">
                            <i class="fas fa-sitemap me-1"></i>Workflow
                        </label>
                        
                        <input type="radio" class="btn-check" name="viewMode" id="settings">
                        <label class="btn btn-outline-secondary" for="settings">
                            <i class="fas fa-cog me-1"></i>Settings
                        </label>
                    </div>
                    
                    <div class="btn-group" role="group">
                        <button class="btn btn-outline-secondary" onclick="undoAction()">
                            <i class="fas fa-undo"></i>
                        </button>
                        <button class="btn btn-outline-secondary" onclick="redoAction()">
                            <i class="fas fa-redo"></i>
                        </button>
                    </div>
                </div>
                
                <div class="d-flex align-items-center">
                    <span class="text-muted me-3">Zoom:</span>
                    <div class="btn-group" role="group">
                        <button class="btn btn-outline-secondary btn-sm" onclick="zoomOut()">
                            <i class="fas fa-minus"></i>
                        </button>
                        <span class="btn btn-outline-secondary btn-sm" id="zoomLevel">100%</span>
                        <button class="btn btn-outline-secondary btn-sm" onclick="zoomIn()">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="canvas-content">
                <div class="canvas-grid" id="canvasGrid" ondrop="drop(event)" ondragover="allowDrop(event)">
                    <div class="empty-canvas" id="emptyCanvas">
                        <i class="fas fa-mouse-pointer"></i>
                        <h5>Start Building Your Module</h5>
                        <p>Drag components from the left panel to begin designing</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Properties Panel -->
        <div class="properties-panel" id="propertiesPanel">
            <div class="properties-header">
                <h6 class="mb-0">Properties</h6>
                <small class="text-muted">Configure selected component</small>
            </div>

            <div class="properties-content" id="propertiesContent">
                <div class="text-center text-muted py-5">
                    <i class="fas fa-hand-pointer fa-2x mb-3"></i>
                    <p>Select a component to edit its properties</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedComponent = null;
        let componentCounter = 0;
        let isPreviewMode = false;
        let moduleData = {
            components: [],
            workflows: [],
            settings: {}
        };

        // Drag and Drop functionality
        function allowDrop(ev) {
            ev.preventDefault();
            document.getElementById('canvasGrid').classList.add('drag-over');
        }

        function drop(ev) {
            ev.preventDefault();
            const canvas = document.getElementById('canvasGrid');
            canvas.classList.remove('drag-over');
            
            const componentType = ev.dataTransfer.getData("component");
            if (componentType) {
                createComponent(componentType, ev.clientX, ev.clientY);
            }
        }

        // Add drag start event to component items
        document.querySelectorAll('.component-item').forEach(item => {
            item.addEventListener('dragstart', function(e) {
                e.dataTransfer.setData("component", this.dataset.component);
            });
        });

        // Create component on canvas
        function createComponent(type, x, y) {
            const canvas = document.getElementById('canvasGrid');
            const canvasRect = canvas.getBoundingClientRect();
            
            componentCounter++;
            const componentId = `component_${componentCounter}`;
            
            const component = document.createElement('div');
            component.className = 'dropped-component';
            component.id = componentId;
            component.onclick = (e) => selectComponent(e, componentId);
            
            // Position relative to canvas
            const relativeX = x - canvasRect.left - 100;
            const relativeY = y - canvasRect.top - 50;
            component.style.left = Math.max(0, relativeX) + 'px';
            component.style.top = Math.max(0, relativeY) + 'px';
            
            component.innerHTML = generateComponentHTML(type, componentId);
            
            canvas.appendChild(component);
            
            // Hide empty canvas message
            document.getElementById('emptyCanvas').style.display = 'none';
            
            // Select the new component
            selectComponent(null, componentId);
            
            // Save to module data
            moduleData.components.push({
                id: componentId,
                type: type,
                position: { x: relativeX, y: relativeY },
                properties: getDefaultProperties(type)
            });
        }

        // Generate component HTML based on type
        function generateComponentHTML(type, id) {
            const templates = {
                'risk-matrix': `
                    <div class="component-header">
                        <span class="component-title">Risk Assessment Matrix</span>
                        <div class="component-actions">
                            <button onclick="editComponent('${id}')" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteComponent('${id}')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="risk-matrix-template">
                        <div class="risk-cell low">1</div>
                        <div class="risk-cell low">2</div>
                        <div class="risk-cell medium">3</div>
                        <div class="risk-cell medium">4</div>
                        <div class="risk-cell high">5</div>
                        <div class="risk-cell low">2</div>
                        <div class="risk-cell medium">4</div>
                        <div class="risk-cell medium">6</div>
                        <div class="risk-cell high">8</div>
                        <div class="risk-cell high">10</div>
                        <div class="risk-cell medium">3</div>
                        <div class="risk-cell medium">6</div>
                        <div class="risk-cell high">9</div>
                        <div class="risk-cell high">12</div>
                        <div class="risk-cell high">15</div>
                        <div class="risk-cell medium">4</div>
                        <div class="risk-cell high">8</div>
                        <div class="risk-cell high">12</div>
                        <div class="risk-cell high">16</div>
                        <div class="risk-cell high">20</div>
                        <div class="risk-cell high">5</div>
                        <div class="risk-cell high">10</div>
                        <div class="risk-cell high">15</div>
                        <div class="risk-cell high">20</div>
                        <div class="risk-cell high">25</div>
                    </div>
                `,
                'smart-form': `
                    <div class="component-header">
                        <span class="component-title">Smart Form</span>
                        <div class="component-actions">
                            <button onclick="editComponent('${id}')" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteComponent('${id}')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-preview">
                        <div class="mb-3">
                            <label class="form-label">Risk Description</label>
                            <input type="text" class="form-control" placeholder="Enter risk description">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Likelihood</label>
                            <select class="form-select">
                                <option>Very Low</option>
                                <option>Low</option>
                                <option>Medium</option>
                                <option>High</option>
                                <option>Very High</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Impact</label>
                            <select class="form-select">
                                <option>Negligible</option>
                                <option>Minor</option>
                                <option>Moderate</option>
                                <option>Major</option>
                                <option>Catastrophic</option>
                            </select>
                        </div>
                    </div>
                `,
                'approval-flow': `
                    <div class="component-header">
                        <span class="component-title">Approval Workflow</span>
                        <div class="component-actions">
                            <button onclick="editComponent('${id}')" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteComponent('${id}')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <div class="workflow-step">
                            <div class="step-number">1</div>
                            <div class="flex-grow-1">
                                <strong>Risk Assessor Review</strong>
                                <div class="text-muted small">Initial review by risk assessor</div>
                            </div>
                        </div>
                        <div class="workflow-step">
                            <div class="step-number">2</div>
                            <div class="flex-grow-1">
                                <strong>Manager Approval</strong>
                                <div class="text-muted small">Department manager approval</div>
                            </div>
                        </div>
                        <div class="workflow-step">
                            <div class="step-number">3</div>
                            <div class="flex-grow-1">
                                <strong>Final Sign-off</strong>
                                <div class="text-muted small">Quality manager final approval</div>
                            </div>
                        </div>
                    </div>
                `
            };
            
            return templates[type] || `
                <div class="component-header">
                    <span class="component-title">${type.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase())}</span>
                    <div class="component-actions">
                        <button onclick="editComponent('${id}')" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteComponent('${id}')" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="p-3 text-center text-muted">
                    <i class="fas fa-puzzle-piece fa-2x mb-2"></i>
                    <p>${type.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase())} Component</p>
                </div>
            `;
        }

        // Select component
        function selectComponent(event, componentId) {
            if (event) event.stopPropagation();
            
            // Remove previous selection
            document.querySelectorAll('.dropped-component').forEach(comp => {
                comp.classList.remove('selected');
            });
            
            // Select new component
            const component = document.getElementById(componentId);
            if (component) {
                component.classList.add('selected');
                selectedComponent = componentId;
                showComponentProperties(componentId);
            }
        }

        // Show component properties
        function showComponentProperties(componentId) {
            const componentData = moduleData.components.find(c => c.id === componentId);
            if (!componentData) return;
            
            const propertiesContent = document.getElementById('propertiesContent');
            propertiesContent.innerHTML = generatePropertiesForm(componentData);
        }

        // Generate properties form
        function generatePropertiesForm(componentData) {
            const commonProperties = `
                <div class="property-group">
                    <label>Component Name</label>
                    <input type="text" class="form-control" value="${componentData.type}" onchange="updateProperty('${componentData.id}', 'name', this.value)">
                </div>
                <div class="property-group">
                    <label>Width</label>
                    <input type="text" class="form-control" value="auto" onchange="updateProperty('${componentData.id}', 'width', this.value)">
                </div>
                <div class="property-group">
                    <label>Height</label>
                    <input type="text" class="form-control" value="auto" onchange="updateProperty('${componentData.id}', 'height', this.value)">
                </div>
                <div class="property-group">
                    <label>Visibility</label>
                    <select class="form-select" onchange="updateProperty('${componentData.id}', 'visibility', this.value)">
                        <option value="always">Always Visible</option>
                        <option value="conditional">Conditional</option>
                        <option value="admin-only">Admin Only</option>
                    </select>
                </div>
            `;
            
            let specificProperties = '';
            
            if (componentData.type === 'smart-form') {
                specificProperties = `
                    <hr>
                    <h6>Form Configuration</h6>
                    <div class="property-group">
                        <label>Form Title</label>
                        <input type="text" class="form-control" value="Risk Assessment Form">
                    </div>
                    <div class="property-group">
                        <label>Submit Button Text</label>
                        <input type="text" class="form-control" value="Submit Assessment">
                    </div>
                    <div class="property-group">
                        <label>Validation Rules</label>
                        <select class="form-select">
                            <option>Standard Validation</option>
                            <option>BRC Compliance</option>
                            <option>Custom Rules</option>
                        </select>
                    </div>
                `;
            } else if (componentData.type === 'risk-matrix') {
                specificProperties = `
                    <hr>
                    <h6>Matrix Configuration</h6>
                    <div class="property-group">
                        <label>Matrix Size</label>
                        <select class="form-select">
                            <option value="5x5" selected>5x5 Matrix</option>
                            <option value="4x4">4x4 Matrix</option>
                            <option value="3x3">3x3 Matrix</option>
                        </select>
                    </div>
                    <div class="property-group">
                        <label>Color Scheme</label>
                        <select class="form-select">
                            <option>Standard (Green/Yellow/Red)</option>
                            <option>High Contrast</option>
                            <option>Monochrome</option>
                        </select>
                    </div>
                `;
            }
            
            return commonProperties + specificProperties;
        }

        // Update component property
        function updateProperty(componentId, property, value) {
            const component = moduleData.components.find(c => c.id === componentId);
            if (component) {
                component.properties[property] = value;
                console.log('Updated property:', componentId, property, value);
            }
        }

        // Get default properties for component type
        function getDefaultProperties(type) {
            return {
                name: type,
                width: 'auto',
                height: 'auto',
                visibility: 'always'
            };
        }

        // Delete component
        function deleteComponent(componentId) {
            if (confirm('Are you sure you want to delete this component?')) {
                const component = document.getElementById(componentId);
                if (component) {
                    component.remove();
                    
                    // Remove from module data
                    moduleData.components = moduleData.components.filter(c => c.id !== componentId);
                    
                    // Clear properties panel
                    document.getElementById('propertiesContent').innerHTML = `
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-hand-pointer fa-2x mb-3"></i>
                            <p>Select a component to edit its properties</p>
                        </div>
                    `;
                    
                    // Show empty canvas if no components
                    if (moduleData.components.length === 0) {
                        document.getElementById('emptyCanvas').style.display = 'block';
                    }
                }
            }
        }

        // Toggle preview mode
        function togglePreview() {
            isPreviewMode = !isPreviewMode;
            const layout = document.getElementById('builderLayout');
            
            if (isPreviewMode) {
                layout.classList.add('preview-mode');
            } else {
                layout.classList.remove('preview-mode');
            }
        }

        // Save module
        function saveModule() {
            const moduleName = document.getElementById('moduleName').value;
            moduleData.name = moduleName;
            moduleData.lastSaved = new Date().toISOString();
            
            console.log('Saving module:', moduleData);
            
            // Here you would send to backend
            alert('Module saved successfully!');
        }

        // Publish module
        function publishModule() {
            if (moduleData.components.length === 0) {
                alert('Please add at least one component before publishing.');
                return;
            }
            
            if (confirm('Are you sure you want to publish this module? It will become available in the marketplace.')) {
                console.log('Publishing module:', moduleData);
                alert('Module published successfully!');
            }
        }

        // Toolbar actions
        function undoAction() {
            console.log('Undo action');
        }

        function redoAction() {
            console.log('Redo action');
        }

        function zoomIn() {
            console.log('Zoom in');
        }

        function zoomOut() {
            console.log('Zoom out');
        }

        // Clear selection when clicking on canvas
        document.getElementById('canvasGrid').addEventListener('click', function(e) {
            if (e.target === this) {
                document.querySelectorAll('.dropped-component').forEach(comp => {
                    comp.classList.remove('selected');
                });
                selectedComponent = null;
                
                document.getElementById('propertiesContent').innerHTML = `
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-hand-pointer fa-2x mb-3"></i>
                        <p>Select a component to edit its properties</p>
                    </div>
                `;
            }
        });

        // Remove drag over class when drag leaves
        document.getElementById('canvasGrid').addEventListener('dragleave', function(e) {
            this.classList.remove('drag-over');
        });
    </script>
</body>
</html>