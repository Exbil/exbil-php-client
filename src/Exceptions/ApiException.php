<?php

namespace Exbil\ResellingAPI\Exceptions;

use Exception;
use Psr\Http\Message\ResponseInterface;

class ApiException extends Exception
{
    protected ?ResponseInterface $response;
    protected array $errorData;

    public function __construct(
        string $message = '',
        int $code = 0,
        ?ResponseInterface $response = null,
        array $errorData = [],
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
        $this->errorData = $errorData;
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    public function getErrorData(): array
    {
        return $this->errorData;
    }
}
