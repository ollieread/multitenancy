# Laravel Multitenancy #

[![Latest Stable Version](https://poser.pugx.org/ollieread/laravel-multitenancy/v/stable.png)](https://packagist.org/packages/ollieread/laravel-multitenancy) [![Total Downloads](https://poser.pugx.org/ollieread/laravel-multitenancy/downloads.png)](https://packagist.org/packages/ollieread/laravel-multitenancy) [![Latest Unstable Version](https://poser.pugx.org/ollieread/laravel-multitenancy/v/unstable.png)](https://packagist.org/packages/ollieread/laravel-multitenancy) [![License](https://poser.pugx.org/ollieread/laravel-multitenancy/license.png)](https://packagist.org/packages/ollieread/laravel-multitenancy)

- **Laravel**: 5.3
- **Author**: Ollie Read 
- **Author Homepage**: http://ollieread.com

Laralve package for multitenancy using subdomain and/or domain based identification.
The package itself works much in the same way as the default Auth library.

## Installation ##

Firstly you want to include this package in your composer.json file.

    "require": {
        "ollieread/laravel-multitenancy": "dev-master"
    }
    
Now you'll want to update or install via composer.

    composer update

Next you open up app/config/app.php and add the following.

    Ollieread\Multitenancy\ServiceProvider::class,
    
Then the facade.

    'Multitenancy' => Ollieread\Multitenancy\Facades\Multitenancy::class,

Finally, run the following command to publish the config.

    php artisan vendor:publish --provider=Ollieread\Multitenancy\ServiceProvider
    
## Configuration ##

There are three main parts to the configuration.

#####The provider

    'provider'      => 'eloquent',
    
By default this is can be either `eloquent` or `database`. If you add a custom provider, you'd use the name here.

#####The domain

    'domain'        => env('MULTITENANCY_DOMAIN', 'mydomain.com'),
    
The domain that should be used for tenant based subdomains.

#####The Provider settings

    'eloquent'      => [
        // The model representing a tenant
        'model'         => Ollieread\Multitenancy\Models\Tenant::class
    ],
    
    'database'      => [
        // The table where tenants are stored
        'table'         => 'tenants',
        // The foreign key for identifying tenant ownership
        'foreign_key'   => 'tenant_id',
        // The identifiers used to identify a tenant
        'identifiers'   => [
            'slug', 'domain'
        ]
    ]
    
These particular settings are defined by the individual providers and the defaults contain small descriptions.

## Current Tenant ##

Access the currently identified tenant by using `Multitenancy::tenant()`.

## Tenant Routes ##

There is a method for create route groups that should be part of the tenant system.

    Multitenancy::routes(function (Router $router) {
        $router->get('/tenancy', function() {
            $tenant = Multitenancy::tenant();
            dd($tenant);
        });
    });
    
To generate a url for a tenant based route, you can use the following methods:

    Multitenancy::route($name, $paramaters = [], $absolute = false);
    Multitenancy::url($path, $paramaters = [], $secure = false);
    
These methods act the same as `route()` and `url()` except that they automatically add the correct domain for the current tenant.

## Eloquent ##

If using the eloquent provider, the specified model must implement:

    Ollieread\Multitenancy\Contracts\Tenant
    
There is a trait available that provides implementation using the default column names `slug` and `domain`. It also offers access to `route()` and `url()` on the model. This trait is:

    Ollieread\Multitenancy\Traits\Tenant
    
### Scopes ###

There is a scope available to you for use on models that belong to a tenant. To use this, add the following trait to the models you wish to belong to a tenant:

    Ollieread\Multitenancy\Traits\TenantOwned
    
This only works for models that have the tenant foreign key as a column, and is designed to prevent you from having to manually add where clauses everywhere.

If you wish to see all entries regardless of the current tenant, use the `withAll()` method.

## Authentication ##

If you're using the `TenantOwned` trait on your user model you won't need to do anything with the Eloquent provider.

For session based authentication you'll want to use the `session.multi` guard, which is identical to the default session guard, except that prefixes session and cookie names with the tenant primary identifier to allow users to be logged into multiple tenants at once.

If you're using the Database provider for auth, you'll want to use the `database.multi` provider so that you only retrieve records specific to the current tenant.

## Custom Providers ##

This package supports custom providers. To create a custom provider, create a class that implements `Ollieread\Multitenancy\Contracts\Provider` and then register it like so:

    Multitenancy::extend('eloquent', function ($app, $config) {
        return new Eloquent($config['model']);
    })->extend('database', function ($app, $config) {
        return new Database($app['db']->connection(), $config['table'], $config['identifiers']);
    });
    
The method is setup for daisy chaining should you need to add multiple.