<?php

namespace Ollieread\Multitenancy\Providers;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;
use Ollieread\Multitenancy\Contracts\Provider;
use Ollieread\Multitenancy\Contracts\Tenant;
use Ollieread\Multitenancy\GenericTenant;

/**
 * Class Database
 *
 * @package Ollieread\Multitenancy\Providers
 */
class Database implements Provider
{

    protected $connection;

    protected $table;

    protected $domainIdentifier;

    protected $subdomainIdentifier;

    public function __construct(ConnectionInterface $connection, $table, $identifiers)
    {
        $this->connection          = $connection;
        $this->table               = $table;
        $this->domainIdentifier    = $identifiers['domain'];
        $this->subdomainIdentifier = $identifiers['subdomain'];
    }

    protected function getGenericTenant($user)
    {
        if (! is_null($user)) {
            return new GenericTenant((array) $user);
        }
    }

    /**
     * @param string $identifier
     *
     * @return Tenant
     */
    public function retrieveBySubdomainIdentifier(string $identifier): ?Tenant
    {
        $tenant = $this->connection
            ->table($this->table)
            ->where($this->subdomainIdentifier, '=', $identifier)
            ->first();

        return $this->getGenericTenant($tenant);
    }

    /**
     * @param string $identifier
     *
     * @return mixed
     */
    public function retrieveByDomainIdentifier(string $identifier): ?Tenant
    {
        $tenant = $this->connection
            ->table($this->table)
            ->where($this->domainIdentifier, '=', $identifier)
            ->first();

        return $this->getGenericTenant($tenant);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function retrieveAll(): Collection
    {
        return $this->connection
            ->table($this->table)
            ->get();
    }
}