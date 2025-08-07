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
    
    <!-- Bu kodu admin/module-builder.php dosyasının sonuna </body> tag'ından önce ekle -->

<script>
// ===== COMPLETE MODULE BUILDER FRONTEND =====

// Global variables
let currentModuleId = null;
let moduleComponents = [];
let moduleData = {};
let isDraggingComponent = false;
let componentPositionChanged = false;

// API base URL - CORRECT PATH
const API_BASE = '../platform/ajax/module-builder.php';

// Initialize when page loads
function initializeModuleEdit() {
    console.log('🚀 Module Builder initialized');
    
    const urlParams = new URLSearchParams(window.location.search);
    const moduleId = urlParams.get('edit') || urlParams.get('id') || urlParams.get('module_id');
    
    console.log('URL Parameters:', Object.fromEntries(urlParams.entries()));
    
    if (moduleId) {
        console.log('📖 Edit mode detected, Module ID:', moduleId);
        currentModuleId = parseInt(moduleId);
        loadModuleForEdit(moduleId);
    } else {
        console.log('➕ Create mode detected');
        setCreateMode();
    }

    setupFormHandler();
    setupAutoSave();

    initializeDragAndDrop();
    console.log('✅ Drag & drop from component library initialized');
}

// API call function - FIXED VERSION
async function apiCall(action, data = {}, method = 'POST') {
    const payload = { action, ...data };
    console.log('📤 API Call:', action, payload, 'Method:', method);
    
    let url = API_BASE;
    let options = {
        method: method,
        headers: { 'Content-Type': 'application/json' }
    };
    
    if (method === 'GET') {
        const params = new URLSearchParams(payload);
        url += '?' + params.toString();
    } else {
        options.body = JSON.stringify(payload);
    }
    
    try {
        const response = await fetch(url, options);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const responseText = await response.text();
        console.log('📊 Raw Response Preview:', responseText.substring(0, 200));
        
        if (!responseText.trim()) {
            throw new Error('Empty response received');
        }
        
        let result;
        try {
            result = JSON.parse(responseText);
            console.log('✅ JSON parsed successfully');
        } catch (parseError) {
            console.error('❌ JSON Parse Error:', parseError.message);
            
            // Multiple JSON objects fix
            const cleanJson = responseText.replace(/\}\{/g, '}|||{').split('|||').pop();
            try {
                result = JSON.parse(cleanJson);
                console.log('✅ Cleaned JSON parsed successfully');
            } catch (cleanError) {
                throw new Error(`JSON parsing failed: ${responseText.substring(0, 500)}`);
            }
        }
        
        console.log('📥 Final API Response:', action, result);
        return result;
        
    } catch (fetchError) {
        console.error('💥 Fetch Error:', fetchError);
        throw fetchError;
    }
}

// Load module for edit
async function loadModuleForEdit(moduleId) {
    console.log('📡 Loading module for edit:', moduleId);
    
    try {
        showLoadingState();
        
        const response = await apiCall('get_module_details', { module_id: moduleId }, 'GET');
        console.log('📥 Module data received:', response);
        
        if (response.success && response.module) {
            moduleData = response.module;
            moduleComponents = response.components || [];
            
            console.log('🔍 Module loaded:', {
                id: moduleData.id,
                name: moduleData.name,
                components_count: moduleComponents.length
            });
            
            // Form field'larını doldur
            populateFormFields(response.module);
            
            // Canvas oluştur ve components'ları render et
            createCanvas();
            
            if (moduleComponents.length > 0) {
                console.log('🎨 Rendering components...');
                renderComponentsOnCanvas(moduleComponents);
            } else {
                console.log('⚠️ No components found for this module');
                showEmptyCanvas();
            }
            
            setEditMode(response.module);
            console.log('✅ Module loaded successfully');
        } else {
            console.error('❌ Failed to load module:', response.error);
            showError('Module yüklenemedi: ' + (response.error || 'Unknown error'));
        }
        
    } catch (error) {
        console.error('💥 Exception loading module:', error);
        showError('Module yükleme hatası: ' + error.message);
    } finally {
        hideLoadingState();
    }
}

// Create canvas if it doesn't exist
function createCanvas() {
    const dropZone = document.getElementById('dropZone');
    
    if (dropZone) {
        console.log('✅ Using existing drop-zone as canvas');
        return dropZone;
    } else {
        console.warn('⚠️ Drop zone not found, creating new canvas');
        
        const canvas = document.createElement('div');
        canvas.id = 'module-canvas';
        canvas.style.position = 'relative';
        canvas.style.minHeight = '600px';
        canvas.style.border = '2px dashed #007bff';
        canvas.style.borderRadius = '8px';
        canvas.style.padding = '20px';
        canvas.style.backgroundColor = '#f8f9fa';
        
        const container = document.querySelector('.canvas-viewport') || document.body;
        container.appendChild(canvas);
        
        return canvas;
    }
}

