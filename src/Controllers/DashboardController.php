<?php
namespace App\Controllers;

use JoonWeb\JoonWebAPI;
use App\Database\SessionStorageFactory;

class DashboardController {
    
    public function index() {
        $siteDomain = $_GET['site'] ?? '';
        
        // Ensure the app is loaded within the Joonweb context
        if (empty($siteDomain)) {
            $this->renderView('dashboard', [
                'error' => 'App must be loaded within the Joonweb Admin Dashboard.', 
                'products' => []
            ]);
            return;
        }

        try {
            // Retrieve Database Configuration and Session Storage
            $dbConfig = require __DIR__ . '/../../config/database.php';
            $storage = SessionStorageFactory::create($dbConfig);
            
            // Get the access token for this specific site
            $tokenData = $storage->getToken($siteDomain);
            if (!$tokenData || empty($tokenData['access_token'])) {
                $this->renderView('dashboard', [
                    'error' => 'Not authenticated. Please reinstall the app.', 
                    'products' => []
                ]);
                return;
            }

            // Initialize the SDK
            $api = new JoonWebAPI($tokenData['access_token'], $siteDomain);
            
            // Fetch Products using the SDK
            $response = $api->product->all(['limit' => 20]);
            
            // Normalize response to array
            $products = is_object($response) && isset($response->data) 
                ? $response->data 
                : (is_array($response) ? $response : []);
            
            $this->renderView('dashboard', [
                'site' => $siteDomain, 
                'products' => $products
            ]);

        } catch (\Exception $e) {
            $this->renderView('dashboard', [
                'error' => 'Failed to connect to Joonweb API: ' . $e->getMessage(), 
                'products' => []
            ]);
        }
    }

    private function renderView($view, $data = []) {
        extract($data);
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "View not found: " . htmlspecialchars($view);
        }
    }
}
