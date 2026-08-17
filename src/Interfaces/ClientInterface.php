<?php

namespace App\Interfaces;

interface ClientInterface extends ModelInterface {
    public static function create(array $data): int;
    public static function update(int $id, array $data): bool;
    public static function findByPhone(string $phone): ?array;
    public static function search(string $term): array;
    public static function phoneExists(string $phone, ?int $ignoreId = null): bool;
    public static function emailExists(string $email, ?int $ignoreId = null): bool;
    public static function getSubscriptions(int $clientId): array;
}

