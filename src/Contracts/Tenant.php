<?php

namespace Ollieread\Multitenancy\Contracts;

/**
 * Interface Tenant
 *
 * @package Ollieread\Multitenancy\Contracts
 */

interface Tenant
{

    /**
     * Get the subdomain identifier column name.
     *
     * @return string
     */
    public function getSubdomainIdentifierName();

    /**
     * Get the subdomain identifier.
     *
     * @return string
     */
    public function getSubdomainIdentifier();

    /**
     * Get the domain identifier column name.
     *
     * @return string
     */
    public function getDomainIdentifierName();

    /**
     * Get the domain identifier.
     *
     * @return string
     */
    public function getDomainIdentifier();
}