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
    public function getSubdomainIdentifierName(): string;

    /**
     * Get the subdomain identifier.
     *
     * @return string
     */
    public function getSubdomainIdentifier(): string;

    /**
     * Get the domain identifier column name.
     *
     * @return string
     */
    public function getDomainIdentifierName(): string;

    /**
     * Get the domain identifier.
     *
     * @return string
     */
    public function getDomainIdentifier(): string;

    /**
     * @return string
     */
    public function getDatabaseName(): string;
}