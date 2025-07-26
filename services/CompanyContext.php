<?php
// services/CompanyContext.php

class CompanyContext {
    private static $instance = null;
    private $companyData = null;
    private $db = null;
    
    public function __construct() {
        $this->initDatabase();
    }
    
    /**
     * Veritabanı bağlantısını başlat
     */
    private function initDatabase() {
        global $pdo, $db;
        
        // Global değişkenlerden veritabanı bağlantısını al
        if (isset($pdo)) {
            $this->db = $pdo;
        } elseif (isset($db)) {
            $this->db = $db;
        } else {
            // Eğer global değişken yoksa, doğrudan bağlantı kur
            try {
                require_once __DIR__ . '/../dbConnect/dbkonfigur.php';
                if (isset($pdo)) {
                    $this->db = $pdo;
                } elseif (isset($db)) {
                    $this->db = $db;
                }
            } catch (Exception $e) {
                error_log("CompanyContext DB connection failed: " . $e->getMessage());
                $this->db = null;
            }
        }
        
        // Hala bağlantı yoksa hata logla
        if (!$this->db) {
            error_log("CompanyContext: Database connection is null");
        }
    }
    
    /**
     * Company context'i set et
     */
    public function set($tenant) {
        try {
            if (!$tenant) {
                throw new Exception("Tenant object is null");
            }
            
            // Tenant tipini kontrol et
            if (is_object($tenant) && method_exists($tenant, 'getCompanyCode')) {
                $companyCode = $tenant->getCompanyCode();
            } elseif (is_string($tenant)) {
                $companyCode = $tenant;
            } else {
                throw new Exception("Invalid tenant type");
            }
            
            $this->loadCompanyData($companyCode);
            
        } catch (Exception $e) {
            error_log("CompanyContext::set() error: " . $e->getMessage());
            // Hata durumunda default company kullan veya hata sayfasına yönlendir
            $this->setDefaultCompany();
        }
    }
    
    /**
     * Company verilerini yükle
     */
    private function loadCompanyData($companyCode) {
        // Veritabanı bağlantısını kontrol et
        if (!$this->db) {
            throw new Exception("Database connection not available");
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, 
                       COUNT(cu.id) as user_count,
                       COUNT(cms.id) as module_count
                FROM companies c 
                LEFT JOIN company_users cu ON c.id = cu.company_id 
                LEFT JOIN company_module_subscriptions cms ON c.id = cms.company_id 
                WHERE c.company_code = ? AND c.status = 'active'
                GROUP BY c.id
            ");
            
            $stmt->execute([$companyCode]);
            $this->companyData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$this->companyData) {
                throw new Exception("Company not found: " . $companyCode);
            }
            
            // Session'a company bilgilerini kaydet
            $_SESSION['company_context'] = $this->companyData;
            
        } catch (PDOException $e) {
            throw new Exception("Database query failed: " . $e->getMessage());
        }
    }
    
    /**
     * Default company set et
     */
    private function setDefaultCompany() {
        $this->companyData = [
            'id' => 1,
            'company_name' => 'Default Company',
            'company_code' => 'default',
            'status' => 'active',
            'user_count' => 0,
            'module_count' => 0
        ];
        
        $_SESSION['company_context'] = $this->companyData;
    }
    
    /**
     * Aktif company verilerini al
     */
    public function getCompany() {
        return $this->companyData;
    }
    
    /**
     * Company ID'sini al
     */
    public function getCompanyId() {
        return $this->companyData['id'] ?? null;
    }
    
    /**
     * Company code'unu al
     */
    public function getCompanyCode() {
        return $this->companyData['company_code'] ?? null;
    }
    
    /**
     * Company adını al
     */
    public function getCompanyName() {
        return $this->companyData['company_name'] ?? 'Unknown Company';
    }
    
    /**
     * Context'in set edilip edilmediğini kontrol et
     */
    public function isSet() {
        return !empty($this->companyData);
    }
    
    /**
     * Context'i temizle
     */
    public function clear() {
        $this->companyData = null;
        unset($_SESSION['company_context']);
    }
    
    /**
     * Singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
?>