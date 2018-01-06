# Laravel Multitenancy #

[![Latest Stable Version](https://poser.pugx.org/ollieread/laravel-multitenancy/v/stable.png)](https://packagist.org/packages/ollieread/laravel-multitenancy) [![Total Downloads](https://poser.pugx.org/ollieread/laravel-multitenancy/downloads.png)](https://packagist.org/packages/ollieread/laravel-multitenancy) [![Latest Unstable Version](https://poser.pugx.org/ollieread/laravel-multitenancy/v/unstable.png)](https://packagist.org/packages/ollieread/laravel-multitenancy) [![License](https://poser.pugx.org/ollieread/laravel-multitenancy/license.png)](https://packagist.org/packages/ollieread/laravel-multitenancy)

- **Laravel**: 5.5
- **PHP**: 7.1+
- **Author**: Ollie Read 
- **Author Homepage**: http://ollieread.com

This package provides multi-database multi-tenancy support for your Laravel application. 

Tenants are identified primarily by a domain, falling back on a subdomain of a provided domain. Tenants are stored in a central database, with an individual database per tenant. 

## Installation

Package is available on [Packagist](https://packagist.org/packages/ollieread/laravel-multitenancy), you can install it using Composer.

    composer require ollieread/laravel-multitenancy
    
Next you'll want to publish the configuration.

    php artisan vendor:publish --provider=Ollieread\Multitenancy\ServiceProvider
    
## Configuration

The configuration file will be located at `config/multitenancy.php`.

### Provider

There are two providers available by default, `database` and `eloquent`. Set this with `multitenancy.provider`.

Each provider has its own settings, of which defaults are provided.

#### Eloquent

For the `eloquent` provider, just provide the model class with `multitenancy.eloquent.model`.

#### Database

For the `database` provider, you'll need to provide the tenant table name with `multitenancy.database.table`, and the identifiers for subdomain and domain with `mutltitenancy.database.identifiers.subdomain` and `multitenancy.database.identifiers.domain`.

### Domain

For the fallback subdomains, you'll need to provide the domain for usage with `multitenancy.domain`.

### Multidatabase

For the multidatabase support, you need to provide the connection to be used with `multitenancy.multidatabase.connection`. You can also change the default location of the tenant migrations with `multitenancy.multidatabase.migrations`.

For a simple setup, I'll typically just duplicate the `mysql` connection, and call it something else.

## Usage

You can use the tenancy manager by using the following facade;

    Ollieread\Multitenancy\Facades\Multitenancy
    
Or you can inject the following manager;

    Ollieread\Multitenancy\TenantManager
    
### Eloquent

If you're using the `eloquent` provider, your tenant model should implement the following contract;

    Ollieread\Multitenancy\Contracts\Tenant
    
For simplicity, you can use the following trait;

    Ollieread\Multitenancy\Concerns\IsTenant
    
All models that represent data for the tenant should return the connection name provided in the configuration. This can be done in several ways.

By implementing the following contract;

    Ollieread\Multitenancy\Concerns\HasTenant
    
By setting the connection property on the models;

    protected $connection = 'my-tenant-connection-name';
    
Or by returning the name from the method;

    public function getConnectionName(): string
    {
        return 'my-tenant-connection-name';
    }
    
The choice is entirely up to you, but as long as it's done, that's fine.

### Manually Setting the Tenant

To set the tenant, you need to call the following;

    $tenantManager->setTenant($tenant)
    
The value of `$tenant` should be object that implements the tenant contract. If you're using the database provider, you can just pass the array representing the tenant into the constructor of the following class.

    Ollieread\Multitenancy\GenericTenant
    
When overwriting the tenant by setting it when a tenant has already been set, it is required that you chain the following method;

    $tenantManager->setTenant($tenant)->reconnect()
    
This actually causes the connection configuration to be parsed again.
    
### Retrieving the Tenant

To retrieve the current tenant, you need to call the following;

    $tenantManager->tenant()

### Tenant Routes

Tenant routes should be wrapped in a group with the following middleware.

    Ollieread\Multitenancy\Middleware\LoadTenant
    
I recommend creating a middleware group for this.

You can generate a `route` or `url` for the currently set tenant by using the following method.

    $tenantManager->route('route.name')
    $tenantManager->url('/something/path')
    
These methods work exactly the same as the `route` and `url` helper method, except they're prefixed with the domain or subdomain if no domain is available. By default, the `route` method will not prefix with the protocol.

### Handling the Tenant connection

If you wish to change the connection parser used to connect to a tenants database, you need to call the following;

    $tenantManager->setConnectionParser(function ($config = [], Tenant $tenant) {
        $config['database'] = 'tenant_'.$tenant->id;
        return $config;
    });
    
The above is the default connection parser provided with this package.

To retrieve the connection object for the multitenant connection, you need to call the following;

    $tenantManager->connection()
    
You can force the connection to reconnect by calling the following;

    $tenantManager->reconnect()
    
## Custom Providers

If you wish to create yourself a customer provider, for example, using Doctrine, you need to implement the following interface;

    Ollieread\Multitenancy\Contracts\Provider
    
Once you have this, you can register this the same way you can with most Laravel packages.

    Multitenancy::extend('eloquent', function ($app, $config) {
        return new Eloquent($config['model']);
    })->extend('database', function ($app, $config) {
        return new Database($app['db']->connection(), $config['table'], $config['identifiers']);
    });