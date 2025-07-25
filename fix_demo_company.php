<?php

require_once 'config/config.php';
require_once 'dbConnect/dbkonfigur.php';

try {
    // Demo company'nin var olup olmadığını kontrol et
    $stmt = $baglanti->prepare("SELECT COUNT(*) FROM companies WHERE subdomain = 'demo'");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        echo "<h3>Demo company bulunamadı. Ekleniyor...</h3>";
        
        // Demo company ekle
        $stmt = $baglanti->prepare("
            INSERT INTO companies (company_name, subdomain, contact_name, contact_email, plan_type, status, theme_color) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            'Demo Company Ltd.',
            'demo',
            'John Doe',
            'john@democompany.com',
            'premium',
            'active',
            '#007bff'
        ]);
        
        $companyId = $baglanti->lastInsertId();
        echo "<p>✅ Demo company eklendi (ID: $companyId)</p>";
        
        // Demo user ekle
        $stmt = $baglanti->prepare("
            INSERT INTO company_users (company_id, username, email, password, first_name, last_name, role) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $companyId,
            'admin',
            'admin@democompany.com',
            password_hash('password123', PASSWORD_DEFAULT),
            'Demo',
            'Admin',
            'owner'
        ]);
        
        echo "<p>✅ Demo user eklendi</p>";
        echo "<p><strong>Artık demo company'ye giriş yapabilirsiniz:</strong></p>";
        echo "<p>URL: <a href='/brcproject/demo'>/brcproject/demo</a></p>";
        echo "<p>Username: admin</p>";
        echo "<p>Password: password123</p>";
        
    } else {
        echo "<h3>✅ Demo company zaten mevcut!</h3>";
        
        // Demo company bilgilerini göster
        $stmt = $baglanti->prepare("SELECT * FROM companies WHERE subdomain = 'demo'");
        $stmt->execute();
        $company = $stmt->fetch();
        
        echo "<p><strong>Company:</strong> " . $company['company_name'] . "</p>";
        echo "<p><strong>Status:</strong> " . $company['status'] . "</p>";
        echo "<p><strong>URL:</strong> <a href='/brcproject/demo'>/brcproject/demo</a></p>";
        
        // Demo user bilgilerini göster
        $stmt = $baglanti->prepare("SELECT * FROM company_users WHERE company_id = ?");
        $stmt->execute([$company['id']]);
        $users = $stmt->fetchAll();
        
        echo "<p><strong>Users:</strong></p>";
        foreach ($users as $user) {
            echo "<p>- {$user['username']} ({$user['email']}) - {$user['role']}</p>";
        }
    }
    
    echo "<hr>";
    echo '<p><a href="/brcproject/">← Ana Sayfaya Dön</a></p>';
    
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}

?>