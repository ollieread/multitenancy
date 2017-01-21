<?php

return [

    /*
     * The provider for retrieving the tenant.
     *
     * Options are: eloquent or database.
     */
    'provider'      => 'eloquent',

    // The domain used for subdomain lookup, tenant would be {identifier}.mydomain.com
    'domain'        => env('MULTITENANCY_DOMAIN', 'mydomain.com'),

    /*
     * Eloquent provider settings.
     */
    'eloquent'      => [
        // The model representing a tenant
        'model'         => Ollieread\Multitenancy\Models\Tenant::class
    ],

    /*
     * Database provider settings.
     */
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

];
