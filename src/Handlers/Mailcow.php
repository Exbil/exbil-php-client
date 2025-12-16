<?php

namespace Exbil\CloudApi\Handlers;

use Exbil\CloudApi\Client;
use Exbil\CloudApi\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class Mailcow
{
    private Client $client;
    private string $basePath = 'v1/products/mailcow';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    // ==================== INFRASTRUCTURE ====================

    /**
     * Get all active Mailcow nodes
     *
     * @param string|null $datacenter Optional datacenter slug filter
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getNodes(?string $datacenter = null): array
    {
        $query = [];
        if ($datacenter !== null) {
            $query['datacenter'] = $datacenter;
        }
        return $this->client->get("{$this->basePath}/nodes", $query);
    }

    /**
     * Get load balancer statistics for nodes
     *
     * @param string|null $datacenter Optional datacenter slug filter
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getLoadBalancerStats(?string $datacenter = null): array
    {
        $query = [];
        if ($datacenter !== null) {
            $query['datacenter'] = $datacenter;
        }
        return $this->client->get("{$this->basePath}/load-balancer/stats", $query);
    }

    /**
     * Calculate pricing for mailbox resources
     *
     * @param string $nodeOrDatacenter Node ID/slug or datacenter ID/slug
     * @param int $mailboxes Number of mailboxes
     * @param int $aliases Number of aliases
     * @param int $quotaMb Quota in MB
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function calculatePrice(string $nodeOrDatacenter, int $mailboxes, int $aliases, int $quotaMb): array
    {
        return $this->client->post("{$this->basePath}/{$nodeOrDatacenter}/calculate", [
            'mailboxes' => $mailboxes,
            'aliases' => $aliases,
            'quota_mb' => $quotaMb,
        ]);
    }

    // ==================== DOMAIN MANAGEMENT ====================

    /**
     * Get all domains or a specific domain
     *
     * @param string|int|null $id Domain name or ID (optional)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getDomains(string|int|null $id = null): array
    {
        $path = "{$this->basePath}/domains";
        if ($id !== null) {
            $path .= "/{$id}";
        }
        return $this->client->get($path);
    }

    /**
     * Create one or multiple Mailcow domains
     *
     * @param string $nodeOrDatacenter Node ID/slug or datacenter ID/slug
     * @param array $config Domain configuration:
     *   - domain: string (single domain) OR domains: array (multiple)
     *   - mailboxes: int
     *   - aliases: int
     *   - quota_mb: int
     *   - defquota_mb: int (default quota per mailbox)
     *   - maxquota_mb: int (max quota per mailbox)
     *   - backupmx: int (0 or 1)
     *   - admin_username: string
     *   - admin_password: string
     *   - permissions: array
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function createDomain(string $nodeOrDatacenter, array $config): array
    {
        return $this->client->post("{$this->basePath}/{$nodeOrDatacenter}/create", $config);
    }

    /**
     * Update domain configuration
     *
     * @param string|int $id Domain name or ID
     * @param array $config Update options:
     *   - active: int (0 or 1)
     *   - aliases: int
     *   - mailboxes: int
     *   - defquota_mb: int
     *   - maxquota_mb: int
     *   - quota_mb: int
     *   - backupmx: int (0 or 1)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function updateDomain(string|int $id, array $config): array
    {
        return $this->client->put("{$this->basePath}/domains/{$id}", $config);
    }

    /**
     * Delete a domain
     *
     * @param string|int $id Domain name or ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function deleteDomain(string|int $id): array
    {
        return $this->client->delete("{$this->basePath}/domains/{$id}");
    }

    // ==================== DOMAIN ADMINS ====================

    /**
     * Get domain admins
     *
     * @param string $domain Domain name
     * @param int|null $adminId Optional admin ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getDomainAdmins(string $domain, ?int $adminId = null): array
    {
        $path = "{$this->basePath}/domain-admin/{$domain}";
        if ($adminId !== null) {
            $path .= "/{$adminId}";
        }
        return $this->client->get($path);
    }

    /**
     * Create a domain admin
     *
     * @param string $domain Domain name
     * @param string $username Admin username
     * @param string|null $password Password (min 12 chars, auto-generated if null)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function createDomainAdmin(string $domain, string $username, ?string $password = null): array
    {
        $data = ['username' => $username];
        if ($password !== null) {
            $data['password'] = $password;
        }
        return $this->client->post("{$this->basePath}/domain-admin/{$domain}", $data);
    }

    /**
     * Update a domain admin
     *
     * @param string $domain Domain name
     * @param string $username Current username
     * @param array $config Update options:
     *   - username_new: string (optional)
     *   - password: string (min 12 chars, optional)
     *   - active: boolean (optional)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function updateDomainAdmin(string $domain, string $username, array $config = []): array
    {
        $config['username'] = $username;
        return $this->client->put("{$this->basePath}/domain-admin/{$domain}", $config);
    }

    /**
     * Delete a domain admin
     *
     * @param string $domain Domain name
     * @param string $username Admin username
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function deleteDomainAdmin(string $domain, string $username): array
    {
        return $this->client->delete("{$this->basePath}/domain-admin/{$domain}", [
            'username' => $username,
        ]);
    }

    // ==================== MAILBOXES ====================

    /**
     * Get mailboxes for a domain
     *
     * @param string $domain Domain name
     * @param int|null $mailboxId Optional mailbox ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getMailboxes(string $domain, ?int $mailboxId = null): array
    {
        $path = "{$this->basePath}/{$domain}/mailboxes";
        if ($mailboxId !== null) {
            $path .= "/{$mailboxId}";
        }
        return $this->client->get($path);
    }

    /**
     * Create a mailbox
     *
     * @param string $domain Domain name
     * @param string $address Local part or full email address
     * @param array $config Mailbox configuration:
     *   - password: string (optional)
     *   - name: string (optional)
     *   - quota_mb: int (optional)
     *   - active: boolean (optional)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function createMailbox(string $domain, string $address, array $config = []): array
    {
        $config['address'] = $address;
        return $this->client->post("{$this->basePath}/{$domain}/mailboxes", $config);
    }

    /**
     * Update a mailbox
     *
     * @param string $domain Domain name
     * @param string $address Local part or full email address
     * @param array $config Update options:
     *   - password: string (min 12 chars, optional)
     *   - name: string (optional)
     *   - quota_mb: int (optional)
     *   - active: boolean (optional)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function updateMailbox(string $domain, string $address, array $config = []): array
    {
        $config['address'] = $address;
        return $this->client->put("{$this->basePath}/{$domain}/mailboxes", $config);
    }

    /**
     * Delete a mailbox
     *
     * @param string $domain Domain name
     * @param string $localPart Local part of the mailbox
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function deleteMailbox(string $domain, string $localPart): array
    {
        return $this->client->delete("{$this->basePath}/{$domain}/mailboxes/{$localPart}");
    }

    // ==================== ALIASES ====================

    /**
     * Get aliases for a domain
     *
     * @param string $domain Domain name
     * @param int|null $aliasId Optional alias ID
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAliases(string $domain, ?int $aliasId = null): array
    {
        $path = "{$this->basePath}/{$domain}/aliases";
        if ($aliasId !== null) {
            $path .= "/{$aliasId}";
        }
        return $this->client->get($path);
    }

    /**
     * Create an alias
     *
     * @param string $domain Domain name
     * @param string $address Local part or full email address
     * @param array $goto Destination email addresses
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function createAlias(string $domain, string $address, array $goto): array
    {
        return $this->client->post("{$this->basePath}/{$domain}/aliases", [
            'address' => $address,
            'goto' => $goto,
        ]);
    }

    /**
     * Update an alias
     *
     * @param string $domain Domain name
     * @param string $address Local part or full email address
     * @param array $goto Destination email addresses
     * @param bool|null $active Active status (optional)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function updateAlias(string $domain, string $address, array $goto, ?bool $active = null): array
    {
        $data = [
            'address' => $address,
            'goto' => $goto,
        ];
        if ($active !== null) {
            $data['active'] = $active;
        }
        return $this->client->put("{$this->basePath}/{$domain}/aliases", $data);
    }

    /**
     * Delete an alias
     *
     * @param string $domain Domain name
     * @param string $localPart Local part of the alias
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function deleteAlias(string $domain, string $localPart): array
    {
        return $this->client->delete("{$this->basePath}/{$domain}/aliases/{$localPart}");
    }
}
