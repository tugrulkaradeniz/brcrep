<?php

class TenantContext 
{
    private $type;
    private $companyId = null;
    private $subdomain = null;
    
    public static function detect()
    {
        $instance = new self();
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $query = $_SERVER['QUERY_STRING'] ?? '';
        
        // Debug için log ekle
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            error_log("TenantContext Debug - Host: $host, URI: $uri, Query: $query");
        }
        
        // Platform admin detection - GENİŞLETİLDİ
        if (strpos($query, 'page=admin') !== false || 
            strpos($uri, '/admin') !== false ||
            (strpos($query, 'page=login') !== false && strpos($query, 'action=login') !== false)) {
            $instance->type = 'platform';
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                error_log("TenantContext: Detected PLATFORM - Query: $query");
            }
        }
        // Subdomain detection (XAMPP için path-based)
        else if (preg_match('/\/brcproject\/(demo|test|company1)/', $uri, $matches)) {
            $instance->type = 'customer';
            $instance->subdomain = $matches[1];
            $instance->loadCompanyData();
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                error_log("TenantContext: Detected CUSTOMER - " . $matches[1]);
            }
        }
        // Main website
        else {
            $instance->type = 'main';
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                error_log("TenantContext: Detected MAIN");
            }
        }
        
        return $instance;
    }
    
    private function loadCompanyData()
    {
        if (!$this->subdomain) return;
        
        try {
            global $baglanti;
            if (!$baglanti) return;
            
            $stmt = $baglanti->prepare("SELECT id FROM companies WHERE subdomain = ? AND status = 'active'");
            $stmt->execute([$this->subdomain]);
            
            if ($company = $stmt->fetch()) {
                $this->companyId = $company['id'];
                if (defined('DEBUG_MODE') && DEBUG_MODE) {
                    error_log("TenantContext: Found company ID: " . $this->companyId);
                }
            }
        } catch (Exception $e) {
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                error_log("TenantContext Error: " . $e->getMessage());
            }
        }
    }
    
    public function getType() { return $this->type; }
    public function getCompanyId() { return $this->companyId; }
    public function getSubdomain() { return $this->subdomain; }
    public function isValid() { return $this->type !== 'unknown'; }
}

?>