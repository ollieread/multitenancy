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
     * Get the primary identifier column name.
     *
     * @return string
     */
    public function getPrimaryIdentifierName();

    /**
     * Get the primary identifier.
     *
     * @return string
     */
    public function getPrimaryIdentifier();

    /**
     * Get the secondary identifier column name.
     *
     * @return string
     */
    public function getSecondaryIdentifierName();

    /**
     * Get the secondary identifier.
     *
     * @return string
     */
    public function getSecondaryIdentifier();
}