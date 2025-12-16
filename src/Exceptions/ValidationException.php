<?php

namespace Exbil\CloudApi\Exceptions;

class ValidationException extends ApiException
{
    public function getValidationErrors(): array
    {
        return $this->errorData['errors'] ?? [];
    }
}
