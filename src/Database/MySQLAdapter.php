<?php
namespace App\Database;

use PDO;
use Exception;

class MySQLAdapter implements SessionStorageInterface {
    private PDO $pdo;

    public function __construct(string $host, string $dbName, string $user, string $pass) {
        $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8mb4";
        $this->pdo = new PDO($dsn, $user, $pass);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Initialize table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS site_tokens (
                site_domain VARCHAR(255) PRIMARY KEY,
                access_token VARCHAR(255) NOT NULL,
                scope TEXT,
                associated_user TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    public function saveToken(string $siteDomain, array $tokenData): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO site_tokens (site_domain, access_token, scope, associated_user) 
            VALUES (:domain, :token, :scope, :user)
            ON DUPLICATE KEY UPDATE 
                access_token = VALUES(access_token),
                scope = VALUES(scope),
                associated_user = VALUES(associated_user)
        ");
        
        return $stmt->execute([
            ':domain' => $siteDomain,
            ':token' => $tokenData['access_token'] ?? '',
            ':scope' => isset($tokenData['scope']) ? implode(',', $tokenData['scope']) : '',
            ':user' => isset($tokenData['associated_user']) ? json_encode($tokenData['associated_user']) : null
        ]);
    }

    public function getToken(string $siteDomain): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM site_tokens WHERE site_domain = :domain");
        $stmt->execute([':domain' => $siteDomain]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        
        return [
            'access_token' => $row['access_token'],
            'scope' => !empty($row['scope']) ? explode(',', $row['scope']) : [],
            'associated_user' => $row['associated_user'] ? json_decode($row['associated_user'], true) : null
        ];
    }

    public function deleteToken(string $siteDomain): bool {
        $stmt = $this->pdo->prepare("DELETE FROM site_tokens WHERE site_domain = :domain");
        return $stmt->execute([':domain' => $siteDomain]);
    }
}
