<?php

namespace Exbil\CloudApi\Handlers;

use Exbil\CloudApi\Client;
use Exbil\CloudApi\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Accounting
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get team/user billing information
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getUserData(): array
    {
        return $this->client->get('v1/accounting/user-data');
    }

    /**
     * Get credit status information
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getCreditStatus(): array
    {
        return $this->client->get('v1/accounting/credit-status');
    }

    /**
     * Get current month usage summary
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getUsage(): array
    {
        return $this->client->get('v1/accounting/usage');
    }

    /**
     * Get detailed usage records with filtering
     *
     * @param array $filters Available filters:
     *   - start: ISO date string (default: start of month)
     *   - end: ISO date string (default: now)
     *   - product_type: string filter
     *   - product_id: integer filter
     *   - limit: integer (max 500, default 100)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getUsageDetails(array $filters = []): array
    {
        return $this->client->get('v1/accounting/usage/details', $filters);
    }

    /**
     * Get all invoices
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getInvoices(): array
    {
        return $this->client->get('v1/accounting/invoices');
    }

    /**
     * Get invoice by ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getInvoiceById(int $id): array
    {
        return $this->client->get("v1/accounting/invoices/{$id}");
    }
}
