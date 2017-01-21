<?php
namespace Ollieread\Multitenancy\Contracts;

/**
 * Interface Provider
 *
 * @package Ollieread\Multitenancy\Contracts
 */

interface Provider
{

    /**
     * @param      $identifier
     * @param bool $primary
     *
     * @return Tenant
     */
    public function retrieveByIdentifier($identifier, $primary = true);
}