// Show empty canvas
function showEmptyCanvas() {
    const dropZone = document.getElementById('dropZone');
    if (dropZone) {
        dropZone.innerHTML = `
            <div class="drop-placeholder">
                <h3>📦 No Components Found</h3>
                <p>This module doesn't have any components yet.</p>
                <button onclick="createTestComponents()" style="
                    padding: 0.75rem 1.5rem;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    border: none;
                    border-radius: 6px;
                    font-weight: 500;
                    cursor: pointer;
                    margin-top: 1rem;
                ">🧪 Add Test Components</button>
            </div>
        `;
    }
}

function createTestComponents() {
    console.log('🧪 Creating test components...');
    
    // Create fake components for testing
    const testComponents = [
        {
            id: 'test-1',
            component_name: 'Quality Control Form',
            component_type: 'form',
            component_config: '{"fields":[{"name":"process_step","type":"text","label":"Süreç Adımı"}]}',
            position_x: 50,
            position_y: 50,
            width: 280,
            height: 180
        },
        {
            id: 'test-2',
            component_name: 'Risk Assessment Matrix',
            component_type: 'risk-matrix',
            component_config: '{"matrix_size":"5x5","categories":["Likelihood","Impact"]}',
            position_x: 360,
            position_y: 80,
            width: 300,
            height: 200
        },
        {
            id: 'test-3',
            component_name: 'Approval Workflow',
            component_type: 'approval-flow',
            component_config: '{"steps":["Review","Approve","Publish"]}',
            position_x: 50,
            position_y: 280,
            width: 250,
            height: 160
        }
    ];
    
    moduleComponents = testComponents;
    renderComponentsOnCanvas(testComponents);
    
    // Update module title
    document.getElementById('moduleTitle').textContent = 'Test Module (with drag & drop)';
    
    showSuccess('Test components created! Try dragging them around.');
}

// Render components on canvas
function renderComponentsOnCanvas(components) {
    console.log('🎨 Rendering components on canvas:', components.length, 'components');
    
    const dropZone = document.getElementById('dropZone');
    if (!dropZone) {
        console.error('❌ Drop zone not found!');
        return;
    }
    
    // Clear drop zone
    dropZone.innerHTML = '';
    dropZone.className = 'drop-zone'; // Reset classes
    
    if (components.length === 0) {
        showEmptyCanvas();
        return;
    }
    
    // Set drop zone as relative for absolute positioning
    dropZone.style.position = 'relative';
    dropZone.style.minHeight = '500px';
    
    // Render each component
    components.forEach((component, index) => {
        console.log(`🧩 Rendering component ${index + 1}:`, component.component_name);
        const componentElement = createComponentElement(component, index);
        dropZone.appendChild(componentElement);
    });
    
    console.log(`✅ ${components.length} components rendered in drop zone`);
    
    // Show info in canvas title
    const moduleTitle = document.getElementById('moduleTitle');
    if (moduleTitle) {
        moduleTitle.textContent = moduleData.name ? 
            `${moduleData.name} (${components.length} components)` : 
            `Module with ${components.length} components`;
    }
}

