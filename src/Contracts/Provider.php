<?php

namespace Ollieread\Multitenancy\Contracts;

use Illuminate\Support\Collection;

/**
 * Interface Provider
 *
 * @package Ollieread\Multitenancy\Contracts
 */

interface Provider
{

    /**
     * @param string $identifier
     *
     * @return Tenant
     */
    public function retrieveBySubdomainIdentifier(string $identifier): ?Tenant;

    /**
     * @param string $identifier
     *
     * @return mixed
     */
    public function retrieveByDomainIdentifier(string $identifier): ?Tenant;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function retrieveAll(): Collection;
}