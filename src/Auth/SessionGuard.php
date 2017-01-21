<?php
namespace Ollieread\Multitenancy\Auth\Guard;

use Illuminate\Auth\SessionGuard as OriginalGuard;
use Ollieread\Multitenancy\Facades\Multitenancy;

/**
 * Class SessionGuard
 *
 * @package Ollieslab\Multitenancy\Auth
 */
class SessionGuard extends OriginalGuard
{

    /**
     * Retrieve the tenant identifier.
     * This is used for setting the name and recaller name.
     *
     * @return null|string
     */
    protected function getTenantName()
    {
        return str_slug(Multitenancy::tenant()->getPrimaryIdentifier()) . '_';
    }

    /**
     * Get a unique identifier for the auth session value.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getTenantName() . parent::getName();
    }

    /**
     * Get the name of the cookie used to store the "recaller".
     *
     * @return string
     */
    public function getRecallerName()
    {
        return $this->getTenantName() . parent::getRecallerName();
    }
}