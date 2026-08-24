<?php
namespace App\Database;

use Exception;

class SessionStorageFactory {
    public static function create(array $config): SessionStorageInterface {
        $driver = $config['driver'] ?? 'sqlite';

        switch ($driver) {
            case 'sqlite':
                $dbPath = $config['sqlite']['path'] ?? __DIR__ . '/../../storage/app-db.sqlite';
                return new SQLiteAdapter($dbPath);
            
            case 'mysql':
                return new MySQLAdapter(
                    $config['mysql']['host'] ?? '127.0.0.1',
                    $config['mysql']['database'] ?? 'joonweb_app',
                    $config['mysql']['username'] ?? 'root',
                    $config['mysql']['password'] ?? ''
                );
            
            case 'mongodb':
                throw new Exception("MongoDB Adapter is not yet implemented.");
                
            default:
                throw new Exception("Unsupported database driver: {$driver}");
        }
    }
}