// Create component element
function createComponentElement(component, index) {
    const element = document.createElement('div');
    element.className = 'dropped-component';
    element.setAttribute('data-component-id', component.id);
    element.setAttribute('data-index', index);
    
    const x = component.position_x || (50 + (index % 3) * 220);
    const y = component.position_y || (50 + Math.floor(index / 3) * 200);
    const width = component.width || 250;
    const height = component.height || 160;
    
    // Use absolute positioning within drop zone
    element.style.position = 'absolute';
    element.style.left = x + 'px';    
    element.style.top = y + 'px';
    element.style.width = width + 'px';
    element.style.height = height + 'px';
    element.style.cursor = 'move';
    element.style.userSelect = 'none';
    
    // Get component icon and color based on type
    const { icon, color } = getComponentStyle(component.component_type);
    
    element.innerHTML = `
        <div class="component-controls">
            <button class="control-btn control-edit" onclick="editComponent(${index})" title="Edit Component">
                ✏️
            </button>
            <button class="control-btn control-delete" onclick="deleteComponent(${index})" title="Delete Component">
                🗑️
            </button>
        </div>
        
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div style="
                width: 40px; 
                height: 40px; 
                background: ${color}; 
                border-radius: 8px; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                font-size: 1.2rem;
            ">
                ${icon}
            </div>
            <div>
                <h4 style="margin: 0; font-size: 1rem; color: #333;">${component.component_name}</h4>
                <p style="margin: 0; font-size: 0.8rem; color: #666; text-transform: capitalize;">
                    ${component.component_type.replace('-', ' ')}
                </p>
            </div>
        </div>
        
        <div style="font-size: 0.85rem; color: #555; line-height: 1.4;">
            ${getComponentPreview(component)}
        </div>
        
        <div style="
            position: absolute; 
            bottom: 8px; 
            right: 8px; 
            font-size: 0.7rem; 
            color: #999;
            background: rgba(255,255,255,0.8);
            padding: 2px 6px;
            border-radius: 3px;
        ">
            ID: ${component.id}
        </div>
    `;
    
    // Add drag functionality
    makeDraggable(element);
    
    console.log('✅ Component created with drag functionality:', component.component_name);
    
    return element;
}

