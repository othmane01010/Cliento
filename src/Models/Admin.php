<?php

namespace App\Models;

use App\Database\Database;
use App\Interfaces\AdminInterface;
use App\Exceptions\DatabaseException;
use PDOException;

class Admin extends BaseModel implements AdminInterface
{
    protected static string $table = 'admins';

    public static function findByEmail(string $email): ?array
    {
        try {
            $sql = sprintf("SELECT * FROM %s WHERE email = ? LIMIT 1", static::$table);
            $result = Database::query($sql, [$email])->fetch();

            return $result ?: null;
        } catch (PDOException $e) {
            self::logError($e, [
                'method'       => static::class . '::findByEmail',
                'table'        => static::$table,
                'record_email' => $email,
            ]);
            throw new DatabaseException("Erreur lors de la recherche de l'administrateur par email.");
        }
    }

    public static function create(array $data): int
    {
        try {
            $sql = sprintf(
                "INSERT INTO %s (full_name, email, password_hash) VALUES (?, ?, ?) RETURNING id",
                static::$table
            );
            $stmt = Database::query($sql, [
                $data['full_name'],
                $data['email'],
                $data['password_hash'],
            ]);

            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            self::logError($e, [
                'method'      => static::class . '::create',
                'table'       => static::$table,
                'record_data' => $data,
            ]);
            throw new DatabaseException("Erreur lors de la création de l'administrateur.");
        }
    }

    public static function updatePassword(int $id, string $passwordHash): bool
    {
        try{
            $sql = sprintf("UPDATE %s SET password_hash = ? WHERE id = ?", static::$table);
            $stmt = Database::query($sql, [$passwordHash, $id]);

            return $stmt->rowCount() > 0;
        }catch(PDOException $e){
            self::logError($e, [
                'method'    => static::class . '::updatePassword',
                'table'     => static::$table,
                'record_id' => $id,
            ]);
            throw new DatabaseException("Erreur lors de la mise à jour du mot de passe.");
        }
    }

    public static function emailExists(string $email, ?int $ignoreId = null): bool
    {
        try {
            if ($ignoreId !== null) {
                $sql = sprintf("SELECT 1 FROM %s WHERE email = ? AND id != ? LIMIT 1", static::$table);
                $params = [$email, $ignoreId];
            } else {
                $sql = sprintf("SELECT 1 FROM %s WHERE email = ? LIMIT 1", static::$table);
                $params = [$email];
            }

            $stmt = Database::query($sql, $params);
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            self::logError($e, [
                'method'       => static::class . '::emailExists',
                'table'        => static::$table,
                'record_email' => $email,
                'ignore_id'    => $ignoreId,
            ]);
            throw new DatabaseException("Erreur lors de la vérification de l'email.");
        }
    }
}