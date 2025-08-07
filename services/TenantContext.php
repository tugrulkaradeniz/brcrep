<?php
// services/TenantContext.php

class TenantContext {
    private $db = null;
    private $companyCode = null;
    
    public function __construct() {
        $this->initDatabase();
    }
    
    /**
     * Veritabanı bağlantısını başlat
     */
    private function initDatabase() {
        global $pdo, $db;
        
        if (isset($pdo)) {
            $this->db = $pdo;
        } elseif (isset($db)) {
            $this->db = $db;
        } else {
            // Veritabanı bağlantısı kurulmamışsa sessizce devam et
            // Çünkü tenant detection her zaman DB gerektirmez
            $this->db = null;
        }
    }
    
    /**
     * Tenant'ı detect et
     */
    public function detect() {
        try {
            // 1. URL path'den tenant detect et (XAMPP için)
            $tenant = $this->detectFromPath();
            
            if ($tenant) {
                return $tenant;
            }
            
            // 2. Subdomain'den detect et (production için)
            $tenant = $this->detectFromSubdomain();
            
            if ($tenant) {
                return $tenant;
            }
            
            // 3. Query parameter'den detect et
            $tenant = $this->detectFromQuery();
            
            if ($tenant) {
                return $tenant;
            }
            
            // Tenant bulunamadı
            return null;
            
        } catch (Exception $e) {
            error_log("TenantContext::detect() error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * URL path'den tenant detect et
     * Örnek: /brcproject/demo -> 'demo'
     * Örnek: /brcproject/company1 -> 'company1'
     */
    private function detectFromPath() {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $basePath = '/brcproject';
        
        // Base path'i kaldır
        if (strpos($requestUri, $basePath) === 0) {
            $requestUri = substr($requestUri, strlen($basePath));
        }
        
        // Query string'i kaldır
        if (strpos($requestUri, '?') !== false) {
            $requestUri = substr($requestUri, 0, strpos($requestUri, '?'));
        }
        
        // Path parçalarını al
        $pathParts = explode('/', trim($requestUri, '/'));
        
        // İlk parça tenant olabilir mi kontrol et
        if (!empty($pathParts[0])) {
            $potentialTenant = $pathParts[0];
            
            // Admin, platform, website değilse tenant olabilir
            $systemPaths = ['admin', 'platform', 'website', 'assets', 'api'];
            
            if (!in_array($potentialTenant, $systemPaths)) {
                // Veritabanından kontrol et (eğer DB varsa)
                if ($this->db && $this->validateTenant($potentialTenant)) {
                    $this->companyCode = $potentialTenant;
                    return $this;
                }
                
                // Demo/test için hardcoded tenant'lar
                $demoTenants = ['demo', 'test', 'company1', 'company2'];
                if (in_array($potentialTenant, $demoTenants)) {
                    $this->companyCode = $potentialTenant;
                    return $this;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Subdomain'den tenant detect et
     * Örnek: company1.brcload.com -> 'company1'
     */
    private function detectFromSubdomain() {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        
        // Localhost'ta subdomain çalışmaz, sadıklıkla test için
        if (strpos($host, 'localhost') !== false) {
            return null;
        }
        
        // Subdomain'i çıkar
        $hostParts = explode('.', $host);
        
        if (count($hostParts) >= 3) {
            $subdomain = $hostParts[0];
            
            // www değilse ve boş değilse tenant olabilir
            if ($subdomain !== 'www' && !empty($subdomain)) {
                if ($this->db && $this->validateTenant($subdomain)) {
                    $this->companyCode = $subdomain;
                    return $this;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Query parameter'den tenant detect et
     * Örnek: ?company=demo
     */
    private function detectFromQuery() {
        $company = $_GET['company'] ?? null;
        
        if ($company) {
            if ($this->db && $this->validateTenant($company)) {
                $this->companyCode = $company;
                return $this;
            }
            
            // Demo tenant'lar için
            $demoTenants = ['demo', 'test', 'company1', 'company2'];
            if (in_array($company, $demoTenants)) {
                $this->companyCode = $company;
                return $this;
            }
        }
        
        return null;
    }
    
    /**
     * Tenant'ı veritabanından validate et
     */
    private function validateTenant($tenantCode) {
        if (!$this->db) {
            return false;
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) 
                FROM companies 
                WHERE company_code = ? AND status = 'active'
            ");
            $stmt->execute([$tenantCode]);
            
            return $stmt->fetchColumn() > 0;
            
        } catch (PDOException $e) {
            error_log("TenantContext::validateTenant() error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Company code'unu al
     */
    public function getCompanyCode() {
        return $this->companyCode;
    }
    
    /**
     * Tenant set edilmiş mi kontrol et
     */
    public function isSet() {
        return !empty($this->companyCode);
    }

    /**
 * Company ID'yi al
 */
    public function getCompanyId() {
        if (!$this->companyCode) {
            return null;
        }
        
        try {
            if (!$this->db) {
                // Demo companies için hardcoded ID'ler
                $demoMapping = [
                    'demo' => 1,
                    'test' => 2, 
                    'company1' => 3
                ];
                
                return $demoMapping[$this->companyCode] ?? 1;
            }
            
            $stmt = $this->db->prepare("SELECT id FROM companies WHERE domain = ? OR subdomain = ? AND status = 'active' LIMIT 1");
            $stmt->execute([$this->companyCode, $this->companyCode]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ? $result['id'] : null;
            
        } catch (PDOException $e) {
            error_log("TenantContext::getCompanyId() error: " . $e->getMessage());
            return 1; // Fallback to demo company
        }
    }

    /**
     * Subdomain'i al  
     */
    public function getSubdomain() {
        return $this->companyCode;
    }
}
?>