function getComponentStyle(type) {
    const styles = {
        'form': { icon: '📝', color: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' },
        'smart-form': { icon: '📝', color: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' },
        'risk-matrix': { icon: '🛡️', color: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)' },
        'quality-control-table': { icon: '🏭', color: 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)' },
        'chart': { icon: '📊', color: 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)' },
        'kpi-card': { icon: '📈', color: 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)' },
        'approval-flow': { icon: '✅', color: 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)' },
        'dashboard': { icon: '📊', color: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' },
        'notification': { icon: '🔔', color: 'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)' },
        'file-upload': { icon: '📁', color: 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)' },
        'default': { icon: '🔧', color: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' }
    };
    
    return styles[type] || styles['default'];
}

// Component preview content
function getComponentPreview(component) {
    let config = {};
    try {
        config = component.component_config ? JSON.parse(component.component_config) : {};
    } catch (e) {
        console.warn('Invalid component config JSON');
    }
    
    switch (component.component_type) {
        case 'form':
        case 'smart-form':
            const fieldCount = config.fields ? config.fields.length : 0;
            return `📝 Interactive form with ${fieldCount} fields<br><small>Supports validation, conditional logic</small>`;
            
        case 'risk-matrix':
            const matrixSize = config.matrix_size || '5x5';
            return `🛡️ Risk assessment matrix (${matrixSize})<br><small>Visual risk evaluation tool</small>`;
            
        case 'quality-control-table':
            return `🏭 Quality control process table<br><small>Track and manage quality metrics</small>`;
            
        case 'chart':
            return `📊 Data visualization charts<br><small>Line, bar, pie charts with real-time data</small>`;
            
        case 'kpi-card':
            return `📈 Key Performance Indicators<br><small>Display critical business metrics</small>`;
            
        case 'approval-flow':
            const stepCount = config.steps ? config.steps.length : 3;
            return `✅ Multi-step approval workflow<br><small>${stepCount} approval stages</small>`;
            
        case 'dashboard':
            const widgetCount = config.widgets ? config.widgets.length : 0;
            return `📊 Dashboard with ${widgetCount} widgets<br><small>Customizable data dashboard</small>`;
            
        case 'notification':
            return `🔔 Smart notifications<br><small>Email, SMS, in-app notifications</small>`;
            
        case 'file-upload':
            return `📁 File upload component<br><small>Drag & drop file uploads with validation</small>`;
            
        default:
            return `🔧 ${component.component_type.replace('-', ' ')}<br><small>Custom component</small>`;
    }
}


// Drag functionality
function makeDraggable(element) {
    let isDragging = false;
    let startX, startY, startLeft, startTop;
    
    console.log('🔧 Making element draggable:', element.getAttribute('data-component-id'));
    
    element.addEventListener('mousedown', function(e) {
        // Don't drag if clicking on control buttons
        if (e.target.closest('.control-btn')) {
            console.log('🚫 Control button clicked, not dragging');
            return;
        }
        
        console.log('🖱️ MOUSEDOWN detected - starting drag');
        
        isDragging = true;
        startX = e.clientX;
        startY = e.clientY;
        startLeft = parseInt(element.style.left) || 0;
        startTop = parseInt(element.style.top) || 0;
        
        // Visual feedback
        element.style.cursor = 'grabbing';
        element.style.opacity = '0.8';
        element.style.zIndex = '1000';
        element.style.transform = 'scale(1.02) rotate(1deg)';
        element.classList.add('selected');
        
        // Prevent text selection
        document.body.style.userSelect = 'none';
        
        console.log('🎯 Drag started at:', startX, startY, 'Element pos:', startLeft, startTop);
        
        function onMouseMove(e) {
            if (!isDragging) return;
            
            const deltaX = e.clientX - startX;
            const deltaY = e.clientY - startY;
            
            const newLeft = startLeft + deltaX;
            const newTop = startTop + deltaY;
            
            // Constrain to drop zone bounds
            const dropZone = document.getElementById('dropZone');
            const dropRect = dropZone.getBoundingClientRect();
            const elemRect = element.getBoundingClientRect();
            
            const maxX = dropZone.offsetWidth - element.offsetWidth;
            const maxY = dropZone.offsetHeight - element.offsetHeight;
            
            const constrainedX = Math.max(10, Math.min(maxX - 10, newLeft));
            const constrainedY = Math.max(10, Math.min(maxY - 10, newTop));
            
            element.style.left = constrainedX + 'px';
            element.style.top = constrainedY + 'px';
            
            console.log('📍 Dragging to:', constrainedX, constrainedY);
        }
        
        function onMouseUp() {
            console.log('🖱️ MOUSEUP - drag ended');
            
            if (!isDragging) return;
            isDragging = false;
            
            // Reset visual feedback
            element.style.cursor = 'move';
            element.style.opacity = '';
            element.style.zIndex = '';
            element.style.transform = '';
            element.classList.remove('selected');
            document.body.style.userSelect = '';
            
            // Remove listeners
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
            
            // Save position
            saveComponentPosition(element);
            
            console.log('✅ Drag completed and position saved');
        }
        
        // Add global listeners
        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
        
        e.preventDefault();
        e.stopPropagation();
    });
    
    console.log('✅ Drag functionality attached to element');
}

// Save component position
function saveComponentPosition(element) {
    const componentId = parseInt(element.getAttribute('data-component-id'));
    const index = parseInt(element.getAttribute('data-index'));
    const newX = parseInt(element.style.left);
    const newY = parseInt(element.style.top);
    
    console.log(`💾 Position updated for component ${componentId}: (${newX}, ${newY})`);
    
    if (moduleComponents[index] && moduleComponents[index].id == componentId) {
        moduleComponents[index].position_x = newX;
        moduleComponents[index].position_y = newY;
        componentPositionChanged = true;
    }
}

// Show components info
function showComponentsInfo(count) {
    // Update canvas toolbar info
    const canvasTitle = document.querySelector('.canvas-title');
    if (canvasTitle) {
        const moduleTitle = document.getElementById('moduleTitle');
        if (moduleTitle) {
            moduleTitle.style.color = '#28a745';
            moduleTitle.textContent = `${moduleData.name || 'Active Module'} (${count} components loaded)`;
        }
    }
    
    // Show success notification
    showSuccess(`${count} components loaded successfully - Drag & Drop enabled!`);
}

// Form field population
function populateFormFields(module) {
    console.log('📝 Populating form fields with module:', module);
    
    try {
        const moduleName = module.name || module.module_name || module.module_code || 'Untitled Module';
        console.log('🏷️ Module name to set:', moduleName);
        
        // Module name - mevcut field'ları kullan
        const nameField = document.getElementById('moduleName');
        if (nameField) {
            nameField.value = moduleName;
            console.log('✅ Module name set:', moduleName);
        }
        
        // Description
        const descField = document.getElementById('moduleDescription');
        if (descField) {
            descField.value = module.description || '';
            console.log('✅ Description set');
        }
        
        // Category
        const catField = document.getElementById('moduleCategory');
        if (catField) {
            catField.value = module.category || '';
            console.log('✅ Category set:', module.category);
        }
        
        // Version
        const versionField = document.getElementById('moduleVersion');
        if (versionField) {
            versionField.value = module.version || '1.0.0';
        }
        
        // Price
        const priceField = document.getElementById('modulePrice');
        if (priceField) {
            priceField.value = module.price || '0';
        }
        
        // Update page title
        const moduleTitle = document.getElementById('moduleTitle');
        if (moduleTitle) {
            moduleTitle.textContent = moduleName;
        }
        
        console.log('✅ Form fields populated successfully');
        
    } catch (error) {
        console.error('💥 Error populating form fields:', error);
    }
}


// Helper functions
function setupFormHandler() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (currentModuleId) {
                updateModule();
            } else {
                createModule();
            }
        });
    }
}

