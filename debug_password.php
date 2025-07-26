<?php
// Şifre debug testi - bu dosyayı oluşturup tarayıcıda açın
// Dosya: /brcproject/debug_password.php

echo "<h2>Password Debug Test</h2>";

// Test verileri
$inputPassword = '123456';
$storedPassword = '123456';

echo "<p>Input password: '$inputPassword'</p>";
echo "<p>Input length: " . strlen($inputPassword) . "</p>";
echo "<p>Input var_dump: ";
var_dump($inputPassword);
echo "</p>";

echo "<p>Stored password: '$storedPassword'</p>";
echo "<p>Stored length: " . strlen($storedPassword) . "</p>";
echo "<p>Stored var_dump: ";
var_dump($storedPassword);
echo "</p>";

// Test karşılaştırmaları
echo "<h3>Comparisons:</h3>";
echo "<p>String comparison (===): " . ($inputPassword === $storedPassword ? 'TRUE' : 'FALSE') . "</p>";
echo "<p>String comparison (==): " . ($inputPassword == $storedPassword ? 'TRUE' : 'FALSE') . "</p>";
echo "<p>strcmp result: " . strcmp($inputPassword, $storedPassword) . "</p>";

// Character by character
echo "<h3>Character Analysis:</h3>";
for ($i = 0; $i < max(strlen($inputPassword), strlen($storedPassword)); $i++) {
    $char1 = isset($inputPassword[$i]) ? $inputPassword[$i] : 'NULL';
    $char2 = isset($storedPassword[$i]) ? $storedPassword[$i] : 'NULL';
    $ord1 = isset($inputPassword[$i]) ? ord($inputPassword[$i]) : 'NULL';
    $ord2 = isset($storedPassword[$i]) ? ord($storedPassword[$i]) : 'NULL';
    
    echo "<p>Position $i: '$char1' (ord: $ord1) vs '$char2' (ord: $ord2)</p>";
}

// Veritabanından gerçek veriyi alalım
require_once __DIR__ . '/dbConnect/dbkonfigur.php';

if (isset($pdo)) {
    $stmt = $pdo->prepare("SELECT password FROM platform_admins WHERE username = 'admin'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $dbPassword = $result['password'];
        echo "<h3>Database Password Analysis:</h3>";
        echo "<p>DB password: '$dbPassword'</p>";
        echo "<p>DB length: " . strlen($dbPassword) . "</p>";
        echo "<p>DB var_dump: ";
        var_dump($dbPassword);
        echo "</p>";
        
        echo "<p>Input vs DB (===): " . ($inputPassword === $dbPassword ? 'TRUE' : 'FALSE') . "</p>";
        echo "<p>Input vs DB (==): " . ($inputPassword == $dbPassword ? 'TRUE' : 'FALSE') . "</p>";
        
        // Trim test
        echo "<p>Trimmed comparison: " . (trim($inputPassword) === trim($dbPassword) ? 'TRUE' : 'FALSE') . "</p>";
    }
}
?>