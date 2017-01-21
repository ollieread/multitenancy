<?php
namespace Ollieread\Multitenancy\Middleware;

use Closure;
use Ollieread\Multitenancy\Facades\Multitenancy;

/**
 * Class LoadTenant
 *
 * @package Ollieslab\Multitenancy\Middleware
 */
class LoadTenant
{

    public function handle($request, Closure $next)
    {
        Multitenancy::process($request);

        return $next($request);
    }
}