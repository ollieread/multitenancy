<?php
namespace Ollieread\Multitenancy;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Ollieread\Multitenancy\Auth\DatabaseUserProvider;
use Ollieread\Multitenancy\Auth\SessionGuard;
use Ollieread\Multitenancy\Contracts\Tenant;
use Ollieread\Multitenancy\Exceptions\InvalidTenantException;
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
            __DIR__ . '/../config/multitenancy.php' => config_path('multitenancy.php')
        ], 'config');
    }

    /**
     * Register any application services.
     *
     * @return void
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

        // Setup multidatabase
        // Extend the database connection
        $this->app['db']->extend(config('multitenancy.multidatabase.connection'), function ($config, $name) use ($manager) {
            if ($manager->hasTenant()) {
                $config = $manager->parseConnection($config);

                return $this->app['db.factory']->make($config, $name);
            }

            throw new InvalidTenantException('No tenant selected');
        });
        // Set the parser of the config
        $manager->setConnectionParser(function ($config = [], Tenant $tenant) {
            if ($tenant) {
                $config['database'] = $tenant->getDatabaseName();
            }

            return $config;
        });
    }

    public function provides()
    {
        return ['multitenancy'];
    }
}