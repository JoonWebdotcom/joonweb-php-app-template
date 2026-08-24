<?php
namespace App\Controllers;

use JoonWeb\Auth\OAuth;

class WebhookController {
    
    public function handle() {
        $payload = file_get_contents('php://input');
        $hmac = $_SERVER['HTTP_X_JOONWEB_HMAC_SHA256'] ?? '';

        if (!OAuth::verifyWebhook($payload, $hmac)) {
            http_response_code(401);
            echo 'Invalid Webhook Signature';
            exit;
        }

        $topic = $_SERVER['HTTP_X_JOONWEB_TOPIC'] ?? '';
        $siteDomain = $_SERVER['HTTP_X_JOONWEB_SITE_DOMAIN'] ?? '';

        // Handle specific topics
        switch ($topic) {
            case 'app/uninstalled':
                $this->handleAppUninstalled($siteDomain);
                break;
            // Add other webhook topics here
        }

        http_response_code(200);
        echo 'Webhook Processed';
    }

    private function handleAppUninstalled($siteDomain) {
        $dbConfig = require __DIR__ . '/../../config/database.php';
        $storage = \App\Database\SessionStorageFactory::create($dbConfig);
        $storage->deleteToken($siteDomain);
    }
}
