<?php

namespace App\Interfaces;

interface SubscriptionInterface extends ModelInterface
{
    public static function create(array $data): int;
    public static function getAllWithDetails(?string $status = null): array;
    public static function getDetailsById(int $id): ?array;
    public static function getExpiringSoon(int $days = 3): array;
    public static function updateExpiredStatuses(): int;
    public static function cancel(int $id): bool;
    public static function hasActiveSubscription(int $clientId): bool;
    public static function countActive(): int;
    public static function getTotalRevenue(): float;
}