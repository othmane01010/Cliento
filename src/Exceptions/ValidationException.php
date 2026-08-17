<?php
namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{

    private array $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
        $firstError = !empty($errors) ? reset($errors) : 'Erreur de validation.';
        parent::__construct($firstError);
    }
    public function getErrors(): array
    {
        return $this->errors;
    }
}

?>