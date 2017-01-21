<?php
namespace Ollieread\Multitenancy\Traits;

use Ollieread\Multitenancy\Facades\Multitenancy;

/**
 * Tenant trait
 *
 * @package Ollieread\Multitenancy\Traits
 */
trait Tenant
{

    /**
     * Get the primary identifier column name.
     *
     * @return string
     */
    public function getPrimaryIdentifierName()
    {
        return 'slug';
    }

    /**
     * Get the primary identifier.
     *
     * @return string
     */
    public function getPrimaryIdentifier()
    {
        return $this->{$this->getPrimaryIdentifierName()};
    }

    /**
     * Get the secondary identifier column name.
     *
     * @return string
     */
    public function getSecondaryIdentifierName()
    {
        return 'domain';
    }

    /**
     * Get the secondary identifier.
     *
     * @return string
     */
    public function getSecondaryIdentifier()
    {
        return $this->{$this->getSecondaryIdentifierName()};
    }

    /**
     * Retrieve a route for the current tenant.
     *
     * @param       $name
     * @param array $parameters
     * @param bool  $absolute
     *
     * @return string
     */
    public function route($name, $parameters = [], $absolute = true)
    {
        return Multitenancy::route($name, $parameters, $absolute);
    }

    /**
     * Retrieve a URL for the current tenant.
     *
     * @param null  $path
     * @param array $parameters
     * @param null  $secure
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function url($path = null, $parameters = [], $secure = null)
    {
        return Multitenancy::url($path, $parameters, $secure);
    }
}