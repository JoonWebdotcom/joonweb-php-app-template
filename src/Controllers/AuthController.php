<?php
namespace App\Controllers;

use JoonWeb\Auth\OAuth;
use App\Database\SessionStorageFactory;

class AuthController {
    


    public function install() {
        $config = require __DIR__ . '/../../config/joonweb.php';
        if (!\JoonWeb\Helper::verifyHmac($_GET, $config['api_secret'])) {
            die('Invalid HMAC signature');
        }

        $siteDomain = $_GET['site'] ?? '';
        
        if (empty($siteDomain)) {
            die('Missing site parameter');
        }

        $scopes = ['read_products', 'write_products']; // Add your required scopes
        $redirectUri = $config['app_url'] . '/auth/callback';

        $authUrl = OAuth::getAuthorizationUrl($siteDomain, $scopes, $redirectUri);
        header("Location: " . $authUrl);
        exit;
    }

    public function callback() {
        $config = require __DIR__ . '/../../config/joonweb.php';
        if (!\JoonWeb\Helper::verifyHmac($_GET, $config['api_secret'])) {
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
            $app_slug = $_GET['app_slug'] ?? $config['api_key']; // Default to client_id if app_slug is missing
            $site_hash = $_GET['site_hash'] ?? '';
            
            $embed_url = \JoonWeb\Helper::getEmbeddedAppUrl($site_hash, $app_slug);
            
            header("Location: " . $embed_url);
            exit;
        } catch (\Exception $e) {
            die('Authentication failed: ' . $e->getMessage());
        }
    }
}
