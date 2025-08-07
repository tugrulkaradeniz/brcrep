<?php
// services/CompanyContext.php

class CompanyContext {
    private static $companyId = null;
    private static $companyData = null;
    
    /**
     * Set company context
     */
    public static function set($companyId) {
        self::$companyId = $companyId;
        self::loadCompanyData();
    }
    
    /**
     * Get company ID
     */
    public static function getCompanyId() {
        return self::$companyId;
    }
    
    /**
     * Get company data
     */
    public static function getCompanyData() {
        if (self::$companyData === null) {
            self::loadCompanyData();
        }
        
        return self::$companyData ?: [
            'company_name' => 'Demo Company',
            'theme_color' => '#007bff'
        ];
    }
    
    /**
     * Load company data from database
     */
    private static function loadCompanyData() {
        if (!self::$companyId) {
            return;
        }
        
        try {
            global $pdo;
            
            if (!$pdo) {
                require_once '../dbConnect/dbkonfigur.php';
            }
            
            $stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ? AND status = 'active' LIMIT 1");
            $stmt->execute([self::$companyId]);
            $company = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($company) {
                self::$companyData = [
                    'id' => $company['id'],
                    'company_name' => $company['name'],
                    'company_code' => $company['domain'] ?: $company['subdomain'],
                    'theme_color' => $company['theme_color'] ?: '#007bff',
                    'logo_url' => $company['logo_url'],
                    'plan_type' => $company['plan_type'],
                    'status' => $company['status']
                ];
            }
            
        } catch (Exception $e) {
            error_log("CompanyContext::loadCompanyData() error: " . $e->getMessage());
            self::$companyData = [
                'company_name' => 'Demo Company',
                'theme_color' => '#007bff'
            ];
        }
    }
    
    /**
     * Clear company context
     */
    public static function clear() {
        self::$companyId = null;
        self::$companyData = null;
    }
}
?>