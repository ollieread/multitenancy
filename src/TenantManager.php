<?php
namespace Ollieread\Multitenancy;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Ollieread\Multitenancy\Contracts\Provider;
use Ollieread\Multitenancy\Contracts\Tenant;
use Ollieread\Multitenancy\Exceptions\InvalidTenantException;
use Ollieread\Multitenancy\Middleware\LoadTenant;

/**
 * Class Manager
 *
 * @package Ollieread\Multitenancy
 */
class TenantManager
{

    /**
     * An instance of the application.
     * @var Application
     */
    protected $app;

    /**
     * The configuration for the package.
     * @var array
     */
    protected $config;

    /**
     * Array of providers.
     * @var array
     */
    protected $providers = [];

    /**
     * @var Provider
     */
    protected $provider;

    /**
     * @var Tenant
     */
    protected $tenant;

    /**
     * @var boolean
     */
    protected $primary = false;

    public function __construct($app)
    {
        $this->app = $app;
        $this->config = $app['config']['multitenancy'];
    }

    /**
     * Pass any method calls not found through to the provider.
     *
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->provider()->{$method}(...$parameters);
    }

    /**
     * Retrieve or create the current provider.
     *
     * @return \Ollieread\Multitenancy\Contracts\Provider
     */
    public function provider()
    {
        return $this->provider ? $this->provider : $this->provider = $this->resolve();
    }

    /**
     * Resolve to a provider.
     *
     * @return \Ollieread\Multitenancy\Contracts\Provider
     */
    public function resolve()
    {
        return $this->createProvider();
    }

    /**
     * Extend the manager with a new provider.
     *
     * @param          $name
     * @param \Closure $callback
     *
     * @return $this
     */
    public function extend($name, \Closure $callback)
    {
        $this->providers[$name] = $callback;
        return $this;
    }

    /**
     * Create an instance of the provider.
     *
     * @return Provider
     */
    protected function createProvider()
    {
        if (isset($this->providers[$this->config['provider']])) {
            return call_user_func(
                $this->providers[$this->config['provider']],
                $this->app,
                $this->getProviderConfig($this->config['provider'])
            );
        }

        throw new \RuntimeException('Invalid provider: '.$this->config['provider']);
    }

    /**
     * Retrieve the config for the specified provider.
     *
     * @param $provider
     *
     * @return array
     */
    protected function getProviderConfig($provider)
    {
        if (isset($this->config[$provider])) {
            return $this->config[$provider];
        }

        return [];
    }

    /**
     * Whether or not we currently have a tenant.
     *
     * @return bool
     */
    public function hasTenant()
    {
        return $this->tenant !== null;
    }

    /**
     * Retrieve the current tenant.
     *
     * @return \Ollieread\Multitenancy\Contracts\Tenant
     */
    public function tenant()
    {
        return $this->tenant;
    }

    /**
     * Set the current tenant.
     *
     * @param Tenant $tenant
     */
    public function setTenant($tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Setup system routes that should belong to a tenant.
     *
     * @param \Closure $routes
     *
     * @return mixed
     */
    public function routes(\Closure $routes)
    {
        Route::pattern('_multitenant_', '[a-z0-9.]+');

        return Route::group(
            [
                'domain'        => '{_multitenant_}',
                'middleware'    => LoadTenant::class
            ],
            $routes);
    }

    /**
     * Process the request and load the tenant.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Ollieread\Multitenancy\Exceptions\InvalidTenantException
     */
    public function process(Request $request)
    {
        $identifier = $request->route()->parameter('_multitenant_');
        $this->primary = false;

        if (strpos($identifier, $this->config['domain']) !== false) {
            $identifier = str_replace('.'.$this->config['domain'], '', $identifier);
            $this->primary = true;
        }

        $this->tenant = $this->provider()->retrieveByIdentifier($identifier, $this->primary);

        if (! $this->tenant) {
            throw new InvalidTenantException('Invalid Tenant \''.$identifier.'\'');
        }

        $request->route()->forgetParameter('_multitenant_');
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
        return route($name, array_merge([$this->getIdentifier()], $parameters), $absolute);
    }

    /**
     * Returns whether or not the current tenant is identified by the primary identifier. Assume that if there is a tenant
     * and this is false, that they're using the secondary identifier.
     *
     * @return bool
     */
    public function isPrimary()
    {
        return $this->primary;
    }

    /**
     * Retrieve the identifier for the currently identified tenant.
     *
     * @return string
     */
    protected function getIdentifier()
    {
        if ($secondary = $this->tenant->getSecondaryIdentifier()) {
            return $secondary;
        } else {
            return $this->tenant->getPrimaryIdentifier().'.'.$this->config['domain'];
        }
    }
}