<?php

namespace Ollieread\Multitenancy\Concerns;

/**
 * Interface BelongsToTenant
 *
 * @package Ollieread\Multitenancy\Concerns
 */

trait HasTenant
{
    /**
     * Override the connection name so that we always use the tenant
     * connection.
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    public function getConnectionName()
    {
        return config('multitenancy.multidatabase.connection', '');
    }
}