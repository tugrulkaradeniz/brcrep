<?php

class Module
{
    private $db;
    
    public function __construct($database)
    {
        $this->db = $database;
    }
    
    public function getAll($status = null)
    {
        $sql = "
            SELECT mm.*, pa.username as created_by_name,
                   COUNT(DISTINCT cms.company_id) as subscriber_count
            FROM marketplace_modules mm
            LEFT JOIN platform_admins pa ON mm.created_by = pa.id
            LEFT JOIN company_module_subscriptions cms ON mm.id = cms.module_id AND cms.status = 'active'
        ";
        
        if ($status) {
            $sql .= " WHERE mm.status = ?";
        }
        
        $sql .= " GROUP BY mm.id ORDER BY mm.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        if ($status) {
            $stmt->execute([$status]);
        } else {
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPublished()
    {
        return $this->getAll('published');
    }
    
    public function getById($id)
    {
        $stmt = $this->db->prepare("
            SELECT mm.*, pa.username as created_by_name,
                   COUNT(DISTINCT cms.company_id) as subscriber_count
            FROM marketplace_modules mm
            LEFT JOIN platform_admins pa ON mm.created_by = pa.id
            LEFT JOIN company_module_subscriptions cms ON mm.id = cms.module_id AND cms.status = 'active'
            WHERE mm.id = ?
            GROUP BY mm.id
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getByCompany($companyId)
    {
        $stmt = $this->db->prepare("
            SELECT mm.*, cms.subscribed_at, cms.expires_at, cms.status as subscription_status
            FROM marketplace_modules mm
            JOIN company_module_subscriptions cms ON mm.id = cms.module_id
            WHERE cms.company_id = ?
            ORDER BY cms.subscribed_at DESC
        ");
        $stmt->execute([$companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>