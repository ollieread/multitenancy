<?php

namespace Ollieread\Multitenancy;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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
    use Concerns\HasProviders,
        Concerns\ManagesConnection,
        Concerns\ManagesDatabase;

    /**
     * The configuration for the package.
     *
     * @var array
     */
    protected $config;

    /**
     * An instance of the application.
     *
     * @var Application
     */
    protected $app;

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
        $this->app    = $app;
        $this->config = $app['config']['multitenancy'];
        $this->setConnections((array) $app['config']['multitenancy']['multidatabase']['connection']);
    }

    /**
     * Whether or not we currently have a tenant.
     *
     * @return bool
     */
    public function hasTenant(): bool
    {
        return $this->tenant !== null;
    }

    /**
     * Retrieve the current tenant.
     *
     * @return \Ollieread\Multitenancy\Contracts\Tenant
     */
    public function tenant(): Tenant
    {
        return $this->tenant;
    }

    /**
     * Set the current tenant.
     *
     * @param Tenant $tenant
     *
     * @return \Ollieread\Multitenancy\TenantManager
     */
    public function setTenant($tenant): TenantManager
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
                'domain'     => '{_multitenant_}',
                'middleware' => LoadTenant::class,
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
    public function process(Request $request): void
    {
        $identifier = $request->getHost();

        $this->loadTenant($identifier);
    }

    /**
     * Load the tenant from the provided identifier.
     *
     * @param $identifier
     *
     * @return bool
     * @throws \Ollieread\Multitenancy\Exceptions\InvalidTenantException
     */
    protected function loadTenant($identifier): bool
    {
        $this->primary = false;

        if (strpos($identifier, $this->config['domain']) !== false) {
            $this->tenant = $this->provider()
                ->retrieveBySubdomainIdentifier(str_replace('.' . $this->config['domain'], '', $identifier));
        } else {
            $this->tenant = $this->provider()
                ->retrieveByDomainIdentifier($identifier);
        }

        if (! $this->tenant) {
            throw new InvalidTenantException('Invalid Tenant \'' . $identifier . '\'');
        }

        return true;
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
    public function route($name, array $parameters = [], $absolute = true): string
    {
        $route = route($name, $parameters, $absolute);

        return $this->getIdentifier() . '/' . $route;
    }

    /**
     * Retrieve a URl for the current tenant
     *
     * @param       $path
     * @param array $parameters
     * @param bool  $secure
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function url($path, $parameters = [], $secure = false)
    {
        return url($this->getIdentifier() . '/' . $path, $parameters, $secure);
    }

    /**
     * Retrieve the identifier for the currently identified tenant.
     *
     * @return string
     */
    protected function getIdentifier(): ?string
    {
        if ($domain = $this->tenant->getDomainIdentifier()) {
            return $domain;
        } else {
            return $this->tenant->getSubdomainIdentifier() . '.' . $this->config['domain'];
        }
    }
}