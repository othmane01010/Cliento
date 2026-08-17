<?php

namespace App\Traits;

use App\Exceptions\ValidationException;
use DateTimeImmutable;

trait ValidationTrait
{
    use LoggerTrait;



    public static function validateRequired(array $data, array $fields): array
    {
        $errors = [];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim((string) $data[$field]) === '') {
                $errors[$field] = "Le champ [{$field}] est obligatoire.";
            }
        }
        return $errors;
    }


    public static function validateName(?string $name): ?string
    {
        if (empty($name)) {
            return null;
        }

        $trimmed = trim($name);
        if (mb_strlen($trimmed) < 2) {
            return "Le nom complet doit contenir au moins 2 caractères.";
        }

        if (mb_strlen($trimmed) > 100) {
            return "Le nom complet ne peut pas dépasser 100 caractères.";
        }

        return null;
    }
    
    public static function validateEmail(?string $email): ?string
    {
        if (empty($email)) {
            return null;
        }
        return filter_var(trim($email), FILTER_VALIDATE_EMAIL) ? null : "Format de l'email invalide.";
    }

    public static function validateMoroccanPhone(string $phone): ?string
    {
        $clean = preg_replace('/[\s\-\.]/', '', trim($phone));
        return preg_match('/^(?:(?:\+|00)212|0)[5-7]\d{8}$/', $clean) ? null : "Numéro de téléphone marocain invalide.";
    }

    public static function validatePrice(mixed $price): ?string
    {
        return (is_numeric($price) && (float) $price >= 0) ? null : "Le prix doit être un nombre positif.";
    }

    public static function validateDuration(mixed $days): ?string
    {
        return (filter_var($days, FILTER_VALIDATE_INT) && (int) $days > 0) ? null : "La durée doit être un nombre de jours supérieur à 0.";
    }

    public static function validateDateRange(string $startDate, string $endDate): ?string
    {
        $start = DateTimeImmutable::createFromFormat('Y-m-d', trim($startDate));
        $end   = DateTimeImmutable::createFromFormat('Y-m-d', trim($endDate));

        if (!$start || !$end) {
            return "Format de date invalide (AAAA-MM-JJ requis).";
        }
        return ($end >= $start) ? null : "La date de fin doit être postérieure à la date de début.";
    }

    public static function checkValidation(array $errors): void
    {
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}