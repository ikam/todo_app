<?php

$dns = 'mysql:host=localhost:3306;dbname=blog';
$user = 'root';
$pwd = '112233';

try {
    $pdo = new PDO($dns, $user, $pwd, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC

    ]);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

return $pdo;