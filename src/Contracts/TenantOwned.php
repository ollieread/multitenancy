<?php
namespace Ollieread\Multitenancy\Contracts;

/**
 * Interface TenantOwned
 *
 * @package Ollieread\Multitenancy\Contracts
 */

interface TenantOwned
{

    public function getTenantKey();
}