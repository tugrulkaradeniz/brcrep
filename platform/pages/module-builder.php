<?php
// platform/pages/module-builder.php - Drag & Drop Modül Oluşturucu

// Admin kontrolü
if (!isset($_SESSION['platform_admin_id'])) {
    header('Location: /brcproject/platform/auth/login.php');
    exit;
}

// Edit mode kontrolü
$editMode = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$pageTitle = $editMode ? 'Modül Düzenle' : 'Yeni Modül Oluştur';

$adminName = $_SESSION['platform_admin_name'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - BRC Load Platform</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #333;
            overflow-x: hidden;
        }
        
        /* Header */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .header-right {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        /* Layout */
        .builder-container {
            display: grid;
            grid-template-columns: 280px 1fr 320px;
            height: calc(100vh - 80px);
            gap: 0;
        }
        
        /* Component Library */
        .component-library {
            background: white;
            border-right: 1px solid #e5e7eb;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
        }
        
        .library-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background: #f8f9fa;
        }
        
        .library-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }
        
        .library-subtitle {
            font-size: 0.9rem;
            color: #666;
        }
        
        .component-category {
            border-bottom: 1px solid #f0f0f0;
        }
        
        .category-header {
            padding: 1rem 1.5rem;
            background: #f8f9fa;
            font-weight: 600;
            color: #555;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s;
        }
        
        .category-header:hover {
            background: #e9ecef;
        }
        
        .category-toggle {
            font-size: 0.8rem;
            transition: transform 0.2s;
        }
        
        .category-content {
            padding: 0.5rem;
        }
        
        .component-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            margin: 0.25rem;
            border: 2px solid transparent;
            border-radius: 8px;
            cursor: grab;
            transition: all 0.2s;
            background: white;
        }
        
        .component-item:hover {
            background: #f8f9fa;
            border-color: #667eea;
            transform: translateX(4px);
        }
        
        .component-item:active {
            cursor: grabbing;
            transform: rotate(2deg);
        }
        
        .component-icon {
            font-size: 1.5rem;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
        }
        
        .component-info {
            flex: 1;
        }
        
        .component-name {
            font-weight: 500;
            font-size: 0.9rem;
            color: #333;
        }
        
        .component-desc {
            font-size: 0.8rem;
            color: #666;
            line-height: 1.3;
        }
        
        /* Canvas Area */
        .canvas-area {
            background: #ffffff;
            position: relative;
            overflow: auto;
        }
        
        .canvas-toolbar {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background: #fafbfc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .canvas-title {
            font-weight: 600;
            color: #333;
        }
        
        .canvas-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .canvas-viewport {
            padding: 2rem;
            min-height: calc(100vh - 200px);
            background-image: 
                radial-gradient(circle, #e5e7eb 1px, transparent 1px);
            background-size: 20px 20px;
            position: relative;
        }
        
        .drop-zone {
            min-height: 400px;
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.8);
            transition: all 0.3s;
            position: relative;
        }
        
        .drop-zone.drag-over {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
            transform: scale(1.02);
        }
        
        .drop-placeholder {
            text-align: center;
            color: #9ca3af;
        }
        
        .drop-placeholder h3 {
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }
        
        .drop-placeholder p {
            font-size: 0.9rem;
        }
        
        /* Properties Panel */
        .properties-panel {
            background: white;
            border-left: 1px solid #e5e7eb;
            overflow-y: auto;
            box-shadow: -2px 0 10px rgba(0,0,0,0.05);
        }
        
        .panel-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background: #f8f9fa;
        }
        
        .panel-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }
        
        .panel-content {
            padding: 1.5rem;
        }
        
        .property-group {
            margin-bottom: 1.5rem;
        }
        
        .property-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
            font-size: 0.9rem;
        }
        
        .property-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: border-color 0.2s;
        }
        
        .property-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        /* Buttons */
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid #d1d5db;
            color: #374151;
        }
        
        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.8rem;
        }
        
        /* Dropped Components */
        .dropped-component {
            margin: 1rem 0;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            background: white;
            position: relative;
            transition: all 0.2s;
        }
        
        .dropped-component:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
        }
        
        .dropped-component.selected {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .component-controls {
            position: absolute;
            top: -12px;
            right: 8px;
            display: flex;
            gap: 0.25rem;
            opacity: 0;
            transition: opacity 0.2s;
        }
        
        .dropped-component:hover .component-controls {
            opacity: 1;
        }
        
        .control-btn {
            width: 24px;
            height: 24px;
            border: none;
            border-radius: 4px;
            font-size: 0.7rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .control-edit {
            background: #fbbf24;
            color: white;
        }
        
        .control-delete {
            background: #ef4444;
            color: white;
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .builder-container {
                grid-template-columns: 260px 1fr 280px;
            }
        }
        
        @media (max-width: 768px) {
            .builder-container {
                grid-template-columns: 1fr;
                grid-template-rows: auto 1fr auto;
            }
            
            .component-library,
            .properties-panel {
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-left">
            <h1>🎨 <?php echo $pageTitle; ?></h1>
        </div>
        <div class="header-right">
            <button class="btn btn-outline" onclick="previewModule()">👁️ Preview</button>
            <button class="btn btn-secondary" onclick="saveModule()">💾 Kaydet</button>
            <button class="btn btn-success" onclick="publishModule()">🚀 Yayınla</button>
            <a href="/brcproject/admin/modules" class="btn btn-outline">← Geri</a>
        </div>
    </header>
    
    <div class="builder-container">
        <!-- Component Library -->
        <div class="component-library">
            <div class="library-header">
                <h3 class="library-title">🧱 Component Library</h3>
                <p class="library-subtitle">Drag & drop components</p>
            </div>
            
            <!-- Display Components -->
            <div class="component-category">
                <div class="category-header" onclick="toggleCategory('display')">
                    <span>🎯 Display Components</span>
                    <span class="category-toggle">▼</span>
                </div>
                <div class="category-content" id="display-components">
                    <div class="component-item" draggable="true" data-type="quality-control-table">
                        <div class="component-icon">🏭</div>
                        <div class="component-info">
                            <div class="component-name">Quality Control Table</div>
                            <div class="component-desc">Kalite kontrol süreç tablosu</div>
                        </div>
                    </div>
                    <div class="component-item" draggable="true" data-type="risk-matrix">
                        <div class="component-icon">🛡️</div>
                        <div class="component-info">
                            <div class="component-name">Risk Matrix</div>
                            <div class="component-desc">5x5 risk assessment matrix</div>
                        </div>
                    </div>
                    <div class="component-item" draggable="true" data-type="chart">
                        <div class="component-icon">📊</div>
                        <div class="component-info">
                            <div class="component-name">Chart</div>
                            <div class="component-desc">Data visualization charts</div>
                        </div>
                    </div>
                    <div class="component-item" draggable="true" data-type="kpi-card">
                        <div class="component-icon">📈</div>
                        <div class="component-info">
                            <div class="component-name">KPI Card</div>
                            <div class="component-desc">Key performance indicators</div>
                        </div>
                    </div>
                    <div class="component-item" draggable="true" data-type="status-tracker">
                        <div class="component-icon">🎯</div>
                        <div class="component-info">
                            <div class="component-name">Status Tracker</div>
                            <div class="component-desc">Progress tracking display</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Input Components -->
            <div class="component-category">
                <div class="category-header" onclick="toggleCategory('input')">
                    <span>📝 Input Components</span>
                    <span class="category-toggle">▼</span>
                </div>
                <div class="category-content" id="input-components">
                    <div class="component-item" draggable="true" data-type="smart-form">
                        <div class="component-icon">📝</div>
                        <div class="component-info">
                            <div class="component-name">Smart Form</div>
                            <div class="component-desc">Dynamic form builder</div>
                        </div>
                    </div>
                    <div class="component-item" draggable="true" data-type="file-upload">
                        <div class="component-icon">📁</div>
                        <div class="component-info">
                            <div class="component-name">File Upload</div>
                            <div class="component-desc">File upload with validation</div>
                        </div>
                    </div>
                    <div class="component-item" draggable="true" data-type="date-picker">
                        <div class="component-icon">📅</div>
                        <div class="component-info">
                            <div class="component-name">Date Picker</div>
                            <div class="component-desc">Advanced date selection</div>
                        </div>
                    </div>
                    <div class="component-item" draggable="true" data-type="signature-pad">
                        <div class="component-icon">✍️</div>
                        <div class="component-info">
                            <div class="component-name">Signature Pad</div>
                            <div class="component-desc">Digital signature capture</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Components -->
            <div class="component-category">
                <div class="category-header" onclick="toggleCategory('action')">
                    <span>⚡ Action Components</span>
                    <span class="category-toggle">▼</span>
                </div>
                <div class="category-content" id="action-components">
                    <div class="component-item" draggable="true" data-type="approval-flow">
                        <div class="component-icon">✅</div>
                        <div class="component-info">
                            <div class="component-name">Approval Flow</div>
                            <div class="component-desc">Multi-step approval process</div>
                        </div>
                    </div>
                    <div class="component-item" draggable="true" data-type="notification">
                        <div class="component-icon">🔔</div>
                        <div class="component-info">
                            <div class="component-name">Notification</div>
                            <div class="component-desc">Email & SMS notifications</div>
                        </div>
                    </div>
                    <div class="component-item" draggable="true" data-type="report-generator">
                        <div class="component-icon">📋</div>
                        <div class="component-info">
                            <div class="component-name">Report Generator</div>
                            <div class="component-desc">Automated report creation</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Layout Components -->
            <div class="component-category">
                <div class="category-header" onclick="toggleCategory('layout')">
                    <span>📋 Layout Components</span>
                    <span class="category-toggle">▼</span>
                </div>
                <div class="category-content" id="layout-components">
                    <div class="component-item" draggable="true" data-type="dashboard-grid">
                        <div class="component-icon">▦</div>
                        <div class="component-info">
                            <div class="component-name">Dashboard Grid</div>
                            <div class="component-desc">Responsive grid layout</div>
                        </div>
                    </div>
                    <div class="component-item" draggable="true" data-type="card-container">
                        <div class="component-icon">🗃️</div>
                        <div class="component-info">
                            <div class="component-name">Card Container</div>
                            <div class="component-desc">Flexible card layout</div>
                        </div>
                    </div>
                    <div class="component-item" draggable="true" data-type="tab-panel">
                        <div class="component-icon">📑</div>
                        <div class="component-info">
                            <div class="component-name">Tab Panel</div>
                            <div class="component-desc">Tabbed content organization</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Canvas Area -->
        <div class="canvas-area">
            <div class="canvas-toolbar">
                <div class="canvas-title">
                    📱 Module Canvas
                    <span id="moduleTitle" style="color: #667eea; margin-left: 1rem;">Untitled Module</span>
                </div>
                <div class="canvas-actions">
                    <button class="btn btn-outline btn-sm" onclick="clearCanvas()">🗑️ Clear</button>
                    <button class="btn btn-outline btn-sm" onclick="undoAction()">↶ Undo</button>
                    <button class="btn btn-outline btn-sm" onclick="redoAction()">↷ Redo</button>
                </div>
            </div>
            
            <div class="canvas-viewport">
                <div class="drop-zone" id="dropZone">
                    <div class="drop-placeholder">
                        <h3>🎨 Start Building Your Module</h3>
                        <p>Drag components from the left panel and drop them here</p>
                        <p style="margin-top: 0.5rem; font-size: 0.8rem; opacity: 0.7;">
                            Risk Matrix • Smart Forms • Approval Workflows • And More!
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Properties Panel -->
        <div class="properties-panel">
            <div class="panel-header">
                <h3 class="panel-title">⚙️ Properties</h3>
            </div>
            <div class="panel-content" id="propertiesContent">
                <div class="property-group">
                    <label class="property-label">Module Name</label>
                    <input type="text" class="property-input" id="moduleName" placeholder="Enter module name..." value="<?php echo $editMode ? 'Existing Module' : ''; ?>">
                </div>
                
                <div class="property-group">
                    <label class="property-label">Description</label>
                    <textarea class="property-input" id="moduleDescription" rows="3" placeholder="Describe your module..."></textarea>
                </div>
                
                <div class="property-group">
                    <label class="property-label">Category</label>
                    <select class="property-input" id="moduleCategory">
                        <option value="">Select category...</option>
                        <option value="BRC Compliance">BRC Compliance</option>
                        <option value="Quality Management">Quality Management</option>
                        <option value="Safety & Health">Safety & Health</option>
                        <option value="Audit Management">Audit Management</option>
                        <option value="Document Control">Document Control</option>
                        <option value="Training & Development">Training & Development</option>
                        <option value="Custom">Custom</option>
                    </select>
                </div>
                
                <div class="property-group">
                    <label class="property-label">Version</label>
                    <input type="text" class="property-input" id="moduleVersion" placeholder="1.0.0" value="1.0.0">
                </div>
                
                <div class="property-group">
                    <label class="property-label">Price ($)</label>
                    <input type="number" class="property-input" id="modulePrice" placeholder="299" min="0">
                </div>
                
                <hr style="margin: 1.5rem 0; border: none; border-top: 1px solid #e5e7eb;">
                
                <div id="componentProperties">
                    <p style="color: #9ca3af; font-size: 0.9rem; text-align: center; margin: 2rem 0;">
                        Select a component to edit its properties
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Global variables
        let moduleData = {
            name: '',
            description: '',
            category: '',
            version: '1.0.0',
            price: 0,
            components: []
        };
        
        let selectedComponent = null;
        let draggedComponent = null;
        let componentCounter = 0;
        
        // Initialize the module builder
        document.addEventListener('DOMContentLoaded', function() {
            initializeDragAndDrop();
            initializeProperties();
            
            <?php if ($editMode): ?>
            loadModuleData(<?php echo $editMode; ?>);
            <?php endif; ?>
            
            console.log('Module Builder initialized');
        });
        
        // Drag and Drop functionality
        function initializeDragAndDrop() {
            const componentItems = document.querySelectorAll('.component-item');
            const dropZone = document.getElementById('dropZone');
            
            // Make components draggable
            componentItems.forEach(item => {
                item.addEventListener('dragstart', function(e) {
                    draggedComponent = {
                        type: this.dataset.type,
                        name: this.querySelector('.component-name').textContent,
                        icon: this.querySelector('.component-icon').textContent
                    };
                    
                    this.style.opacity = '0.5';
                    e.dataTransfer.effectAllowed = 'copy';
                });
                
                item.addEventListener('dragend', function(e) {
                    this.style.opacity = '1';
                });
            });
            
            // Drop zone events
            dropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'copy';
                this.classList.add('drag-over');
            });
            
            dropZone.addEventListener('dragleave', function(e) {
                this.classList.remove('drag-over');
            });
            
            dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');
                
                if (draggedComponent) {
                    addComponentToCanvas(draggedComponent);
                    draggedComponent = null;
                }
            });
        }
        
        // Add component to canvas
        function addComponentToCanvas(componentData) {
            componentCounter++;
            const componentId = `component_${componentCounter}`;
            
            // Remove placeholder if this is the first component
            const placeholder = document.querySelector('.drop-placeholder');
            if (placeholder) {
                placeholder.remove();
            }
            
            // Create component element
            const componentElement = createComponentElement(componentId, componentData);
            document.getElementById('dropZone').appendChild(componentElement);
            
            // Add to module data
            moduleData.components.push({
                id: componentId,
                type: componentData.type,
                name: componentData.name,
                icon: componentData.icon,
                properties: getDefaultProperties(componentData.type)
            });
            
            // Select the new component
            selectComponent(componentId);
            
            console.log('Component added:', componentData);
        }
        
        // Create component element
        function createComponentElement(id, data) {
            const element = document.createElement('div');
            element.className = 'dropped-component';
            element.id = id;
            element.onclick = () => selectComponent(id);
            
            element.innerHTML = `
                <div class="component-controls">
                    <button class="control-btn control-edit" onclick="editComponent('${id}')" title="Edit">✏️</button>
                    <button class="control-btn control-delete" onclick="deleteComponent('${id}')" title="Delete">🗑️</button>
                </div>
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <span style="font-size: 2rem;">${data.icon}</span>
                    <div>
                        <h4 style="margin: 0; color: #333;">${data.name}</h4>
                        <p style="margin: 0; color: #666; font-size: 0.9rem;">Type: ${data.type}</p>
                    </div>
                </div>
                <div class="component-preview">
                    ${getComponentPreview(data.type)}
                </div>
            `;
            
            return element;
        }
        
        // Get component preview HTML
        function getComponentPreview(type) {
            const previews = {
                'quality-control-table': `
                    <div style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden; margin: 1rem 0;">
                        <div style="background: #f8f9fa; padding: 0.75rem; border-bottom: 1px solid #ddd; font-weight: 600; color: #333;">
                            🏭 Kalite Kontrol Süreci
                        </div>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.8rem;">
                                <thead>
                                    <tr style="background: #e9ecef;">
                                        <th style="padding: 0.5rem; border: 1px solid #ddd; text-align: left;">AŞAMA</th>
                                        <th style="padding: 0.5rem; border: 1px solid #ddd; text-align: left;">KRİTER</th>
                                        <th style="padding: 0.5rem; border: 1px solid #ddd; text-align: left;">SIKLIK</th>
                                        <th style="padding: 0.5rem; border: 1px solid #ddd; text-align: left;">KABUL KRİTERİ</th>
                                        <th style="padding: 0.5rem; border: 1px solid #ddd; text-align: left;">KONTROL SORUMLUSU</th>
                                        <th style="padding: 0.5rem; border: 1px solid #ddd; text-align: left;">SAPMA DURUMUNDA YAPILACAK</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="padding: 0.4rem; border: 1px solid #ddd;">Hammadde Kontrolü</td>
                                        <td style="padding: 0.4rem; border: 1px solid #ddd;">Görsel Kontrol</td>
                                        <td style="padding: 0.4rem; border: 1px solid #ddd;">Her parti</td>
                                        <td style="padding: 0.4rem; border: 1px solid #ddd;">Spec. uygun</td>
                                        <td style="padding: 0.4rem; border: 1px solid #ddd;">QC Uzmanı</td>
                                        <td style="padding: 0.4rem; border: 1px solid #ddd;">Red - Değiştir</td>
                                    </tr>
                                    <tr style="background: #f8f9fa;">
                                        <td style="padding: 0.4rem; border: 1px solid #ddd;">Üretim Süreci</td>
                                        <td style="padding: 0.4rem; border: 1px solid #ddd;">Sıcaklık</td>
                                        <td style="padding: 0.4rem; border: 1px solid #ddd;">2 saatte bir</td>
                                        <td style="padding: 0.4rem; border: 1px solid #ddd;">±2°C</td>
                                        <td style="padding: 0.4rem; border: 1px solid #ddd;">Operatör</td>
                                        <td style="padding: 0.4rem; border: 1px solid #ddd;">Ayar yap</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                `,
                'risk-matrix': `
                    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 2px; margin: 1rem 0;">
                        ${Array(25).fill(0).map((_, i) => {
                            const colors = ['#28a745', '#ffc107', '#fd7e14', '#dc3545', '#6f42c1'];
                            return `<div style="width: 30px; height: 30px; background: ${colors[i % 5]}; border-radius: 4px; opacity: 0.7;"></div>`;
                        }).join('')}
                    </div>
                    <p style="color: #666; font-size: 0.8rem;">5x5 Risk Assessment Matrix</p>
                `,
                'smart-form': `
                    <div style="border: 1px solid #ddd; padding: 1rem; border-radius: 6px; background: #f9f9f9;">
                        <div style="margin-bottom: 0.5rem; padding: 0.5rem; background: white; border-radius: 4px;">📝 Field 1</div>
                        <div style="margin-bottom: 0.5rem; padding: 0.5rem; background: white; border-radius: 4px;">📅 Field 2</div>
                        <div style="margin-bottom: 0.5rem; padding: 0.5rem; background: white; border-radius: 4px;">📁 Field 3</div>
                    </div>
                `,
                'chart': `
                    <div style="border: 1px solid #ddd; padding: 1rem; border-radius: 6px; background: #f9f9f9; text-align: center;">
                        <div style="height: 80px; background: linear-gradient(45deg, #667eea, #764ba2); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: white;">
                            📊 Chart Preview
                        </div>
                    </div>
                `,
                'approval-flow': `
                    <div style="display: flex; align-items: center; gap: 0.5rem; padding: 1rem; background: #f9f9f9; border-radius: 6px;">
                        <div style="padding: 0.5rem; background: #28a745; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">1</div>
                        <div style="flex: 1; height: 2px; background: #ddd;"></div>
                        <div style="padding: 0.5rem; background: #ffc107; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">2</div>
                        <div style="flex: 1; height: 2px; background: #ddd;"></div>
                        <div style="padding: 0.5rem; background: #6c757d; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">3</div>
                    </div>
                `
            };
            
            return previews[type] || `
                <div style="padding: 2rem; text-align: center; color: #666; background: #f9f9f9; border-radius: 6px;">
                    🧩 ${type.replace('-', ' ').toUpperCase()} Component
                </div>
            `;
        }
        
        // Get default properties for component type
        function getDefaultProperties(type) {
            const defaults = {
                'quality-control-table': {
                    title: 'Kalite Kontrol Süreci',
                    showHeader: true,
                    allowEdit: true,
                    allowAdd: true,
                    allowDelete: true,
                    exportable: true,
                    columns: [
                        { id: 'asama', label: 'AŞAMA', width: '15%', required: true },
                        { id: 'kriter', label: 'KRİTER', width: '15%', required: true },
                        { id: 'siklik', label: 'SIKLIK', width: '10%', required: true },
                        { id: 'kabul_kriteri', label: 'KABUL KRİTERİ', width: '20%', required: true },
                        { id: 'kontrol_sorumlusu', label: 'KONTROL SORUMLUSU', width: '15%', required: true },
                        { id: 'sapma_durumu', label: 'SAPMA DURUMUNDA YAPILACAK', width: '25%', required: true }
                    ],
                    sampleData: [
                        {
                            asama: 'Hammadde Kontrolü',
                            kriter: 'Görsel Kontrol',
                            siklik: 'Her parti',
                            kabul_kriteri: 'Spesifikasyona uygun',
                            kontrol_sorumlusu: 'QC Uzmanı',
                            sapma_durumu: 'Red et ve tedarikçiye iade'
                        },
                        {
                            asama: 'Üretim Süreci',
                            kriter: 'Sıcaklık Kontrolü',
                            siklik: '2 saatte bir',
                            kabul_kriteri: '±2°C tolerans',
                            kontrol_sorumlusu: 'Üretim Operatörü',
                            sapma_durumu: 'Ekipman ayarını yap'
                        }
                    ]
                },
                'risk-matrix': {
                    title: 'Risk Assessment Matrix',
                    showLegend: true,
                    allowEdit: true,
                    size: '5x5'
                },
                'smart-form': {
                    title: 'Smart Form',
                    fields: [],
                    validation: true,
                    submitAction: 'save'
                },
                'chart': {
                    title: 'Chart',
                    type: 'bar',
                    dataSource: 'manual',
                    showLegend: true
                }
            };
            
            return defaults[type] || {};
        }
        
        // Component selection
        function selectComponent(componentId) {
            // Remove previous selection
            document.querySelectorAll('.dropped-component').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Select new component
            const component = document.getElementById(componentId);
            if (component) {
                component.classList.add('selected');
                selectedComponent = componentId;
                loadComponentProperties(componentId);
            }
        }
        
        // Load component properties
        function loadComponentProperties(componentId) {
            const component = moduleData.components.find(c => c.id === componentId);
            if (!component) return;
            
            const propertiesContent = document.getElementById('componentProperties');
            propertiesContent.innerHTML = `
                <h4 style="margin-bottom: 1rem; color: #333;">
                    ${component.icon} ${component.name} Properties
                </h4>
                
                <div class="property-group">
                    <label class="property-label">Component Title</label>
                    <input type="text" class="property-input" value="${component.properties.title || ''}" 
                           onchange="updateComponentProperty('${componentId}', 'title', this.value)">
                </div>
                
                <div class="property-group">
                    <label class="property-label">Visibility</label>
                    <select class="property-input" onchange="updateComponentProperty('${componentId}', 'visibility', this.value)">
                        <option value="visible">Visible</option>
                        <option value="hidden">Hidden</option>
                        <option value="conditional">Conditional</option>
                    </select>
                </div>
                
                <div class="property-group">
                    <label class="property-label">CSS Classes</label>
                    <input type="text" class="property-input" placeholder="custom-class another-class"
                           onchange="updateComponentProperty('${componentId}', 'cssClasses', this.value)">
                </div>
                
                ${getTypeSpecificProperties(component.type, componentId)}
                
                <hr style="margin: 1rem 0;">
                <button class="btn btn-outline btn-sm" onclick="duplicateComponent('${componentId}')">
                    📋 Duplicate
                </button>
            `;
        }
        
        // Get type-specific properties
        function getTypeSpecificProperties(type, componentId) {
            switch(type) {
                case 'quality-control-table':
                    return `
                        <div class="property-group">
                            <label class="property-label">Table Title</label>
                            <input type="text" class="property-input" value="Kalite Kontrol Süreci" 
                                   onchange="updateComponentProperty('${componentId}', 'title', this.value)">
                        </div>
                        <div class="property-group">
                            <label class="property-label">Allow Edit</label>
                            <select class="property-input" onchange="updateComponentProperty('${componentId}', 'allowEdit', this.value === 'true')">
                                <option value="true">Yes</option>
                                <option value="false">No</option>
                            </select>
                        </div>
                        <div class="property-group">
                            <label class="property-label">Allow Add New Rows</label>
                            <select class="property-input" onchange="updateComponentProperty('${componentId}', 'allowAdd', this.value === 'true')">
                                <option value="true">Yes</option>
                                <option value="false">No</option>
                            </select>
                        </div>
                        <div class="property-group">
                            <label class="property-label">Allow Delete Rows</label>
                            <select class="property-input" onchange="updateComponentProperty('${componentId}', 'allowDelete', this.value === 'true')">
                                <option value="true">Yes</option>
                                <option value="false">No</option>
                            </select>
                        </div>
                        <div class="property-group">
                            <label class="property-label">Export to Excel</label>
                            <select class="property-input" onchange="updateComponentProperty('${componentId}', 'exportable', this.value === 'true')">
                                <option value="true">Yes</option>
                                <option value="false">No</option>
                            </select>
                        </div>
                        <div class="property-group">
                            <label class="property-label">Data Source</label>
                            <select class="property-input" onchange="updateComponentProperty('${componentId}', 'dataSource', this.value)">
                                <option value="manual">Manual Entry</option>
                                <option value="database">Database Table</option>
                                <option value="api">External API</option>
                            </select>
                        </div>
                        <hr style="margin: 1rem 0;">
                        <button class="btn btn-secondary btn-sm" onclick="configureQCColumns('${componentId}')">
                            ⚙️ Configure Columns
                        </button>
                        <button class="btn btn-outline btn-sm" onclick="addSampleData('${componentId}')">
                            📝 Add Sample Data
                        </button>
                    `;
                    
                case 'risk-matrix':
                    return `
                        <div class="property-group">
                            <label class="property-label">Matrix Size</label>
                            <select class="property-input" onchange="updateComponentProperty('${componentId}', 'size', this.value)">
                                <option value="3x3">3x3</option>
                                <option value="5x5" selected>5x5</option>
                                <option value="7x7">7x7</option>
                            </select>
                        </div>
                        <div class="property-group">
                            <label class="property-label">Show Legend</label>
                            <select class="property-input" onchange="updateComponentProperty('${componentId}', 'showLegend', this.value === 'true')">
                                <option value="true">Yes</option>
                                <option value="false">No</option>
                            </select>
                        </div>
                    `;
                    
                case 'smart-form':
                    return `
                        <div class="property-group">
                            <label class="property-label">Submit Button Text</label>
                            <input type="text" class="property-input" value="Submit" 
                                   onchange="updateComponentProperty('${componentId}', 'submitText', this.value)">
                        </div>
                        <div class="property-group">
                            <label class="property-label">Enable Validation</label>
                            <select class="property-input" onchange="updateComponentProperty('${componentId}', 'validation', this.value === 'true')">
                                <option value="true">Yes</option>
                                <option value="false">No</option>
                            </select>
                        </div>
                    `;
                    
                default:
                    return `
                        <div class="property-group">
                            <label class="property-label">Custom Settings</label>
                            <textarea class="property-input" rows="3" placeholder="Enter JSON configuration..."
                                      onchange="updateComponentProperty('${componentId}', 'customSettings', this.value)"></textarea>
                        </div>
                    `;
            }
        }
        
        // Update component property
        function updateComponentProperty(componentId, property, value) {
            const component = moduleData.components.find(c => c.id === componentId);
            if (component) {
                component.properties[property] = value;
                console.log('Property updated:', componentId, property, value);
            }
        }
        
        // Component actions
        function editComponent(componentId) {
            selectComponent(componentId);
        }
        
        function deleteComponent(componentId) {
            if (confirm('Are you sure you want to delete this component?')) {
                // Remove from DOM
                const element = document.getElementById(componentId);
                if (element) {
                    element.remove();
                }
                
                // Remove from module data
                moduleData.components = moduleData.components.filter(c => c.id !== componentId);
                
                // Clear properties if this was selected
                if (selectedComponent === componentId) {
                    selectedComponent = null;
                    document.getElementById('componentProperties').innerHTML = `
                        <p style="color: #9ca3af; font-size: 0.9rem; text-align: center; margin: 2rem 0;">
                            Select a component to edit its properties
                        </p>
                    `;
                }
                
                // Show placeholder if no components left
                if (moduleData.components.length === 0) {
                    document.getElementById('dropZone').innerHTML = `
                        <div class="drop-placeholder">
                            <h3>🎨 Start Building Your Module</h3>
                            <p>Drag components from the left panel and drop them here</p>
                        </div>
                    `;
                }
            }
        }
        
        function duplicateComponent(componentId) {
            const originalComponent = moduleData.components.find(c => c.id === componentId);
            if (originalComponent) {
                const newComponentData = {
                    type: originalComponent.type,
                    name: originalComponent.name + ' (Copy)',
                    icon: originalComponent.icon
                };
                addComponentToCanvas(newComponentData);
            }
        }
        
        // Category toggle
        function toggleCategory(categoryId) {
            const content = document.getElementById(categoryId + '-components');
            const toggle = event.target.querySelector('.category-toggle');
            
            if (content.style.display === 'none') {
                content.style.display = 'block';
                toggle.textContent = '▼';
            } else {
                content.style.display = 'none';
                toggle.textContent = '▶';
            }
        }
        
        // Initialize properties
        function initializeProperties() {
            document.getElementById('moduleName').addEventListener('input', function() {
                moduleData.name = this.value;
                document.getElementById('moduleTitle').textContent = this.value || 'Untitled Module';
            });
            
            document.getElementById('moduleDescription').addEventListener('input', function() {
                moduleData.description = this.value;
            });
            
            document.getElementById('moduleCategory').addEventListener('change', function() {
                moduleData.category = this.value;
            });
            
            document.getElementById('moduleVersion').addEventListener('input', function() {
                moduleData.version = this.value;
            });
            
            document.getElementById('modulePrice').addEventListener('input', function() {
                moduleData.price = parseFloat(this.value) || 0;
            });
        }
        
        // Canvas actions
        function clearCanvas() {
            if (confirm('Are you sure you want to clear the canvas? This will remove all components.')) {
                document.getElementById('dropZone').innerHTML = `
                    <div class="drop-placeholder">
                        <h3>🎨 Start Building Your Module</h3>
                        <p>Drag components from the left panel and drop them here</p>
                    </div>
                `;
                moduleData.components = [];
                selectedComponent = null;
                componentCounter = 0;
            }
        }
        
        function undoAction() {
            // TODO: Implement undo functionality
            alert('Undo functionality will be implemented');
        }
        
        function redoAction() {
            // TODO: Implement redo functionality
            alert('Redo functionality will be implemented');
        }
        
        // Module actions
        function previewModule() {
            if (moduleData.components.length === 0) {
                alert('Please add at least one component to preview the module.');
                return;
            }
            
            // TODO: Open preview modal
            alert('Preview functionality will be implemented');
        }
        
        function saveModule() {
            if (!moduleData.name) {
                alert('Please enter a module name before saving.');
                document.getElementById('moduleName').focus();
                return;
            }
            
            if (moduleData.components.length === 0) {
                alert('Please add at least one component before saving.');
                return;
            }
            
            // Prepare module data for saving
            const saveData = {
                ...moduleData,
                id: <?php echo $editMode ? $editMode : 'null'; ?>
            };
            
            // Show loading state
            const saveBtn = document.querySelector('[onclick="saveModule()"]');
            const originalText = saveBtn.textContent;
            saveBtn.textContent = '💾 Saving...';
            saveBtn.disabled = true;
            
            // Send AJAX request
            fetch('/brcproject/platform/ajax/module-builder.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'save',
                    ...saveData
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Module saved successfully!');
                    
                    // Update edit mode if this was a new module
                    if (!saveData.id && data.module_id) {
                        const url = new URL(window.location);
                        url.searchParams.set('edit', data.module_id);
                        window.history.replaceState({}, '', url);
                    }
                } else {
                    alert('Error saving module: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Save error:', error);
                alert('Error saving module. Please try again.');
            })
            .finally(() => {
                // Restore button state
                saveBtn.textContent = originalText;
                saveBtn.disabled = false;
            });
        }
        
        function publishModule() {
            if (!moduleData.name || moduleData.components.length === 0) {
                alert('Please complete the module before publishing.');
                return;
            }
            
            if (confirm('Are you sure you want to publish this module to the marketplace?')) {
                const moduleId = <?php echo $editMode ? $editMode : 'null'; ?>;
                
                if (!moduleId) {
                    alert('Please save the module first before publishing.');
                    return;
                }
                
                // Show loading state
                const publishBtn = document.querySelector('[onclick="publishModule()"]');
                const originalText = publishBtn.textContent;
                publishBtn.textContent = '🚀 Publishing...';
                publishBtn.disabled = true;
                
                // Send AJAX request
                fetch('/brcproject/platform/ajax/module-builder.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=publish&module_id=${moduleId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Module published successfully!');
                        window.location.href = '/brcproject/admin/modules';
                    } else {
                        alert('Error publishing module: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Publish error:', error);
                    alert('Error publishing module. Please try again.');
                })
                .finally(() => {
                    // Restore button state
                    publishBtn.textContent = originalText;
                    publishBtn.disabled = false;
                });
            }
        }
        
        // Load module data (for edit mode)
        function loadModuleData(moduleId) {
            console.log('Loading module data for ID:', moduleId);
            
            fetch(`/brcproject/platform/ajax/module-builder.php?action=load&module_id=${moduleId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.module) {
                    const module = data.module;
                    
                    // Load basic module info
                    moduleData.name = module.name;
                    moduleData.description = module.description;
                    moduleData.category = module.category;
                    moduleData.version = module.version;
                    moduleData.price = module.price;
                    
                    // Update form fields
                    document.getElementById('moduleName').value = module.name;
                    document.getElementById('moduleDescription').value = module.description;
                    document.getElementById('moduleCategory').value = module.category;
                    document.getElementById('moduleVersion').value = module.version;
                    document.getElementById('modulePrice').value = module.price;
                    document.getElementById('moduleTitle').textContent = module.name;
                    
                    // Load components if they exist
                    if (module.components && module.components.length > 0) {
                        loadModuleComponents(module.components);
                    }
                    
                    console.log('Module loaded successfully:', module);
                } else {
                    console.error('Error loading module:', data.error);
                    alert('Error loading module: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Load error:', error);
                alert('Error loading module. Please try again.');
            });
        }
        
        // Load module components into canvas
        function loadModuleComponents(components) {
            // Clear existing canvas
            const dropZone = document.getElementById('dropZone');
            const placeholder = dropZone.querySelector('.drop-placeholder');
            if (placeholder) {
                placeholder.remove();
            }
            
            // Reset counters
            componentCounter = 0;
            moduleData.components = [];
            
            // Add each component
            components.forEach(component => {
                componentCounter++;
                const componentId = component.id || `component_${componentCounter}`;
                
                // Create component element
                const componentElement = createComponentElement(componentId, {
                    type: component.type,
                    name: component.name,
                    icon: component.icon
                });
                
                dropZone.appendChild(componentElement);
                
                // Add to module data
                moduleData.components.push({
                    id: componentId,
                    type: component.type,
                    name: component.name,
                    icon: component.icon,
                    properties: component.properties || getDefaultProperties(component.type)
                });
            });
            
            console.log('Components loaded:', components.length);
        }
        
        // Quality Control Table specific functions
        function configureQCColumns(componentId) {
            const component = moduleData.components.find(c => c.id === componentId);
            if (!component || component.type !== 'quality-control-table') return;
            
            const columns = component.properties.columns || [];
            
            let html = `
                <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;">
                    <div style="background: white; padding: 2rem; border-radius: 10px; max-width: 800px; width: 90%; max-height: 80vh; overflow-y: auto;">
                        <h3 style="margin-bottom: 1rem;">⚙️ Column Configuration</h3>
                        <div id="columnsConfig">
            `;
            
            columns.forEach((col, index) => {
                html += `
                    <div style="border: 1px solid #ddd; padding: 1rem; margin-bottom: 1rem; border-radius: 6px;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr 80px 80px; gap: 1rem; align-items: center;">
                            <input type="text" value="${col.label}" placeholder="Column Label" style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                            <input type="text" value="${col.id}" placeholder="Column ID" style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                            <input type="text" value="${col.width}" placeholder="Width" style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                            <button onclick="removeColumn(${index})" style="background: #dc3545; color: white; border: none; padding: 0.5rem; border-radius: 4px;">🗑️</button>
                        </div>
                    </div>
                `;
            });
            
            html += `
                        </div>
                        <div style="margin-top: 1rem; display: flex; gap: 1rem; justify-content: flex-end;">
                            <button onclick="addNewColumn()" style="background: #28a745; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 6px;">➕ Add Column</button>
                            <button onclick="saveColumnsConfig('${componentId}')" style="background: #007bff; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 6px;">💾 Save</button>
                            <button onclick="closeModal()" style="background: #6c757d; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 6px;">Cancel</button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', html);
        }
        
        function addSampleData(componentId) {
            const component = moduleData.components.find(c => c.id === componentId);
            if (!component || component.type !== 'quality-control-table') return;
            
            // Add default sample data
            const sampleRows = [
                {
                    asama: 'Hammadde Kabul',
                    kriter: 'Renk Kontrolü',
                    siklik: 'Her parti',
                    kabul_kriteri: 'Pantone standardına uygun',
                    kontrol_sorumlusu: 'QC Inspector',
                    sapma_durumu: 'Parti reddi ve tedarikçi bilgilendirme'
                },
                {
                    asama: 'Üretim Başlangıcı',
                    kriter: 'Makine Kalibrasyonu',
                    siklik: 'Her vardiya',
                    kabul_kriteri: 'Kalibrasyon sertifikası mevcut',
                    kontrol_sorumlusu: 'Makine Operatörü',
                    sapma_durumu: 'Kalibrasyon tekrarı'
                },
                {
                    asama: 'Ara Kontrol',
                    kriter: 'Ölçü Kontrolü',
                    siklik: 'Her 100 adet',
                    kabul_kriteri: '±0.5mm tolerans',
                    kontrol_sorumlusu: 'QC Teknisyeni',
                    sapma_durumu: 'Makine ayarı ve tekrar ölçüm'
                }
            ];
            
            if (!component.properties.sampleData) {
                component.properties.sampleData = [];
            }
            
            component.properties.sampleData.push(...sampleRows);
            
            alert('Sample data added to Quality Control Table!');
            console.log('Sample data added:', sampleRows);
        }
        
        function closeModal() {
            const modal = document.querySelector('[style*="position: fixed"]');
            if (modal) {
                modal.remove();
            }
        }
        
        function addNewColumn() {
            const configDiv = document.getElementById('columnsConfig');
            const newIndex = configDiv.children.length;
            
            const newColumnHtml = `
                <div style="border: 1px solid #ddd; padding: 1rem; margin-bottom: 1rem; border-radius: 6px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr 80px 80px; gap: 1rem; align-items: center;">
                        <input type="text" value="New Column" placeholder="Column Label" style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                        <input type="text" value="new_column" placeholder="Column ID" style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                        <input type="text" value="10%" placeholder="Width" style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                        <button onclick="removeColumn(${newIndex})" style="background: #dc3545; color: white; border: none; padding: 0.5rem; border-radius: 4px;">🗑️</button>
                    </div>
                </div>
            `;
            
            configDiv.insertAdjacentHTML('beforeend', newColumnHtml);
        }
        
        function removeColumn(index) {
            const configDiv = document.getElementById('columnsConfig');
            if (configDiv.children[index]) {
                configDiv.children[index].remove();
            }
        }
        
        function saveColumnsConfig(componentId) {
            const component = moduleData.components.find(c => c.id === componentId);
            if (!component) return;
            
            const configDiv = document.getElementById('columnsConfig');
            const newColumns = [];
            
            Array.from(configDiv.children).forEach(columnDiv => {
                const inputs = columnDiv.querySelectorAll('input');
                if (inputs.length >= 3) {
                    newColumns.push({
                        label: inputs[0].value,
                        id: inputs[1].value,
                        width: inputs[2].value,
                        required: true
                    });
                }
            });
            
            component.properties.columns = newColumns;
            closeModal();
            
            alert('Column configuration saved!');
            console.log('Columns updated:', newColumns);
        }
    </script>
</body>
</html>