<?php

namespace App\Models;

use App\Database\Database;
use App\Interfaces\PlanInterface;
use App\Exceptions\DatabaseException;
use PDOException;

class Plan extends BaseModel implements PlanInterface
{
    protected static string $table = 'plans';

   public static function create(array $data): int
    {
        try {
            $sql = sprintf(
                "INSERT INTO %s (name, price, duration_days, is_active) VALUES (?, ?, ?, ?) RETURNING id",
                static::$table
            );

           
            $isActive = !empty($data['is_active']) ? 'true' : 'false';

            $stmt = Database::query($sql, [
                $data['name'],
                $data['price'],
                $data['duration_days'],
                $isActive,
            ]);

            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            self::logError($e, [
                'method'      => static::class . '::create',
                'table'       => static::$table,
                'record_data' => $data,
            ]);
            throw new DatabaseException("Erreur lors de la création du plan d'abonnement.");
        }
    }

    public static function update(int $id, array $data): bool
    {
        try {
            $sql = sprintf(
                "UPDATE %s SET name = ?, price = ?, duration_days = ?, is_active = ? WHERE id = ?",
                static::$table
            );

            $isActive = !empty($data['is_active']) ? 'true' : 'false';

            $stmt = Database::query($sql, [
                $data['name'],
                $data['price'],
                $data['duration_days'],
                $isActive,
                $id,
            ]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            self::logError($e, [
                'method'      => static::class . '::update',
                'table'       => static::$table,
                'record_id'   => $id,
                'record_data' => $data,
            ]);
            throw new DatabaseException("Erreur lors de la mise à jour du plan.");
        }
    }

    public static function getActivePlans(): array
    {
        try {
            $sql = sprintf("SELECT * FROM %s WHERE is_active = TRUE ORDER BY price ASC", static::$table);
            return Database::query($sql)->fetchAll();
        } catch (PDOException $e) {
            self::logError($e, [
                'method' => static::class . '::getActivePlans',
                'table'  => static::$table,
            ]);
            throw new DatabaseException("Erreur lors de la récupération des plans actifs.");
        }
    }

    public static function toggleActive(int $id, bool $status): bool
    {
        try {
            $sql = sprintf("UPDATE %s SET is_active = ? WHERE id = ?", static::$table);
            $stmt = Database::query($sql, [$status ? 'true' : 'false', $id]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            self::logError($e, [
                'method'    => static::class . '::toggleActive',
                'table'     => static::$table,
                'record_id' => $id,
                'status'    => $status,
            ]);
            throw new DatabaseException("Erreur lors du changement de statut du plan.");
        }
    }

    public static function hasSubscriptions(int $id): bool
    {
        try {
            $sql = "SELECT 1 FROM subscriptions WHERE plan_id = ? LIMIT 1";
            $stmt = Database::query($sql, [$id]);

            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            self::logError($e, [
                'method'  => static::class . '::hasSubscriptions',
                'table'   => static::$table,
                'plan_id' => $id,
            ]);
            throw new DatabaseException("Erreur lors de la vérification des abonnements liés au plan.");
        }
    }
}