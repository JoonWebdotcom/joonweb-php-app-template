<?php
namespace App\Controllers;

use JoonWeb\Auth\OAuth;
use App\Database\SessionStorageFactory;

class AuthController {
    
    /**
     * Securely verify HMAC signature to ensure the request is from JoonWeb.
     */
    private function verifyHmac(array $params): bool {
        $hmac = $params['hmac'] ?? '';
        unset($params['hmac']);
        
        $config = require __DIR__ . '/../../config/joonweb.php';
        $secret = $config['api_secret'];
        
        ksort($params);
        $message = http_build_query($params);
        $calculated_hmac = hash_hmac('sha256', $message, $secret);
        
        return hash_equals($hmac, $calculated_hmac);
    }

    public function install() {
        if (!$this->verifyHmac($_GET)) {
            die('Invalid HMAC signature');
        }

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
        if (!$this->verifyHmac($_GET)) {
            die('Invalid HMAC signature');
        }

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

            // Redirect back to JoonWeb embed URL
            $config = require __DIR__ . '/../../config/joonweb.php';
            $app_slug = $_GET['app_slug'] ?? $config['api_key']; // Default to client_id if app_slug is missing
            $site_hash = $_GET['site_hash'] ?? '';
            
            $base_url = "https://accounts.joonweb.com/site/";
            $embed_url = $base_url . '?sitehash=' . urlencode($site_hash) . '&apps&' . urlencode($app_slug);
            
            header("Location: " . $embed_url);
            exit;
        } catch (\Exception $e) {
            die('Authentication failed: ' . $e->getMessage());
        }
    }
}
