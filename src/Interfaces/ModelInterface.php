<?php

namespace App\Interfaces;

interface ModelInterface{
    public static function all(): array;
    public static function find(int $id): ?array;
    public static function delete(int $id): bool;
    public static function count(): int;
}
