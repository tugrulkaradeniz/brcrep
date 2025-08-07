<?php

class CompanyUser
{
    private $db;
    
    public function __construct($database)
    {
        $this->db = $database;
    }
    
    public function getByCompany($companyId)
    {
        $stmt = $this->db->prepare("
            SELECT cu.*, c.company_name 
            FROM company_users cu
            JOIN companies c ON cu.company_id = c.id
            WHERE cu.company_id = ?
            ORDER BY cu.created_at DESC
        ");
        $stmt->execute([$companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id, $companyId = null)
    {
        $sql = "SELECT * FROM company_users WHERE id = ?";
        $params = [$id];
        
        if ($companyId) {
            $sql .= " AND company_id = ?";
            $params[] = $companyId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function authenticate($login, $password, $companyId)
    {
        // Login can be username or email
        $stmt = $this->db->prepare("
            SELECT cu.*, c.company_name, c.status as company_status
            FROM company_users cu
            JOIN companies c ON cu.company_id = c.id
            WHERE cu.company_id = ? 
            AND (cu.username = ? OR cu.email = ?)
            AND cu.status = 'active'
            AND c.status = 'active'
        ");
        
        $stmt->execute([$companyId, $login, $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // Update last login
            $this->updateLastLogin($user['id']);
            return $user;
        }
        
        return false;
    }
    
    public function create($data)
    {
        // Check username/email uniqueness within company
        if (!$this->isUsernameAvailable($data['username'], $data['company_id'])) {
            throw new Exception('Username already exists in this company');
        }
        
        if (!$this->isEmailAvailable($data['email'], $data['company_id'])) {
            throw new Exception('Email already exists in this company');
        }
        
        $stmt = $this->db->prepare("
            INSERT INTO company_users (
                company_id, username, email, password, first_name, 
                last_name, role, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['company_id'],
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['first_name'],
            $data['last_name'],
            $data['role'] ?? 'user',
            $data['status'] ?? 'active'
        ]);
    }
    
    public function update($id, $data, $companyId = null)
    {
        $fields = [];
        $values = [];
        
        $allowedFields = [
            'username', 'email', 'first_name', 'last_name', 'role', 'status'
        ];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                // Check uniqueness for username/email
                if ($field === 'username' && !$this->isUsernameAvailable($data[$field], $companyId, $id)) {
                    throw new Exception('Username already exists in this company');
                }
                if ($field === 'email' && !$this->isEmailAvailable($data[$field], $companyId, $id)) {
                    throw new Exception('Email already exists in this company');
                }
                
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }
        
        // Password update
        if (isset($data['password']) && !empty($data['password'])) {
            $fields[] = "password = ?";
            $values[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if (empty($fields)) return false;
        
        $values[] = $id;
        $sql = "UPDATE company_users SET " . implode(', ', $fields) . " WHERE id = ?";
        
        if ($companyId) {
            $sql .= " AND company_id = ?";
            $values[] = $companyId;
        }
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    public function delete($id, $companyId = null)
    {
        $sql = "DELETE FROM company_users WHERE id = ?";
        $params = [$id];
        
        if ($companyId) {
            $sql .= " AND company_id = ?";
            $params[] = $companyId;
        }
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    private function isUsernameAvailable($username, $companyId, $excludeId = null)
    {
        $sql = "SELECT id FROM company_users WHERE username = ? AND company_id = ?";
        $params = [$username, $companyId];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return !$stmt->fetch();
    }
    
    private function isEmailAvailable($email, $companyId, $excludeId = null)
    {
        $sql = "SELECT id FROM company_users WHERE email = ? AND company_id = ?";
        $params = [$email, $companyId];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return !$stmt->fetch();
    }
    
    private function updateLastLogin($userId)
    {
        $stmt = $this->db->prepare("UPDATE company_users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$userId]);
    }
}

?>