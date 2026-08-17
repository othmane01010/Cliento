<?php

namespace App\Models;

use App\Database\Database;
use App\Interfaces\SubscriptionInterface;
use App\Exceptions\DatabaseException;
use PDOException;

class Subscription extends BaseModel implements SubscriptionInterface
{
    protected static string $table = 'subscriptions';

    public static function create(array $data): int
    {
        try {
            $sql = sprintf(
                "INSERT INTO %s (client_id, plan_id, start_date, end_date, status) VALUES (?, ?, ?, ?, ?) RETURNING id",
                static::$table
            );

            $stmt = Database::query($sql, [
                $data['client_id'],
                $data['plan_id'],
                $data['start_date'],
                $data['end_date'],
                $data['status'] ?? 'ACTIVE',
            ]);

            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            self::logError($e, [
                'method'      => static::class . '::create',
                'table'       => static::$table,
                'record_data' => $data,
            ]);
            throw new DatabaseException("Erreur lors de la création de l'abonnement.");
        }
    }

    public static function getAllWithDetails(?string $status = null): array
    {
        try {
            $sql = "SELECT s.*, 
                           c.full_name AS client_name, 
                           c.phone AS client_phone, 
                           c.photo AS client_photo,
                           p.name AS plan_name, 
                           p.price AS plan_price, 
                           p.duration_days AS plan_duration
                    FROM subscriptions s
                    JOIN clients c ON s.client_id = c.id
                    JOIN plans p ON s.plan_id = p.id";

            $params = [];
            if ($status !== null) {
                $sql .= " WHERE s.status = ?";
                $params[] = $status;
            }

            $sql .= " ORDER BY s.id DESC";

            return Database::query($sql, $params)->fetchAll();
        } catch (PDOException $e) {
            self::logError($e, [
                'method' => static::class . '::getAllWithDetails',
                'table'  => static::$table,
                'status' => $status,
            ]);
            throw new DatabaseException("Erreur lors de la récupération des abonnements détaillés.");
        }
    }

  public static function getDetailsById(int $id): ?array
{
    try {
        $sql = "SELECT s.*, 
                       c.full_name AS client_name, 
                       c.cin AS client_cin, 
                       c.phone AS client_phone, 
                       c.email AS client_email, 
                       c.photo AS client_photo, 
                       p.name AS plan_name, 
                       p.price AS plan_price, 
                       p.duration_days AS plan_duration
                FROM subscriptions s
                JOIN clients c ON s.client_id = c.id
                JOIN plans p ON s.plan_id = p.id
                WHERE s.id = ?
                LIMIT 1";

        $result = Database::query($sql, [$id])->fetch();
        return $result ?: null;
    } catch (PDOException $e) {
        self::logError($e, [
            'method'    => static::class . '::getDetailsById',
            'record_id' => $id,
        ]);
        throw new DatabaseException("Erreur lors de la récupération des détails du reçu.");
    }
}

    public static function getExpiringSoon(int $days = 3): array
    {
        try {
            $sql = "SELECT s.*, 
                           c.full_name AS client_name, 
                           c.phone AS client_phone, 
                           p.name AS plan_name
                    FROM subscriptions s
                    JOIN clients c ON s.client_id = c.id
                    JOIN plans p ON s.plan_id = p.id
                    WHERE s.status = 'ACTIVE' 
                      AND s.end_date >= CURRENT_DATE 
                      AND s.end_date <= (CURRENT_DATE + (? || ' days')::INTERVAL)
                    ORDER BY s.end_date ASC";

            return Database::query($sql, [$days])->fetchAll();
        } catch (PDOException $e) {
            self::logError($e, [
                'method' => static::class . '::getExpiringSoon',
                'table'  => static::$table,
                'days'   => $days,
            ]);
            throw new DatabaseException("Erreur lors de la récupération des abonnements expirant bientôt.");
        }
    }

    public static function updateExpiredStatuses(): int
    {
        try {
            $sql = "UPDATE subscriptions 
                    SET status = 'EXPIRED' 
                    WHERE end_date < CURRENT_DATE 
                      AND status NOT IN ('EXPIRED', 'CANCELLED')";

            $stmt = Database::query($sql);

            return $stmt->rowCount();
        } catch (PDOException $e) {
            self::logError($e, [
                'method' => static::class . '::updateExpiredStatuses',
                'table'  => static::$table,
            ]);
            throw new DatabaseException("Erreur lors de la mise à jour automatique des abonnements expirés.");
        }
    }

    public static function cancel(int $id): bool
    {
        try {
            $sql = sprintf("UPDATE %s SET status = 'CANCELLED' WHERE id = ?", static::$table);
            $stmt = Database::query($sql, [$id]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            self::logError($e, [
                'method'    => static::class . '::cancel',
                'table'     => static::$table,
                'record_id' => $id,
            ]);
            throw new DatabaseException("Erreur lors de l'annulation de l'abonnement.");
        }
    }

    public static function hasActiveSubscription(int $clientId): bool
    {
        try {
            $sql = "SELECT 1 FROM subscriptions 
                    WHERE client_id = ? 
                      AND status IN ('ACTIVE', 'EXPIRING_SOON') 
                      AND end_date >= CURRENT_DATE 
                    LIMIT 1";

            $stmt = Database::query($sql, [$clientId]);

            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            self::logError($e, [
                'method'    => static::class . '::hasActiveSubscription',
                'table'     => static::$table,
                'client_id' => $clientId,
            ]);
            throw new DatabaseException("Erreur lors de la vérification de l'abonnement actif.");
        }
    }

    public static function countActive(): int
    {
        try {
            $sql = sprintf("SELECT COUNT(*) FROM %s WHERE status = 'ACTIVE'", static::$table);
            return (int) Database::query($sql)->fetchColumn();
        } catch (PDOException $e) {
            self::logError($e, ['method' => static::class . '::countActive', 'table' => static::$table]);
            throw new DatabaseException("Erreur lors du calcul des abonnements actifs.");
        }
    }

 
    public static function getTotalRevenue(): float
    {
        try {
            $sql = "SELECT COALESCE(SUM(p.price), 0) 
                    FROM subscriptions s 
                    JOIN plans p ON s.plan_id = p.id 
                    WHERE s.status IN ('ACTIVE', 'EXPIRING_SOON')";

            return (float) Database::query($sql)->fetchColumn();
        } catch (PDOException $e) {
            self::logError($e, ['method' => static::class . '::getTotalRevenue', 'table' => static::$table]);
            throw new DatabaseException("Erreur lors du calcul des revenus.");
        }
    }
}