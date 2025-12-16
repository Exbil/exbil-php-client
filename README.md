# Exbil Reselling API Client

PHP API Client for the Exbil Reselling Portal.

## Installation

```bash
composer require exbil/reselling-api-client
```

## Quick Start

```php
<?php
require 'vendor/autoload.php';

use Exbil\CloudApi\Client;

$client = new Client('your-api-key', 'https://reselling-portal.de/api/');

// List servers
$servers = $client->rootServer()->getAll();

// Create VPN account
$account = $client->vpn()->createAccount('username', 'password');

// Create Mailcow domain
$domain = $client->mailcow()->createDomain('node-1', [
    'domain' => 'example.com',
    'mailboxes' => 10,
    'aliases' => 50,
    'quota_mb' => 5000,
]);
```

## Authentication

All API requests require an API key transmitted as Bearer Token:

```php
$client = new Client('your-api-key');
```

---

## API Reference

### Accounting

Billing, invoices, credit and usage.

| Method | Description |
|--------|-------------|
| `getUserData()` | Team/user billing information |
| `getCreditStatus()` | Current credit status |
| `getUsage()` | Current month usage summary |
| `getUsageDetails(array $filters)` | Detailed usage records |
| `getInvoices()` | All invoices |
| `getInvoiceById(int $id)` | Single invoice |

```php
// Get credit status
$credit = $client->accounting()->getCreditStatus();

// Get usage with filters
$usage = $client->accounting()->getUsageDetails([
    'start' => '2024-01-01',
    'end' => '2024-01-31',
    'product_type' => 'rootserver',
    'limit' => 100,
]);

// Get invoice
$invoice = $client->accounting()->getInvoiceById(123);
```

---

### Root Server

Manage virtual servers.

#### Locations & Clusters

| Method | Description |
|--------|-------------|
| `getLocations()` | All datacenters |
| `getClustersByDatacenter(string $slug)` | Clusters of a datacenter |
| `getClusters(?string $slug)` | All or single cluster |
| `getOsList(string $clusterSlug)` | Available operating systems |
| `getPriceList(string $clusterSlug)` | Price list of a cluster |
| `calculatePrice(string $clusterSlug, array $config)` | Calculate price |

```php
// Get locations
$locations = $client->rootServer()->getLocations();

// Get OS list for cluster
$osList = $client->rootServer()->getOsList('cluster-de-1');

// Calculate price
$price = $client->rootServer()->calculatePrice('cluster-de-1', [
    'cores' => 4,
    'ram_mb' => 8192,
    'disk_gb' => 100,
    'backup_slots' => 1,
    'ipv4_addresses' => 1,
    'ipv6_addresses' => 1,
]);
```

#### Server Management

| Method | Description |
|--------|-------------|
| `getAll(array $filters)` | All servers (filters: state, datacenter_id, cluster_id) |
| `getById(int $vmId)` | Single server |
| `create(string $clusterSlug, array $config)` | Create server |
| `update(int $vmId, array $config)` | Resize server |
| `delete(int $vmId)` | Delete server |
| `resetRootPassword(int $vmId, ?string $password)` | Reset root password |
| `reinstall(int $vmId, array $config)` | Reinstall server |

```php
// Create server
$server = $client->rootServer()->create('cluster-de-1', [
    'hostname' => 'web-server-01',
    'cores' => 4,
    'ram_mb' => 8192,
    'disk_gb' => 100,
    'operating_system_slug' => 'ubuntu-22.04',
    'root_password' => 'secure-password',
    'ipv4_addresses' => 1,
    'backup_slots' => 1,
]);

// Resize server (disk can only be increased)
$client->rootServer()->update(12345, [
    'cores' => 8,
    'ram_mb' => 16384,
]);

// Delete server
$client->rootServer()->delete(12345);
```

#### Power Control

| Method | Description |
|--------|-------------|
| `start(int $vmId)` | Start server |
| `stop(int $vmId)` | Shutdown server (graceful) |
| `reboot(int $vmId)` | Reboot server |
| `forceStop(int $vmId)` | Power off server (force) |

