<?php

namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends \DomainException
{
    private ConstraintViolationListInterface $errors;

    public function __construct(ConstraintViolationListInterface $errors, string $message = '')
    {
        $this->errors = $errors;
        parent::__construct($message);
    }

    public function getErrors(): ConstraintViolationListInterface
    {
        return $this->errors;
    }
}
