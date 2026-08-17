<?php

namespace App\Interfaces;

interface PlanInterface extends ModelInterface{
    public static function create(array $data): int;
    public static function update(int $id, array $data): bool;
    public static function getActivePlans(): array;
    public static function toggleActive(int $id, bool $status): bool;
    public static function hasSubscriptions(int $id): bool;
}
