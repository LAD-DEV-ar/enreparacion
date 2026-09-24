<?php

namespace App\Exceptions;

use Exception;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\Net\MPResponse;

class MercadoPagoException extends Exception
{
    public function __construct(
        string $message,
        private readonly ?int $statusCode = null,
        private readonly ?MPResponse $response = null,
    ) {
        parent::__construct($message);
    }

    public static function fromApi(MPApiException $e): self
    {
        return new self(
            $e->getMessage(),
            $e->getStatusCode(),
            $e->getApiResponse(),
        );
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function getResponse(): ?MPResponse
    {
        return $this->response;
    }
}
