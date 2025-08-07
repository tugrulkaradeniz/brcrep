// ===== MODULE BUILDER DEBUG HELPER =====
// Console'a bu kodu yapıştırarak debug yapabilirsiniz

console.log('🔧 Module Builder Debug Helper loaded');

// Debug API call
async function debugApiCall() {
    console.log('🧪 Testing API connection...');
    
    try {
        // Test connection
        const response = await fetch('ajax/module-builder.php?action=test_connection', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        console.log('📊 Response status:', response.status);
        console.log('📊 Response headers:', Object.fromEntries(response.headers.entries()));
        
        const text = await response.text();
        console.log('📊 Raw response text:', text);
        
        try {
            const json = JSON.parse(text);
            console.log('✅ Parsed JSON:', json);
        } catch (e) {
            console.error('❌ JSON Parse Error:', e);
            console.log('📄 Response text preview:', text.substring(0, 500));
        }
        
    } catch (error) {
        console.error('❌ Fetch error:', error);
    }
}

// URL parametrelerini kontrol et
function debugUrlParams() {
    const urlParams = new URLSearchParams(window.location.search);
    console.log('🔗 URL Parameters:', Object.fromEntries(urlParams.entries()));
    console.log('🔗 Edit param:', urlParams.get('edit'));
    console.log('🔗 ID param:', urlParams.get('id'));
    console.log('🔗 Module ID param:', urlParams.get('module_id'));
}

// Module load'u test et
async function debugModuleLoad(moduleId) {
    console.log('🧪 Testing module load for ID:', moduleId);
    
    try {
        const url = `ajax/module-builder.php?action=get_module_details&module_id=${moduleId}`;
        console.log('📤 Request URL:', url);
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        console.log('📊 Response status:', response.status);
        console.log('📊 Response ok:', response.ok);
        
        const text = await response.text();
        console.log('📊 Raw response:', text);
        
        if (text) {
            try {
                const json = JSON.parse(text);
                console.log('✅ Module data:', json);
                return json;
            } catch (e) {
                console.error('❌ JSON Parse Error:', e);
                return { error: 'Invalid JSON', raw: text };
            }
        }
        
    } catch (error) {
        console.error('❌ Module load error:', error);
        return { error: error.message };
    }
}

// Global değişkenleri kontrol et
function debugGlobals() {
    console.log('🌐 Global Variables:');
    console.log('- currentModuleId:', window.currentModuleId);
    console.log('- moduleComponents:', window.moduleComponents);
    console.log('- moduleData:', window.moduleData);
    console.log('- API_BASE:', window.API_BASE);
}

// Form field'larını kontrol et
function debugFormFields() {
    console.log('📝 Form Fields:');
    
    const selectors = [
        '#module_name', '#moduleName', 'input[name="module_name"]', 'input[name="name"]',
        '#description', 'textarea[name="description"]',
        '#category', 'select[name="category"]'
    ];
    
    selectors.forEach(selector => {
        const element = document.querySelector(selector);
        console.log(`- ${selector}:`, element ? element.value : 'NOT FOUND');
    });
}

// Canvas'ı kontrol et
function debugCanvas() {
    console.log('🎨 Canvas Debug:');
    
    const canvasSelectors = [
        '#module-canvas', '#designer-canvas', '.designer-canvas', '.canvas'
    ];
    
    canvasSelectors.forEach(selector => {
        const element = document.querySelector(selector);
        console.log(`- ${selector}:`, element ? 'FOUND' : 'NOT FOUND');
        if (element) {
            console.log(`  - Children count: ${element.children.length}`);
            console.log(`  - Inner HTML length: ${element.innerHTML.length}`);
        }
    });
}

// Hata simulation
function simulateError() {
    console.log('🧪 Simulating save error...');
    const badData = { action: 'invalid_action', data: 'test' };
    
    fetch('ajax/module-builder.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(badData)
    })
    .then(response => response.text())
    .then(text => {
        console.log('📊 Error response:', text);
        try {
            const json = JSON.parse(text);
            console.log('✅ Error JSON:', json);
        } catch (e) {
            console.error('❌ Error parsing error response:', e);
        }
    });
}

// Tüm debug'ları çalıştır
async function runAllDebug() {
    console.log('🚀 Running all debug tests...');
    
    debugUrlParams();
    debugGlobals();
    debugFormFields();
    debugCanvas();
    
    await debugApiCall();
    
    const urlParams = new URLSearchParams(window.location.search);
    const moduleId = urlParams.get('edit') || urlParams.get('id');
    if (moduleId) {
        await debugModuleLoad(moduleId);
    }
    
    console.log('✅ All debug tests completed!');
}

// Console'da kullanım:
console.log(`
🔧 Debug Commands:
- debugApiCall()         - Test API connection
- debugUrlParams()       - Check URL parameters  
- debugModuleLoad(26)    - Test loading module ID 26
- debugGlobals()         - Check global variables
- debugFormFields()      - Check form fields
- debugCanvas()          - Check canvas elements
- simulateError()        - Test error handling
- runAllDebug()          - Run all tests

Example: debugModuleLoad(26)
`);

// Auto-run if current page has module ID
setTimeout(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const moduleId = urlParams.get('edit') || urlParams.get('id');
    if (moduleId) {
        console.log(`🔍 Auto-debug detected module ID: ${moduleId}`);
        debugModuleLoad(moduleId);
    }
}, 1000);