```php
$client->rootServer()->start(12345);
$client->rootServer()->stop(12345);
$client->rootServer()->reboot(12345);
$client->rootServer()->forceStop(12345);
```

#### Monitoring

| Method | Description |
|--------|-------------|
| `getStats(int $vmId)` | Live statistics (CPU, RAM, network) |
| `getLogs(int $vmId, int $limit)` | Server logs |
| `getTasks(int $vmId, int $limit)` | Running/completed tasks |

```php
$stats = $client->rootServer()->getStats(12345);
$logs = $client->rootServer()->getLogs(12345, 100);
$tasks = $client->rootServer()->getTasks(12345);
```

---

### VPN

VPN accounts and configurations.

#### Information

| Method | Description |
|--------|-------------|
| `getServers()` | All VPN servers |
| `getPorts()` | Available ports |
| `getPricing()` | Pricing |
| `getGeoIP()` | GeoIP info of current request |

```php
$servers = $client->vpn()->getServers();
$pricing = $client->vpn()->getPricing();
```

#### Account Management

| Method | Description |
|--------|-------------|
| `getAccounts()` | All VPN accounts |
| `getAccount(int $id)` | Single account |
| `createAccount(string $username, ?string $password)` | Create account |
| `deleteAccount(int $id)` | Delete account |
| `syncAccount(int $id)` | Sync account |
| `enableAccount(int $id)` | Enable account |
| `disableAccount(int $id)` | Disable account |
| `changePassword(int $id, string $password)` | Change password |
| `checkUsername(string $username)` | Check username availability |

```php
// Check username
$available = $client->vpn()->checkUsername('new-user');

// Create account
$account = $client->vpn()->createAccount('new-user', 'secure-password');

// Enable/disable account
$client->vpn()->enableAccount(123);
$client->vpn()->disableAccount(123);

// Change password
$client->vpn()->changePassword(123, 'new-password');
```

#### Configurations

| Method | Description |
|--------|-------------|
| `getOpenVpnConfig(int $accountId, int $serverId, int $portId)` | OpenVPN config (JSON) |
| `downloadOpenVpnConfig(int $accountId, int $serverId, int $portId)` | OpenVPN .ovpn download |
| `getWireGuardConfig(int $accountId, int $serverId)` | WireGuard config (JSON) |
| `downloadWireGuardConfig(int $accountId, int $serverId)` | WireGuard .conf download |

```php
// OpenVPN configuration
$ovpnConfig = $client->vpn()->getOpenVpnConfig(123, 1, 443);

// WireGuard configuration
$wgConfig = $client->vpn()->getWireGuardConfig(123, 1);
```

---

### Mailcow

Email domains, mailboxes and aliases.

#### Infrastructure

| Method | Description |
|--------|-------------|
| `getNodes(?string $datacenter)` | All Mailcow nodes |
| `getLoadBalancerStats(?string $datacenter)` | Load balancer statistics |
| `calculatePrice(string $nodeOrDc, int $mailboxes, int $aliases, int $quotaMb)` | Calculate price |

```php
$nodes = $client->mailcow()->getNodes();
$price = $client->mailcow()->calculatePrice('node-1', 10, 50, 5000);
```

#### Domain Management

| Method | Description |
|--------|-------------|
| `getDomains(?string $id)` | All or single domain |
| `createDomain(string $nodeOrDc, array $config)` | Create domain |
| `updateDomain(string $id, array $config)` | Update domain |
| `deleteDomain(string $id)` | Delete domain |

```php
// Create domain
$domain = $client->mailcow()->createDomain('node-1', [
    'domain' => 'example.com',
    'mailboxes' => 10,
    'aliases' => 50,
    'quota_mb' => 5000,
    'defquota_mb' => 500,
    'maxquota_mb' => 1000,
    'admin_username' => 'admin',
    'admin_password' => 'secure-password',
]);

// Create multiple domains
$domains = $client->mailcow()->createDomain('node-1', [
    'domains' => ['example.com', 'example.org'],
    'mailboxes' => 10,
    'aliases' => 50,
    'quota_mb' => 5000,
]);

// Update domain
$client->mailcow()->updateDomain('example.com', [
    'mailboxes' => 20,
    'quota_mb' => 10000,
]);
```

