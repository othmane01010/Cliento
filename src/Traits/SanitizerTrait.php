<?php

namespace App\Traits;

trait SanitizerTrait{
    public static function sanitizeString(?string $value): string
    {
        if ($value === null) {
            return '';
        }
        $trimmed = trim($value);
        $stripped = strip_tags($trimmed);
        return htmlspecialchars($stripped, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

}