function setupAutoSave() {
    setInterval(() => {
        if (componentPositionChanged && currentModuleId) {
            console.log('💾 Auto-saving positions...');
            componentPositionChanged = false;
        }
    }, 30000);
}

function setEditMode(module) {
    updatePageTitle(`Edit: ${module.name || 'Module'}`);
    
    const saveBtn = document.querySelector('#save-btn, button[type="submit"]');
    if (saveBtn) {
        saveBtn.textContent = 'Update Module';
        saveBtn.classList.add('btn-success');
    }
}

function setCreateMode() {
    updatePageTitle('Create New Module');
}

function updatePageTitle(title) {
    document.title = title;
    const header = document.querySelector('h1, .page-title');
    if (header) header.textContent = title;
}

// Update module
async function updateModule() {
    console.log('💾 Updating module...');
    
    try {
        showLoadingState();
        const formData = collectFormData();
        
        if (!formData.name) {
            showError('Module name is required');
            return;
        }
        
        const response = await apiCall('update_module', {
            module_id: currentModuleId,
            ...formData
        });
        
        if (response.success) {
            showSuccess('Module updated successfully!');
            moduleData = { ...moduleData, ...formData };
        } else {
            showError('Update failed: ' + response.error);
        }
        
    } catch (error) {
        console.error('💥 Update exception:', error);
        showError('Update error: ' + error.message);
    } finally {
        hideLoadingState();
    }
}

function collectFormData() {
    const data = {};
    
    const nameField = document.getElementById('moduleName');
    if (nameField && nameField.value.trim()) {
        data.name = nameField.value.trim();
        console.log('✅ Name:', data.name);
    }
    
    const descField = document.getElementById('moduleDescription');
    if (descField && descField.value.trim()) {
        data.description = descField.value.trim();
        console.log('✅ Description:', data.description);
    }
    
    const catField = document.getElementById('moduleCategory');
    if (catField && catField.value) {
        data.category = catField.value;
        console.log('✅ Category:', data.category);
    }
    
    // Version
    const versionField = document.getElementById('moduleVersion');
    if (versionField && versionField.value) {
        data.version = versionField.value;
        console.log('✅ Version:', data.version);
    }
    
    // Price
    const priceField = document.getElementById('modulePrice');
    if (priceField && priceField.value) {
        data.price = parseFloat(priceField.value) || 0;
        console.log('✅ Price:', data.price);
    }
    
    console.log('📋 Collected form data:', data);
    return data;
}
// Component actions
async function editComponent(index) {
    const component = moduleComponents[index];
    if (!component) return;
    
    const newName = prompt('Enter component name:', component.component_name);
    if (newName && newName !== component.component_name) {
        component.component_name = newName;
        renderComponentsOnCanvas(moduleComponents);
        showSuccess('Component name updated!');
    }
}

async function deleteComponent(index) {
    if (!confirm('Delete this component?')) return;
    
    moduleComponents.splice(index, 1);
    renderComponentsOnCanvas(moduleComponents);
    showSuccess('Component deleted!');
}

// UI helpers
function showLoadingState() {
    let loading = document.getElementById('loading');
    if (!loading) {
        loading = document.createElement('div');
        loading.id = 'loading';
        loading.style.position = 'fixed';
        loading.style.top = '50%';
        loading.style.left = '50%';
        loading.style.transform = 'translate(-50%, -50%)';
        loading.style.background = 'rgba(0,0,0,0.8)';
        loading.style.color = 'white';
        loading.style.padding = '20px';
        loading.style.borderRadius = '5px';
        loading.style.zIndex = '9999';
        loading.innerHTML = '🔄 Loading...';
        document.body.appendChild(loading);
    }
    loading.style.display = 'block';
}

function hideLoadingState() {
    const loading = document.getElementById('loading');
    if (loading) loading.style.display = 'none';
}

function showSuccess(message) {
    showNotification(message, 'success');
}

function showError(message) {
    showNotification(message, 'error');
}

function showNotification(message, type) {
    const existing = document.querySelectorAll('.notification');
    existing.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.padding = '15px 20px';
    notification.style.borderRadius = '5px';
    notification.style.zIndex = '9999';
    notification.style.color = 'white';
    notification.style.backgroundColor = type === 'success' ? '#28a745' : '#dc3545';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 5000);
}

// ===== COMPONENT LIBRARY DRAG & DROP =====

