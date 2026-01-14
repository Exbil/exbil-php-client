<?php

namespace Exbil\ResellingAPI\Exceptions;

class ValidationException extends ApiException
{
    public function getValidationErrors(): array
    {
        return $this->errorData['errors'] ?? [];
    }
}
