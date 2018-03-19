<?php

namespace Ollieread\Multitenancy;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Ollieread\Multitenancy\Auth\DatabaseUserProvider;
use Ollieread\Multitenancy\Auth\SessionGuard;
use Ollieread\Multitenancy\Contracts\Tenant;
use Ollieread\Multitenancy\Providers\Database;
use Ollieread\Multitenancy\Providers\Eloquent;

/**
 * Class ServiceProvider
 *
 * @package Ollieread\Multitenancy
 */
class ServiceProvider extends BaseServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/multitenancy.php' => config_path('multitenancy.php'),
        ], 'config');
    }

    /**
     * Register any application services.
     *
     * @return void
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function register()
    {
        // Register the tenant manager
        $manager = new TenantManager($this->app);
        $manager->extend('eloquent', function ($app, $config) {
            return new Eloquent($config['model']);
        })->extend('database', function ($app, $config) {
            return new Database($app['db']->connection(), $config['table'], $config['identifiers']);
        });
        // Register the instances with the ioc
        $this->app->instance('multitenancy', $manager);
        $this->app->singleton('multitenancy.provider', function ($app) {
            return $app['multitenancy']->provider();
        });

        $connections = $this->app['config']['multitenancy']['multidatabase']['connection'];
        $connections = \is_array($connections) ? $connections : [$connections];

        foreach ($connections as $connection) {
            $this->app['db']->extend($connection, function ($config, $name) use ($manager) {
                return $manager->resolveConnection($config, $name);
            });
        }

        $manager->setConnectionResolver(function ($config = [], Tenant $tenant, string $connection) {
            if ($tenant) {
                $config['database'] = 'tenant_' . $tenant->id;
            }

            if ($config['driver'] === 'mongodb') {
                // This is just a plain string so that we don't have to include the mongodb library
                // I'm assuming everyone is using this. If you aren't, you can override the resolver yourself.
                $mongodbConnection = '\Jenssegers\Mongodb\Connection';

                if (class_exists($mongodbConnection)) {
                    return new $mongodbConnection($config);
                }
            }

            return $this->app['db.factory']->make($config, $connection);
        });
    }

    public function provides()
    {
        return ['multitenancy'];
    }
}