<?php
namespace Ollieread\Multitenancy\Auth\Provider;

use Illuminate\Auth\DatabaseUserProvider as OriginalProvider;
use Ollieread\Multitenancy\Facades\Multitenancy;

/**
 * Class DatabaseUserProvider
 *
 * @package Ollieslab\Multitenancy\Auth\Provider
 */
class DatabaseUserProvider extends OriginalProvider
{

    /**
     * Get the foreign key for the multitenancy.
     *
     * @return mixed
     */
    protected function getForeignKey()
    {
        return config('multitenancy.foreign_key', 'account_id');
    }

    /**
     * Get the id of the current tenant.
     *
     * @return mixed
     */
    protected function getTenantId()
    {
        return Multitenancy::tenant()->id;
    }

    public function retrieveById($identifier)
    {
        $user = $this->conn->table($this->table)
            ->where('id', $identifier)
            ->where($this->getForeignKey(), $this->getTenantId())
            ->first();

        return $this->getGenericUser($user);
    }

    public function retrieveByToken($identifier, $token)
    {
        $user = $this->conn->table($this->table)
            ->where($this->getForeignKey(), $this->getTenantId())
            ->where('id', $identifier)
            ->where('remember_token', $token)
            ->first();

        return $this->getGenericUser($user);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        $credentials[$this->getForeignKey()] = $this->getTenantId();
        return parent::retrieveByCredentials($credentials);
    }
}