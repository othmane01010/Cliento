<?php

namespace App\Models;

use App\Database\Database;
use App\Interfaces\ModelInterface;
use App\Traits\LoggerTrait;
use App\Exceptions\DatabaseException;
use PDO;
use PDOException;

abstract class BaseModel implements ModelInterface {
    use LoggerTrait;
    protected static string $table = '';
    
     public static function all(): array{
        try {
            $sql = sprintf("SELECT * FROM %s ORDER BY id DESC", static::$table);
            return Database::query($sql)->fetchAll();
        } catch (PDOException $e) {
            self::logError($e, ['method' => static::class . '::all', 'table' => static::$table]);
            throw new DatabaseException("Erreur lors de la récupération des données.");
        }
    }
       

   public static function find(int $id): ?array{
    try {
        $sql = sprintf("SELECT * FROM %s WHERE id = ? LIMIT 1", static::$table);
        $result = Database::query($sql, [$id])->fetch();

        return $result ?: null;
    } catch (PDOException $e) {
        self::logError($e, [
            'method'    => static::class . '::find',
            'table'     => static::$table,
            'record_id' => $id
        ]);
        throw new DatabaseException("Erreur lors de la recherche de l'enregistrement.");
    }
  }

   public static function delete(int $id): bool{
    try {
        $sql = sprintf("DELETE FROM %s WHERE id = ?", static::$table);
        $stmt = Database::query($sql, [$id]);

        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        self::logError($e, [
            'method'    => static::class . '::delete',
            'table'     => static::$table,
            'record_id' => $id,
        ]);
       throw new DatabaseException("Erreur lors de la suppression de l'enregistrement.");
    }
  }
   
        public static function count(): int{
        try {
            $sql = sprintf("SELECT COUNT(*) FROM %s", static::$table);
            $stmt = Database::query($sql);

            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            self::logError($e, [
                'method' => static::class . '::count',
                'table'  => static::$table,
            ]);
            throw new DatabaseException("Erreur lors du comptage des enregistrements.");
        }
    }


}