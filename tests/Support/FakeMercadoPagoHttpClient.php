<?php

namespace Tests\Support;

use MercadoPago\Net\MPHttpClient;
use MercadoPago\Net\MPRequest;
use MercadoPago\Net\MPResponse;

class FakeMercadoPagoHttpClient implements MPHttpClient
{
    /** @var array<int, MPResponse|\Throwable> */
    private array $responses = [];

    /** @var array<int, MPRequest> */
    public array $requests = [];

    public function push(MPResponse|\Throwable $response): static
    {
        $this->responses[] = $response;

        return $this;
    }

    public function send(MPRequest $request): MPResponse
    {
        $this->requests[] = $request;

        $response = array_shift($this->responses);

        if ($response instanceof \Throwable) {
            throw $response;
        }

        return $response ?? new MPResponse(200, []);
    }
}
