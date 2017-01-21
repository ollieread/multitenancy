<?php
namespace Ollieread\Multitenancy\Providers;

use Illuminate\Database\ConnectionInterface;
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

    protected $primaryIdentifier;

    protected $secondaryIdentifier;

    public function __construct(ConnectionInterface $connection, $table, $identifiers)
    {
        $this->connection = $connection;
        $this->table = $table;
        $this->primaryIdentifier = $identifiers[0];
        $this->secondaryIdentifier = isset($identifiers[1]) ? $identifiers[1] : null;
    }

    public function retrieveById($identifier, $secondaryIdentifier = null)
    {
        $query = $this->connection->table($this->table);
        $query->where($this->primaryIdentifier, '=', $identifier);

        if ($this->secondaryIdentifier) {
            $query->orWhere($this->secondaryIdentifier, '=', $secondaryIdentifier);
        }

        return $this->getGenericTenant($query->first());
    }

    protected function getGenericTenant($user)
    {
        if (! is_null($user)) {
            return new GenericTenant((array) $user);
        }
    }

    /**
     * @param      $identifier
     * @param bool $primary
     *
     * @return Tenant
     */
    public function retrieveByIdentifier($identifier, $primary = true)
    {
        $query = $this->connection->table($this->table);
        $query->where($primary ? $this->primaryIdentifier : $this->secondaryIdentifier, '=', $identifier);

        return $this->getGenericTenant($query->first());
    }
}