<?php
// ===== BRC TEMPLATE UPDATE SCRIPT =====
// File: database/scripts/update_brc_template.php
// Description: Load BRC Food Safety template into database

// Database connection
require_once '../../dbConnect/dbkonfigur.php';

// Load template file
$template_path = '../../templates/brc_food_safety_v9.json';
if (!file_exists($template_path)) {
    die("ERROR: Template file not found at: {$template_path}\n");
}

$template_content = file_get_contents($template_path);
$template_data = json_decode($template_content, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("ERROR: Invalid JSON in template file: " . json_last_error_msg() . "\n");
}

try {
    // Update the existing template
    $stmt = $pdo->prepare("
        UPDATE process_templates 
        SET template_data = ?, 
            template_version = ?,
            updated_at = NOW()
        WHERE template_code = 'brc_food_safety_v9'
    ");
    
    $result = $stmt->execute([
        json_encode($template_data),
        $template_data['template_info']['template_version']
    ]);
    
    if ($result && $stmt->rowCount() > 0) {
        echo "✅ SUCCESS: BRC Food Safety template updated successfully!\n";
        echo "📋 Template Info:\n";
        echo "   - Name: " . $template_data['template_info']['template_name'] . "\n";
        echo "   - Version: " . $template_data['template_info']['template_version'] . "\n";
        echo "   - Steps: " . count($template_data['process_steps']) . "\n";
        echo "   - Revision: " . $template_data['template_info']['revision'] . "\n";
        
        // Show step summary
        echo "\n📝 Process Steps:\n";
        foreach ($template_data['process_steps'] as $step) {
            echo "   {$step['step_number']}. {$step['step_name']} ({$step['estimated_duration']}min)\n";
        }
        
    } else {
        echo "⚠️ WARNING: No template found with code 'brc_food_safety_v9' - creating new one...\n";
        
        // Insert new template
        $stmt = $pdo->prepare("
            INSERT INTO process_templates 
            (template_name, template_code, brc_standard, template_version, template_data, is_active, created_by) 
            VALUES (?, ?, ?, ?, ?, 1, 1)
        ");
        
        $stmt->execute([
            $template_data['template_info']['template_name'],
            $template_data['template_info']['template_code'],
            $template_data['template_info']['brc_standard'],
            $template_data['template_info']['template_version'],
            json_encode($template_data)
        ]);
        
        echo "✅ SUCCESS: New BRC template created with ID: " . $pdo->lastInsertId() . "\n";
    }
    
    // Verify the template
    $stmt = $pdo->prepare("SELECT id, template_name, template_version, created_at, updated_at FROM process_templates WHERE template_code = 'brc_food_safety_v9'");
    $stmt->execute();
    $template = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($template) {
        echo "\n📊 Database Verification:\n";
        echo "   - Template ID: " . $template['id'] . "\n";
        echo "   - Created: " . $template['created_at'] . "\n";
        echo "   - Updated: " . $template['updated_at'] . "\n";
        
        // Test JSON parsing
        $stmt = $pdo->prepare("SELECT template_data FROM process_templates WHERE id = ?");
        $stmt->execute([$template['id']]);
        $stored_data = $stmt->fetchColumn();
        $parsed_data = json_decode($stored_data, true);
        
        if ($parsed_data && isset($parsed_data['process_steps'])) {
            echo "   - JSON Parse: ✅ Valid\n";
            echo "   - Steps Count: " . count($parsed_data['process_steps']) . "\n";
        } else {
            echo "   - JSON Parse: ❌ Invalid\n";
        }
    }
    
    echo "\n🎯 Next Steps:\n";
    echo "1. Test the API: php test_process_api.php\n";
    echo "2. Create frontend interface\n";
    echo "3. Test with sample data\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>