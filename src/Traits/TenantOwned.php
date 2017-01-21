<?php
namespace Ollieread\Multitenancy\Traits;

use Ollieread\Multitenancy\Scopes\TenantOwnedScope;

/**
 * Class TenantOwned
 *
 * @package Ollieread\Multitenancy\Traits
 */
trait TenantOwned
{

    public static function bootTenantOwned()
    {
        static::addGlobalScope(new TenantOwnedScope);
    }
}