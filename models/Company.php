<?php
class Company
{
    private $db;
    
    public function __construct($database)
    {
        $this->db = $database;
    }
    
    public function getAll($limit = null, $offset = 0)
    {
        $sql = "
            SELECT c.*, 
                   COUNT(DISTINCT cu.id) as user_count,
                   COUNT(DISTINCT cms.id) as module_count
            FROM companies c
            LEFT JOIN company_users cu ON c.id = cu.company_id AND cu.status = 'active'
            LEFT JOIN company_module_subscriptions cms ON c.id = cms.company_id AND cms.status = 'active'
            GROUP BY c.id
            ORDER BY c.created_at DESC
        ";
        
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id)
    {
        $stmt = $this->db->prepare("
            SELECT c.*, 
                   COUNT(DISTINCT cu.id) as user_count,
                   COUNT(DISTINCT cms.id) as module_count
            FROM companies c
            LEFT JOIN company_users cu ON c.id = cu.company_id AND cu.status = 'active'
            LEFT JOIN company_module_subscriptions cms ON c.id = cms.company_id AND cms.status = 'active'
            WHERE c.id = ?
            GROUP BY c.id
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getBySubdomain($subdomain)
    {
        $stmt = $this->db->prepare("SELECT * FROM companies WHERE subdomain = ?");
        $stmt->execute([$subdomain]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO companies (
                company_name, subdomain, contact_name, contact_email, 
                contact_phone, billing_address, plan_type, status, 
                trial_end_date, max_users, logo_url, theme_color, settings
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['company_name'],
            $data['subdomain'],
            $data['contact_name'],
            $data['contact_email'],
            $data['contact_phone'] ?? null,
            $data['billing_address'] ?? null,
            $data['plan_type'] ?? 'basic',
            $data['status'] ?? 'trial',
            $data['trial_end_date'] ?? date('Y-m-d', strtotime('+30 days')),
            $data['max_users'] ?? 5,
            $data['logo_url'] ?? null,
            $data['theme_color'] ?? '#007bff',
            $data['settings'] ?? null
        ]);
    }
    
    public function update($id, $data)
    {
        $fields = [];
        $values = [];
        
        $allowedFields = [
            'company_name', 'subdomain', 'contact_name', 'contact_email',
            'contact_phone', 'billing_address', 'plan_type', 'status',
            'trial_end_date', 'subscription_end_date', 'max_users',
            'logo_url', 'theme_color', 'settings'
        ];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }
        
        if (empty($fields)) return false;
        
        $values[] = $id;
        $sql = "UPDATE companies SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    public function delete($id)
    {
        // Soft delete approach - sadece status'u inactive yap
        $stmt = $this->db->prepare("UPDATE companies SET status = 'suspended' WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function isSubdomainAvailable($subdomain, $excludeId = null)
    {
        $sql = "SELECT id FROM companies WHERE subdomain = ?";
        $params = [$subdomain];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return !$stmt->fetch();
    }
    
    public function getStats()
    {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_companies,
                COUNT(CASE WHEN status = 'active' THEN 1 END) as active_companies,
                COUNT(CASE WHEN status = 'trial' THEN 1 END) as trial_companies,
                COUNT(CASE WHEN status = 'suspended' THEN 1 END) as suspended_companies,
                COUNT(CASE WHEN plan_type = 'basic' THEN 1 END) as basic_plans,
                COUNT(CASE WHEN plan_type = 'premium' THEN 1 END) as premium_plans,
                COUNT(CASE WHEN plan_type = 'enterprise' THEN 1 END) as enterprise_plans
            FROM companies
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>