// Sayfa yüklendiğinde drag & drop'u başlat
function initializeDragAndDrop() {
    console.log('🎯 Initializing drag & drop from component library...');
    
    // Sol menüdeki tüm component item'lara drag başlat
    const componentItems = document.querySelectorAll('.component-item[draggable="true"]');
    console.log(`Found ${componentItems.length} draggable components in library`);
    
    componentItems.forEach(item => {
        item.addEventListener('dragstart', handleDragStart);
        item.addEventListener('dragend', handleDragEnd);
    });
    
    // Canvas (drop-zone) events
    const dropZone = document.getElementById('dropZone');
    if (dropZone) {
        dropZone.addEventListener('dragover', handleDragOver);
        dropZone.addEventListener('dragenter', handleDragEnter);
        dropZone.addEventListener('dragleave', handleDragLeave);
        dropZone.addEventListener('drop', handleDrop);
        
        console.log('✅ Drop zone events attached');
    } else {
        console.error('❌ Drop zone not found!');
    }
}

// Sürükleme başladığında
let draggedComponentType = null;

function handleDragStart(e) {
    draggedComponentType = e.target.dataset.type;
    console.log('🎯 Drag started for component type:', draggedComponentType);
    
    // Visual feedback
    e.target.style.opacity = '0.5';
    e.target.style.transform = 'rotate(5deg)';
    
    // Drag data
    e.dataTransfer.effectAllowed = 'copy';
    e.dataTransfer.setData('text/plain', draggedComponentType);
}

function handleDragEnd(e) {
    console.log('🎯 Drag ended');
    
    // Reset visual feedback
    e.target.style.opacity = '1';
    e.target.style.transform = 'none';
}

// Canvas üzerinde sürüklenirken
function handleDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'copy';
}

function handleDragEnter(e) {
    e.preventDefault();
    console.log('🎯 Drag entered drop zone');
    
    // Drop zone visual feedback
    const dropZone = document.getElementById('dropZone');
    dropZone.classList.add('drag-over');
}

function handleDragLeave(e) {
    // Only remove if really leaving (not moving to child)
    if (!e.currentTarget.contains(e.relatedTarget)) {
        console.log('🎯 Drag left drop zone');
        const dropZone = document.getElementById('dropZone');
        dropZone.classList.remove('drag-over');
    }
}

// Canvas'a bırakıldığında - EN ÖNEMLİ KISIM!
function handleDrop(e) {
    e.preventDefault();
    
    const componentType = e.dataTransfer.getData('text/plain') || draggedComponentType;
    console.log('🎯 Component dropped! Type:', componentType);
    
    // Drop zone visual feedback'i temizle
    const dropZone = document.getElementById('dropZone');
    dropZone.classList.remove('drag-over');
    
    if (!componentType) {
        console.error('❌ No component type found');
        return;
    }
    
    // Mouse pozisyonunu al (drop zone içindeki relative pozisyon)
    const dropRect = dropZone.getBoundingClientRect();
    const x = e.clientX - dropRect.left - 125; // component width/2
    const y = e.clientY - dropRect.top - 75;   // component height/2
    
    console.log('📍 Drop position:', x, y);
    
    // Yeni component oluştur
    createNewComponent(componentType, x, y);
}

// Yeni component oluştur
function createNewComponent(type, x, y) {
    console.log('➕ Creating new component:', type, 'at position:', x, y);
    
    // Component bilgilerini al
    const componentInfo = getComponentInfo(type);
    
    // Yeni component objesi oluştur
    const newComponent = {
        id: 'new-' + Date.now(), // Geçici ID
        component_name: componentInfo.name,
        component_type: type,
        component_config: JSON.stringify(componentInfo.config),
        position_x: Math.max(10, x),
        position_y: Math.max(10, y),
        width: componentInfo.width || 250,
        height: componentInfo.height || 160
    };
    
    // Global array'e ekle
    moduleComponents.push(newComponent);
    
    // Canvas'ı yeniden render et
    renderComponentsOnCanvas(moduleComponents);
    
    // Success message
    showSuccess(`${componentInfo.name} added to canvas!`);
    
    console.log('✅ Component created successfully');
}

