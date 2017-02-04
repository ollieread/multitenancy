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
     * Provider specific settings.
     * If you add a custom provider it's easier to just add your configuration in here, and the
     * tenant manager will pass the correct details through.
     */
    'providers'     => [
        //Eloquent provider settings.
        'eloquent'      => [
            // The model representing a tenant
            'model'         => Ollieread\Multitenancy\Models\Tenant::class
        ],
        //Database provider settings.
        'database'      => [
            // The table where tenants are stored
            'table'         => 'tenants',
            // The foreign key for identifying tenant ownership
            'foreign_key'   => 'tenant_id',
            // The identifiers used to identify a tenant
            'identifiers'   => [
                'slug', 'domain'
            ],
        ]
    ],

    /*
     * This is for multitenant setups where each tenant should have their own
     * individual database.
     */
    'multidatabase' => [
        /*
         * This is the connection to use for tenancy, for example, if you wish to use the same
         * connection details as the mysql connection, copy the details into a new connection
         * in config/database.php and add the name here.
         */
        'connection'    => 'multitenancy'
    ]

];
