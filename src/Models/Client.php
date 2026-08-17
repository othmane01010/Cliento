<?php

namespace App\Models;

use App\Database\Database;
use App\Interfaces\ClientInterface;
use App\Exceptions\DatabaseException;
use PDOException;

class Client extends BaseModel implements ClientInterface
{
    protected static string $table = 'clients';

public static function create(array $data): int
{
    try {
        $sql = sprintf(
            "INSERT INTO %s (full_name, cin, photo, email, phone) VALUES (?, ?, ?, ?, ?) RETURNING id",
            static::$table
        );

        $stmt = Database::query($sql, [
            $data['full_name'],
            !empty($data['cin']) ? strtoupper(trim($data['cin'])) : null,
            $data['photo'] ?? 'default.png',
            $data['email'] ?? null,
            $data['phone'],
        ]);

        return (int) $stmt->fetchColumn();
    } catch (PDOException $e) {
        self::logError($e, ['method' => static::class . '::create', 'table' => static::$table]);
        throw new DatabaseException("Erreur lors de la création du client.");
    }
}


public static function update(int $id, array $data): bool
{
    try {
        $sql = sprintf(
            "UPDATE %s SET full_name = ?, cin = ?, photo = ?, email = ?, phone = ? WHERE id = ?",
            static::$table
        );

        $stmt = Database::query($sql, [
            $data['full_name'],
            !empty($data['cin']) ? strtoupper(trim($data['cin'])) : null,
            $data['photo'] ?? 'default.png',
            $data['email'] ?? null,
            $data['phone'],
            $id,
        ]);

        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        self::logError($e, ['method' => static::class . '::update', 'table' => static::$table]);
        throw new DatabaseException("Erreur lors de la mise à jour du client.");
    }
}

public static function search(string $term): array
{
    try {
        $searchTerm = '%' . trim($term) . '%';
        $sql = sprintf(
            "SELECT * FROM %s WHERE full_name ILIKE ? OR cin ILIKE ? OR email ILIKE ? OR phone ILIKE ? ORDER BY id DESC",
            static::$table
        );

        return Database::query($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm])->fetchAll();
    } catch (PDOException $e) {
        self::logError($e, ['method' => static::class . '::search', 'table' => static::$table]);
        throw new DatabaseException("Erreur lors de la recherche des clients.");
    }
}

    public static function findByPhone(string $phone): ?array
    {
        try {
            $sql = sprintf("SELECT * FROM %s WHERE phone = ? LIMIT 1", static::$table);
            $result = Database::query($sql, [$phone])->fetch();

            return $result ?: null;
        } catch (PDOException $e) {
            self::logError($e, [
                'method'       => static::class . '::findByPhone',
                'table'        => static::$table,
                'record_phone' => $phone,
            ]);
            throw new DatabaseException("Erreur lors de la recherche du client par téléphone.");
        }
    }

 
    public static function cinExists(string $cin, ?int $ignoreId = null): bool
{
    try {
        $cleanCin = strtoupper(trim($cin));
        if ($ignoreId !== null) {
            $sql = sprintf("SELECT 1 FROM %s WHERE cin = ? AND id != ? LIMIT 1", static::$table);
            $params = [$cleanCin, $ignoreId];
        } else {
            $sql = sprintf("SELECT 1 FROM %s WHERE cin = ? LIMIT 1", static::$table);
            $params = [$cleanCin];
        }

        $stmt = Database::query($sql, $params);
        return (bool) $stmt->fetchColumn();
    } catch (PDOException $e) {
        self::logError($e, [
            'method'    => static::class . '::cinExists',
            'table'     => static::$table,
            'cin'       => $cin,
            'ignore_id' => $ignoreId,
        ]);
        throw new DatabaseException("Erreur lors de la vérification du CIN.");
    }
}

    public static function phoneExists(string $phone, ?int $ignoreId = null): bool
    {
        try {
            if ($ignoreId !== null) {
                $sql = sprintf("SELECT 1 FROM %s WHERE phone = ? AND id != ? LIMIT 1", static::$table);
                $params = [$phone, $ignoreId];
            } else {
                $sql = sprintf("SELECT 1 FROM %s WHERE phone = ? LIMIT 1", static::$table);
                $params = [$phone];
            }

            $stmt = Database::query($sql, $params);
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            self::logError($e, [
                'method'       => static::class . '::phoneExists',
                'table'        => static::$table,
                'record_phone' => $phone,
                'ignore_id'    => $ignoreId,
            ]);
            throw new DatabaseException("Erreur lors de la vérification du numéro de téléphone.");
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
            throw new DatabaseException("Erreur lors de la vérification de l'email du client.");
        }
    }

    public static function getSubscriptions(int $clientId): array
    {
        try {
            $sql = "SELECT s.*, p.name AS plan_name, p.price AS plan_price, p.duration_days AS plan_duration
                    FROM subscriptions s
                    JOIN plans p ON s.plan_id = p.id
                    WHERE s.client_id = ?
                    ORDER BY s.id DESC";

            return Database::query($sql, [$clientId])->fetchAll();
        } catch (PDOException $e) {
            self::logError($e, [
                'method'    => static::class . '::getSubscriptions',
                'table'     => static::$table,
                'client_id' => $clientId,
            ]);
            throw new DatabaseException("Erreur lors de la récupération des abonnements du client.");
        }
    }
}