#### Domain Admins

| Method | Description |
|--------|-------------|
| `getDomainAdmins(string $domain, ?int $id)` | Get domain admins |
| `createDomainAdmin(string $domain, string $username, ?string $password)` | Create admin |
| `updateDomainAdmin(string $domain, string $username, array $config)` | Update admin |
| `deleteDomainAdmin(string $domain, string $username)` | Delete admin |

```php
// Create admin
$admin = $client->mailcow()->createDomainAdmin('example.com', 'admin', 'secure-password');

// Update admin
$client->mailcow()->updateDomainAdmin('example.com', 'admin', [
    'password' => 'new-password',
    'active' => true,
]);
```

#### Mailboxes

| Method | Description |
|--------|-------------|
| `getMailboxes(string $domain, ?int $id)` | Get mailboxes |
| `createMailbox(string $domain, string $address, array $config)` | Create mailbox |
| `updateMailbox(string $domain, string $address, array $config)` | Update mailbox |
| `deleteMailbox(string $domain, string $localPart)` | Delete mailbox |

```php
// Create mailbox
$mailbox = $client->mailcow()->createMailbox('example.com', 'info', [
    'password' => 'secure-password',
    'name' => 'Info Mailbox',
    'quota_mb' => 500,
]);

// Update mailbox
$client->mailcow()->updateMailbox('example.com', 'info', [
    'quota_mb' => 1000,
    'active' => true,
]);

// Delete mailbox
$client->mailcow()->deleteMailbox('example.com', 'info');
```

#### Aliases

| Method | Description |
|--------|-------------|
| `getAliases(string $domain, ?int $id)` | Get aliases |
| `createAlias(string $domain, string $address, array $goto)` | Create alias |
| `updateAlias(string $domain, string $address, array $goto, ?bool $active)` | Update alias |
| `deleteAlias(string $domain, string $localPart)` | Delete alias |

```php
// Create alias
$alias = $client->mailcow()->createAlias('example.com', 'support', [
    'info@example.com',
    'admin@example.com',
]);

// Update alias
$client->mailcow()->updateAlias('example.com', 'support', [
    'info@example.com',
], true);

// Delete alias
$client->mailcow()->deleteAlias('example.com', 'support');
```

---

## Error Handling

```php
use Exbil\CloudApi\Exceptions\ApiException;
use Exbil\CloudApi\Exceptions\AuthenticationException;
use Exbil\CloudApi\Exceptions\ForbiddenException;
use Exbil\CloudApi\Exceptions\NotFoundException;
use Exbil\CloudApi\Exceptions\ValidationException;

try {
    $server = $client->rootServer()->getById(99999);
} catch (AuthenticationException $e) {
    // 401 - Invalid API key
    echo "Authentication failed: " . $e->getMessage();
} catch (ForbiddenException $e) {
    // 403 - No permission
    echo "Access denied: " . $e->getMessage();
} catch (NotFoundException $e) {
    // 404 - Resource not found
    echo "Not found: " . $e->getMessage();
} catch (ValidationException $e) {
    // 422 - Validation error
    echo "Validation error: " . $e->getMessage();
    print_r($e->getValidationErrors());
} catch (ApiException $e) {
    // Other API errors
    echo "API error: " . $e->getMessage();
    echo "Status code: " . $e->getCode();
}
```

---

## Asynchronous Operations

Many operations are executed asynchronously and return a 202 status:

- Server create/delete/resize
- Power operations (start, stop, reboot)
- Mailcow domain/mailbox/alias create/update/delete
- VPN account create/enable/disable

The response typically contains a job ID or task information for tracking.

---

## License

BSD-2-Clause
