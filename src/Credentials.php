<?php

namespace Exbil\ResellingAPI;

class Credentials
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct(string $apiKey, string $baseUrl = 'https://reselling-portal.de/api/')
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($baseUrl, '/') . '/';
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function __toString(): string
    {
        return sprintf('[Host: %s], [ApiKey: %s***]', $this->baseUrl, substr($this->apiKey, 0, 8));
    }
}