// Component bilgilerini al
function getComponentInfo(type) {
    const componentTypes = {
        'quality-control-table': {
            name: 'Quality Control Table',
            config: { 
                headers: ['Process Step', 'Control Point', 'Criteria', 'Responsible'],
                rows: []
            },
            width: 350,
            height: 200
        },
        'risk-matrix': {
            name: 'Risk Assessment Matrix',
            config: { 
                size: '5x5',
                axes: ['Likelihood', 'Impact'],
                risks: []
            },
            width: 300,
            height: 300
        },
        'smart-form': {
            name: 'Smart Form',
            config: {
                fields: [
                    { name: 'title', type: 'text', label: 'Title', required: true },
                    { name: 'description', type: 'textarea', label: 'Description' }
                ]
            },
            width: 280,
            height: 180
        },
        'chart': {
            name: 'Data Chart',
            config: {
                type: 'line',
                data: { labels: [], datasets: [] }
            },
            width: 300,
            height: 200
        },
        'kpi-card': {
            name: 'KPI Card',
            config: {
                title: 'Key Metric',
                value: 0,
                target: 100,
                unit: '%'
            },
            width: 200,
            height: 120
        },
        'approval-flow': {
            name: 'Approval Workflow',
            config: {
                steps: [
                    { name: 'Review', assignee: 'reviewer' },
                    { name: 'Approve', assignee: 'manager' },
                    { name: 'Publish', assignee: 'admin' }
                ]
            },
            width: 280,
            height: 180
        },
        'file-upload': {
            name: 'File Upload',
            config: {
                allowedTypes: ['.pdf', '.doc', '.xlsx'],
                maxSize: '10MB',
                multiple: true
            },
            width: 250,
            height: 150
        },
        'notification': {
            name: 'Notification System',
            config: {
                types: ['email', 'sms', 'in-app'],
                triggers: []
            },
            width: 250,
            height: 140
        },
        'dashboard-grid': {
            name: 'Dashboard Grid',
            config: {
                columns: 2,
                widgets: []
            },
            width: 400,
            height: 250
        }
    };
    
    return componentTypes[type] || {
        name: type.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase()),
        config: {},
        width: 250,
        height: 160
    };
}

// ===== HEADER BUTTON FONKSİYONLARI =====

// SAVE MODULE - Ana kaydetme fonksiyonu
async function saveModule() {
    console.log('💾 Modül kaydediliyor...');
    
    try {
        // Form verilerini topla
        const formData = collectFormData();
        
        if (!formData.name) {
            alert('Modül adı gerekli!');
            return;
        }
        
        console.log('📝 Form data:', formData);
        
        let response;
        if (currentModuleId) {
            // Mevcut modülü güncelle
            console.log('🔄 Updating existing module:', currentModuleId);
            response = await apiCall('update_module', {
                module_id: currentModuleId,
                ...formData
            });
        } else {
            // Yeni modül oluştur
            console.log('➕ Creating new module');
            response = await apiCall('create_module', formData);
        }
        
        if (response.success) {
            console.log('✅ Module saved successfully');
            
            if (response.module_id && !currentModuleId) {
                currentModuleId = response.module_id;
                // URL'i edit mode'a güncelle
                const newUrl = window.location.pathname + '?edit=' + currentModuleId;
                window.history.pushState({}, '', newUrl);
                console.log('🔗 URL updated to edit mode');
            }
            
            // Module title'ı güncelle
            if (response.module_name || formData.name) {
                const moduleTitle = document.getElementById('moduleTitle');
                if (moduleTitle) {
                    moduleTitle.textContent = response.module_name || formData.name;
                }
            }
            
            // ŞİMDİ COMPONENT'LERI KAYDET
            console.log('💾 Now saving components...');
            const savedComponentCount = await saveModuleComponents();
            
            // Success mesajı
            if (savedComponentCount > 0) {
                alert(`✅ Modül ve ${savedComponentCount} component başarıyla kaydedildi!`);
            } else {
                alert('✅ Modül kaydedildi (component yok)!');
            }
            
        } else {
            console.error('❌ Save failed:', response.error);
            alert('❌ Kaydetme başarısız: ' + (response.error || 'Bilinmeyen hata'));
        }
        
    } catch (error) {
        console.error('💥 Save exception:', error);
        alert('💥 Kaydetme hatası: ' + error.message);
    }
}

