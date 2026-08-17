<?php

namespace App\Interfaces;

interface AdminInterface extends ModelInterface {
    public static function findByEmail(string $email): ?array;
    public static function create(array $data): int;
    public static function updatePassword(int $id, string $passwordHash): bool;
    public static function emailExists(string $email, ?int $ignoreId = null): bool;
}
