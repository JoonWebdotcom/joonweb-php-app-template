<?php
namespace App\Controllers;

use JoonWeb\Auth\OAuth;
use App\Database\SessionStorageFactory;

class AuthController {
    
    public function install() {
        $siteDomain = $_GET['site'] ?? '';
        
        if (empty($siteDomain)) {
            die('Missing site parameter');
        }

        $config = require __DIR__ . '/../../config/joonweb.php';
        $scopes = ['read_products', 'write_products']; // Add your required scopes
        $redirectUri = $config['app_url'] . '/auth/callback';

        $authUrl = OAuth::getAuthorizationUrl($siteDomain, $scopes, $redirectUri);
        header("Location: " . $authUrl);
        exit;
    }

    public function callback() {
        $siteDomain = $_GET['site'] ?? '';
        $code = $_GET['code'] ?? '';

        if (empty($siteDomain) || empty($code)) {
            die('Missing site or code parameters');
        }

        try {
            $tokenData = OAuth::exchangeCodeForToken($siteDomain, $code);
            
            // Store token using Factory
            $dbConfig = require __DIR__ . '/../../config/database.php';
            $storage = SessionStorageFactory::create($dbConfig);
            
            $storage->saveToken($siteDomain, $tokenData);

            // Redirect back to Joonweb Admin
            header("Location: https://{$siteDomain}/admin/apps");
            exit;
        } catch (\Exception $e) {
            die('Authentication failed: ' . $e->getMessage());
        }
    }
}