async function saveModuleComponents() {
    if (!currentModuleId) {
        console.log('⚠️ No module ID, skipping component save');
        return;
    }
    
    if (moduleComponents.length === 0) {
        console.log('⚠️ No components to save');
        return;
    }
    
    console.log('💾 Saving components...', moduleComponents.length, 'components');
    
    try {
        // Önce mevcut component'leri sil (basit yöntem)
        const deleteResponse = await apiCall('delete_all_components', {
            module_id: currentModuleId
        });
        
        console.log('🗑️ Delete response:', deleteResponse);
        
        // Sonra yeni component'leri ekle
        let savedCount = 0;
        for (const component of moduleComponents) {
            console.log('💾 Saving component:', component.component_name);
            
            const saveResponse = await apiCall('add_component', {
                module_id: currentModuleId,
                component_name: component.component_name,
                component_type: component.component_type,
                component_config: component.component_config,
                position_x: component.position_x || 0,
                position_y: component.position_y || 0,
                width: component.width || 250,
                height: component.height || 160
            });
            
            if (saveResponse.success) {
                // Geçici ID'yi gerçek ID ile değiştir
                if (component.id.toString().startsWith('new-')) {
                    component.id = saveResponse.component_id;
                }
                savedCount++;
                console.log('✅ Component saved:', component.component_name);
            } else {
                console.error('❌ Failed to save component:', component.component_name, saveResponse.error);
            }
        }
        
        console.log(`✅ ${savedCount}/${moduleComponents.length} components saved`);
        return savedCount;
        
    } catch (error) {
        console.error('💥 Component save error:', error);
        return 0;
    }
}
// PREVIEW MODULE
function previewModule() {
    console.log('👁️ Preview module...');
    
    if (moduleComponents.length === 0) {
        alert('Önizleme için en az bir component ekleyin.');
        return;
    }
    
    // Basit preview - yeni pencerede göster
    const previewWindow = window.open('', '_blank', 'width=800,height=600');
    previewWindow.document.write(`
        <html>
        <head>
            <title>Module Preview: ${moduleData.name || 'Untitled'}</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
                .preview-header { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
                .component-preview { background: white; margin: 10px 0; padding: 15px; border-radius: 8px; border-left: 4px solid #007bff; }
            </style>
        </head>
        <body>
            <div class="preview-header">
                <h1>📱 ${moduleData.name || 'Module Preview'}</h1>
                <p>${moduleData.description || 'No description'}</p>
                <small>Components: ${moduleComponents.length}</small>
            </div>
            ${moduleComponents.map(comp => `
                <div class="component-preview">
                    <h3>${comp.component_name}</h3>
                    <p><strong>Type:</strong> ${comp.component_type}</p>
                    <p><strong>Position:</strong> ${comp.position_x}, ${comp.position_y}</p>
                </div>
            `).join('')}
        </body>
        </html>
    `);
}

// PUBLISH MODULE
async function publishModule() {
    console.log('🚀 Publishing module...');
    
    if (!currentModuleId) {
        alert('Önce modülü kaydedin.');
        return;
    }
    
    if (moduleComponents.length === 0) {
        alert('Yayınlamak için en az bir component ekleyin.');
        return;
    }
    
    if (confirm('Modülü yayınlamak istediğinizden emin misiniz?')) {
        alert('🚀 Yayınlama özelliği yakında eklenecek!');
    }
}

// CLEAR CANVAS
function clearCanvas() {
    console.log('🗑️ Clearing canvas...');
    
    if (moduleComponents.length === 0) {
        alert('Canvas zaten boş.');
        return;
    }
    
    if (confirm('Tüm componentleri silmek istediğinizden emin misiniz?')) {
        moduleComponents = [];
        renderComponentsOnCanvas([]);
        alert('🗑️ Canvas temizlendi.');
    }
}

// UNDO ACTION
let actionHistory = [];
let historyIndex = -1;

function undoAction() {
    console.log('↶ Undo action...');
    alert('↶ Geri alma özelliği yakında eklenecek!');
}

// REDO ACTION
function redoAction() {
    console.log('↷ Redo action...');
    alert('↷ İleri alma özelliği yakında eklenecek!');
}

// TOGGLE CATEGORY - Component library için
function toggleCategory(categoryId) {
    const content = document.getElementById(categoryId + '-components');
    const toggleIcon = document.querySelector(`[onclick="toggleCategory('${categoryId}')"] .category-toggle`);
    
    if (content && toggleIcon) {
        if (content.style.display === 'none') {
            content.style.display = 'block';
            toggleIcon.textContent = '▼';
        } else {
            content.style.display = 'none';
            toggleIcon.textContent = '▶';
        }
    }
}

console.log('✅ Header button functions loaded');

// Initialize when DOM loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM loaded, initializing module builder...');
    setTimeout(() => {
        initializeModuleEdit();
    }, 100);
});

// Debug helpers
window.debugModuleBuilder = {
    moduleData: () => console.log('Module:', moduleData),
    components: () => console.log('Components:', moduleComponents),
    reload: () => currentModuleId && loadModuleForEdit(currentModuleId),
    createCanvas: () => createCanvas()
};

console.log('🔧 Complete Frontend Module Builder JavaScript loaded!');
</script>
</body>
</html>