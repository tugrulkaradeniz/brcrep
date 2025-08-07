<?php

session_start();
require_once '../../dbConnect/dbkonfigur.php';
require_once '../../models/Company.php';
require_once '../../models/CompanyUser.php';

// Platform admin authentication check
if (!isset($_SESSION['platform_admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$companyModel = new Company($baglanti);
$userModel = new CompanyUser($baglanti);

switch ($action) {
    case 'create':
        try {
            // Validate required fields
            $required = ['company_name', 'subdomain', 'contact_name', 'contact_email'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("$field is required");
                }
            }
            
            // Check subdomain availability
            if (!$companyModel->isSubdomainAvailable($_POST['subdomain'])) {
                throw new Exception('Subdomain already exists');
            }
            
            // Create company
            $companyData = [
                'company_name' => $_POST['company_name'],
                'subdomain' => strtolower(trim($_POST['subdomain'])),
                'contact_name' => $_POST['contact_name'],
                'contact_email' => $_POST['contact_email'],
                'contact_phone' => $_POST['contact_phone'] ?? null,
                'billing_address' => $_POST['billing_address'] ?? null,
                'plan_type' => $_POST['plan_type'] ?? 'basic',
                'status' => $_POST['status'] ?? 'trial',
                'max_users' => intval($_POST['max_users'] ?? 5)
            ];
            
            if ($companyModel->create($companyData)) {
                $companyId = $baglanti->lastInsertId();
                
                // Create default admin user for the company
                $adminData = [
                    'company_id' => $companyId,
                    'username' => 'admin',
                    'email' => $companyData['contact_email'],
                    'password' => 'password123', // Default password
                    'first_name' => explode(' ', $companyData['contact_name'])[0],
                    'last_name' => explode(' ', $companyData['contact_name'])[1] ?? '',
                    'role' => 'owner'
                ];
                
                $userModel->create($adminData);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Company created successfully',
                    'company_id' => $companyId
                ]);
            } else {
                throw new Exception('Failed to create company');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;
        
    case 'update':
        try {
            $companyId = $_POST['company_id'];
            if (empty($companyId)) {
                throw new Exception('Company ID is required');
            }
            
            $updateData = [];
            $allowedFields = [
                'company_name', 'subdomain', 'contact_name', 'contact_email',
                'contact_phone', 'billing_address', 'plan_type', 'status', 'max_users'
            ];
            
            foreach ($allowedFields as $field) {
                if (isset($_POST[$field])) {
                    $updateData[$field] = $_POST[$field];
                }
            }
            
            // Check subdomain availability if changed
            if (isset($updateData['subdomain'])) {
                if (!$companyModel->isSubdomainAvailable($updateData['subdomain'], $companyId)) {
                    throw new Exception('Subdomain already exists');
                }
            }
            
            if ($companyModel->update($companyId, $updateData)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Company updated successfully'
                ]);
            } else {
                throw new Exception('Failed to update company');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;
        
    case 'delete':
        try {
            $companyId = $_POST['company_id'];
            if (empty($companyId)) {
                throw new Exception('Company ID is required');
            }
            
            if ($companyModel->delete($companyId)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Company suspended successfully'
                ]);
            } else {
                throw new Exception('Failed to suspend company');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;
        
    case 'list':
        try {
            $companies = $companyModel->getAll();
            echo json_encode([
                'success' => true,
                'data' => $companies
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;
        
    case 'get':
        try {
            $companyId = $_GET['company_id'];
            if (empty($companyId)) {
                throw new Exception('Company ID is required');
            }
            
            $company = $companyModel->getById($companyId);
            if ($company) {
                echo json_encode([
                    'success' => true,
                    'data' => $company
                ]);
            } else {
                throw new Exception('Company not found');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;
        
    case 'check_subdomain':
        $subdomain = $_GET['subdomain'] ?? '';
        $excludeId = $_GET['exclude_id'] ?? null;
        
        $available = $companyModel->isSubdomainAvailable($subdomain, $excludeId);
        echo json_encode([
            'available' => $available,
            'subdomain' => $subdomain
        ]);
        break;
        
    default:
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
}

?>