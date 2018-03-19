<?php

namespace Ollieread\Multitenancy\Concerns;

use Doctrine\DBAL\Driver\Connection;
use Ollieread\Multitenancy\Contracts\Tenant;

/**
 * Interface ManagesDatabase
 *
 * @package Ollieread\Multitenancy\Concerns
 */
trait ManagesDatabase
{
    public function setupDatabase(Tenant $tenant)
    {
        $result       = $this->connection()
            ->prepare('CREATE DATABASE :schema')
            ->execute(['schema', $tenant->getDatabaseName()]);

        if ($result) {
            $this->setTenant($tenant)->reconnect();
            $this->migrateDatabase();
        }
    }

    public function migrateDatabase()
    {

    }
}