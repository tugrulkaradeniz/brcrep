<?php

class PlatformAdmin
{
    private $db;
    
    public function __construct($database)
    {
        $this->db = $database;
    }
    
    public function authenticate($login, $password)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM platform_admins 
            WHERE (username = ? OR email = ?) AND status = 'active'
        ");
        
        $stmt->execute([$login, $login]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin && password_verify($password, $admin['password'])) {
            $this->updateLastLogin($admin['id']);
            return $admin;
        }
        
        return false;
    }
    
    public function getAll()
    {
        $stmt = $this->db->prepare("
            SELECT * FROM platform_admins 
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM platform_admins WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function updateLastLogin($adminId)
    {
        $stmt = $this->db->prepare("UPDATE platform_admins SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$adminId]);
    }
}

?>