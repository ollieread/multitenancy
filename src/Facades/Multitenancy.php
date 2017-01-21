<?php
namespace Ollieread\Multitenancy\Facades;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Facade;
use Ollieread\Multitenancy\Contracts\Provider;
use Ollieread\Multitenancy\Contracts\Tenant;

/**
 * Class Multitenancy
 *
 * @method static Tenant tenant() Return the currently authenticated tenant
 * @method static Provider provider() Return the current provider
 * @method static Route routes(\Closure $routes) Provide routes that belong to the tenant restricted part of the app
 * @method static string route($name, $parameters = [], $absolute = true) Generate a route for a tenant restricted route
 * @method static string url($path = null, $parameters = [], $secure = null) Generate a url for a tenant restricted url
 *
 * @package Ollieslab\Multitenancy\Facades
 */
class Multitenancy extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'multitenancy';
    }
}