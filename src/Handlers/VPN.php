<?php

namespace Exbil\CloudApi\Handlers;

use Exbil\CloudApi\Client;
use Exbil\CloudApi\Exceptions\ApiException;
use GuzzleHttp\Exception\GuzzleException;

class VPN
{
    private Client $client;
    private string $basePath = 'v1/products/vpn';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    // ==================== PUBLIC INFORMATION ====================

    /**
     * Get all available VPN servers
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getServers(): array
    {
        return $this->client->get("{$this->basePath}/servers");
    }

    /**
     * Get all available VPN ports
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getPorts(): array
    {
        return $this->client->get("{$this->basePath}/ports");
    }

    /**
     * Get VPN pricing
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getPricing(): array
    {
        return $this->client->get("{$this->basePath}/pricing");
    }

    /**
     * Get GeoIP info for current request
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getGeoIP(): array
    {
        return $this->client->get("{$this->basePath}/geoip");
    }

    // ==================== ACCOUNT MANAGEMENT ====================

    /**
     * Get all VPN accounts
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAccounts(): array
    {
        return $this->client->get("{$this->basePath}/accounts");
    }

    /**
     * Get a specific VPN account
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getAccount(int $id): array
    {
        return $this->client->get("{$this->basePath}/accounts/{$id}");
    }

    /**
     * Create a new VPN account
     *
     * @param string $username Username (3-50 chars, alphanumeric/hyphen/underscore)
     * @param string|null $password Password (6-50 chars, auto-generated if null)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function createAccount(string $username, ?string $password = null): array
    {
        $data = ['username' => $username];
        if ($password !== null) {
            $data['password'] = $password;
        }
        return $this->client->post("{$this->basePath}/accounts", $data);
    }

    /**
     * Delete a VPN account
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function deleteAccount(int $id): array
    {
        return $this->client->delete("{$this->basePath}/accounts/{$id}");
    }

    /**
     * Synchronize account from external API
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function syncAccount(int $id): array
    {
        return $this->client->post("{$this->basePath}/accounts/{$id}/sync");
    }

    // ==================== ACCOUNT OPERATIONS ====================

    /**
     * Enable a VPN account
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function enableAccount(int $id): array
    {
        return $this->client->put("{$this->basePath}/accounts/{$id}/enable");
    }

    /**
     * Disable a VPN account
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function disableAccount(int $id): array
    {
        return $this->client->put("{$this->basePath}/accounts/{$id}/disable");
    }

    /**
     * Change VPN account password
     *
     * @param int $id Account ID
     * @param string $password New password (6-50 chars)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function changePassword(int $id, string $password): array
    {
        return $this->client->put("{$this->basePath}/accounts/{$id}/password", [
            'password' => $password,
        ]);
    }

    // ==================== USERNAME VALIDATION ====================

    /**
     * Check if a username is available
     *
     * @param string $username Username to check (3-50 chars)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function checkUsername(string $username): array
    {
        return $this->client->post("{$this->basePath}/check-username", [
            'username' => $username,
        ]);
    }

    // ==================== CONFIGURATION ====================

    /**
     * Get OpenVPN configuration
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getOpenVpnConfig(int $accountId, int $serverId, int $portId): array
    {
        return $this->client->get("{$this->basePath}/accounts/{$accountId}/config/openvpn", [
            'server_id' => $serverId,
            'port_id' => $portId,
        ]);
    }

    /**
     * Download OpenVPN configuration file (.ovpn)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function downloadOpenVpnConfig(int $accountId, int $serverId, int $portId): array
    {
        return $this->client->get("{$this->basePath}/accounts/{$accountId}/config/openvpn/download", [
            'server_id' => $serverId,
            'port_id' => $portId,
        ]);
    }

    /**
     * Get WireGuard configuration
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function getWireGuardConfig(int $accountId, int $serverId): array
    {
        return $this->client->get("{$this->basePath}/accounts/{$accountId}/config/wireguard", [
            'server_id' => $serverId,
        ]);
    }

    /**
     * Download WireGuard configuration file (.conf)
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    public function downloadWireGuardConfig(int $accountId, int $serverId): array
    {
        return $this->client->get("{$this->basePath}/accounts/{$accountId}/config/wireguard/download", [
            'server_id' => $serverId,
        ]);
    }
}
