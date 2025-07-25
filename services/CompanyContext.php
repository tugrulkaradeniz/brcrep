<?php

class CompanyContext
{
    private static $companyId = null;
    private static $companyData = null;
    
    public static function set($companyId)
    {
        self::$companyId = $companyId;
        self::loadCompanyData();
    }
    
    public static function get()
    {
        return self::$companyId;
    }
    
    public static function getCompanyData()
    {
        return self::$companyData;
    }
    
    private static function loadCompanyData()
    {
        if (!self::$companyId) return;
        
        global $baglanti;
        $stmt = $baglanti->prepare("
            SELECT c.*, 
                   COUNT(cu.id) as user_count,
                   COUNT(cms.id) as module_count
            FROM companies c
            LEFT JOIN company_users cu ON c.id = cu.company_id AND cu.status = 'active'
            LEFT JOIN company_module_subscriptions cms ON c.id = cms.company_id AND cms.status = 'active'
            WHERE c.id = ?
            GROUP BY c.id
        ");
        
        $stmt->execute([self::$companyId]);
        self::$companyData = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function checkSubscription($moduleCode)
    {
        if (!self::$companyId) return false;
        
        global $baglanti;
        $stmt = $baglanti->prepare("
            SELECT cms.id FROM company_module_subscriptions cms
            JOIN marketplace_modules mm ON cms.module_id = mm.id
            WHERE cms.company_id = ? 
            AND mm.module_code = ? 
            AND cms.status = 'active'
            AND (cms.expires_at IS NULL OR cms.expires_at > CURRENT_DATE)
        ");
        
        $stmt->execute([self::$companyId, $moduleCode]);
        return $stmt->fetch() !== false;
    }
    
    public static function getSubscribedModules()
    {
        if (!self::$companyId) return [];
        
        global $baglanti;
        $stmt = $baglanti->prepare("
            SELECT mm.*, cms.subscribed_at, cms.expires_at
            FROM marketplace_modules mm
            JOIN company_module_subscriptions cms ON mm.id = cms.module_id
            WHERE cms.company_id = ? AND cms.status = 'active'
            ORDER BY cms.subscribed_at DESC
        ");
        
        $stmt->execute([self::$companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>