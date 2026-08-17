<?php 

namespace App\Database;

use PDO;
use PDOException;
use App\Traits\LoggerTrait;
use App\Exceptions\DatabaseException;

class Database
{
    use LoggerTrait;

    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}
    
    public static function getConnection(): PDO 
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/database.php';  
            $dsn = sprintf(
                "%s:host=%s;port=%s;dbname=%s",
                $config['driver'],
                $config['host'],
                $config['port'],
                $config['database']
            );
            try {
                self::$instance = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    $config['options']
                );
            } catch (PDOException $e) {
                self::logError($e, ['context' => 'Database Connection Initialization']);
                throw new DatabaseException("Impossible de se connecter à la base de données.");
            }
        }
        return self::$instance;
    }
    
    public static function query(string $sql, array $params = []): \PDOStatement {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
    }
    
}