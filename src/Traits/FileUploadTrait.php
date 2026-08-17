<?php

namespace App\Traits;

use App\Exceptions\ValidationException;
use finfo;

trait FileUploadTrait
{
    private static function getUploadDirectory(): string
    {
        return __DIR__ . '/../../public/uploads/clients/';
    }

    public static function uploadImage(array $file): ?string
    {
        if (empty($file['tmp_name']) || (isset($file['error']) && $file['error'] === UPLOAD_ERR_NO_FILE)) {
            return null;
        }

        if ($file['size'] > 2097152) {
            throw new ValidationException(['photo' => "L'image ne doit pas dépasser 2 Mo."]);
        }

        $allowedMimes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']); 

        if (!array_key_exists($mimeType, $allowedMimes)) {
            throw new ValidationException(['photo' => "Format d'image invalide. Formats acceptés : JPG, PNG, WEBP."]);
        }

        $extension = $allowedMimes[$mimeType];
        $uniqueName = bin2hex(random_bytes(16)) . '.' . $extension;

        $uploadDir = self::getUploadDirectory();
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $destination = rtrim($uploadDir, '/') . '/' . $uniqueName;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new ValidationException(['photo' => "Échec du téléchargement de l'image."]);
        }

        return $uniqueName;
    }

    public static function deleteImage(?string $filename): void
    {
        if (empty($filename) || $filename === 'default.png') {
            return;
        }

        $filePath = rtrim(self::getUploadDirectory(), '/') . '/' . basename($filename);

        if (file_exists($filePath) && is_file($filePath)) {
            unlink($filePath);
        }
    }
}