<?php

return [

    /*
     * Multi-Account Tenancy
     *
     * Allows multiple accounts on a single App (domain). Tenancy is handled through
     * the URI so: domain.com/tenant/<id>/app/route/here. A tenant can have multiple
     * sub-accounts if desired. The participant must be configured and must implement
     * the interfaces:
     *
     *  * TenantParticipant
     *  * TenantParticipantRepository
     *
     * Eloquent models should work, provided a repository class is created first.
     */
    'multi_account' => [
        'enabled' => false,

        /*
         * Participant class and repository must be a valid Entity + Repository class.
         * The Doctrine EntityRepository class should be fine if using Doctrine.
         *
         * It is recommended to use the ::class property to allow auto-updating the class
         * names when using IDE refactoring tools.
         *
         * Mappings
         *
         * Mappings allow a short alias to be used for the participant class e.g. for use
         * with EnsureTenantType route middleware in routes.
         *
         * If you are using table inheritance, then you must provide mappings for
         * each class type - NOT the abstract base class. The aliases can be
         * whatever you like. It is suggested to use:
         *
         * short name (for routing)
         * doctrine discriminator name (for consistency with Doctrine)
         * class name (for class look ups)
         *
         * Format is:
         * // alias => fully qualified class name
         * 'alias' => \App\Entity\MyClass::class,
         */
        'participant' => [
            'class'      => '',
            'repository' => '',
            'mappings'   => [
                // 'my_alias' => Some\Class\Name::class,
            ],
        ],
    ],

    /*
     * Multi-Site Tenancy
     *
     * Multi-Site has a separate configuration as it can run at the same time as
     * multi-account. It has a similar set of options to multi-account and requires
     * that the tenant participant implement:
     *
     *  * DomainAwareTenantParticipant
     *  * DomainAwareTenantParticipantRepository
     */
    'multi_site' => [
        'enabled' => false,

        /*
         * For multi-site tenancy i.e. by domain name, specify the
         * DomainAwareTenantParticipantRepository here. This will be used to find tenants
         * by the domain name. The entity class is also required.
         *
         * Note: that the interface extends TenantParticipant so the same details can be
         * used here as in the previous participant settings.
         */
        'participant' => [
            'class'      => '',
            'repository' => '',
            'mappings'   => [
                // 'my_alias' => Some\Class\Name::class,
            ],
        ],

        /*
         * An array of domain chunks that should be removed from the host name before
         * performing a domain aware tenant participant look-up.
         *
         * For example: your main domain is www.example.com but in testing and dev it
         * is prefixed with dev. and test. this array can contain these (and www.) and
         * the host will always be resolved to example.com.
         *
         * Leave this empty to have the domain look up always check the full domain.
         */
        'ignorable_domain_components' => [
            'dev.',
            'test.',
        ],

        /*
         * Register your router settings for multi-site. The namespace and patterns will
         * be added to the router when the TenantRouteResolver middleware is triggered.
         */
        'router' => [
            'namespace' => 'App\Http\Controllers',
            'patterns'  => [
                // 'id' => '[0-9]+',
            ],
        ],
    ],

    /*
     * Register Doctrine repositories
     *
     * Add any repositories that need to be added to the container with the
     * tenant bound. This should be the Tenant version, the base repository
     * being wrapped, and then an alias / any tags (optional).
     *
     * Each Tenanted repository should extend TenantAwareRepository and ensure
     * that any custom security models have been defined in the repository
     * methods.
     *
     * Generally you should extend the TenantAwareRepository to a base class that
     * can add / override the security model methods.
     *
     * Format is:
     *
     * [
     *     'repository' => '\App\Repo\Tenant\MyRepo',
     *     'base'       => '\App\Repo\MyRepo',
     *     'alias'      => 'tenant.my_repo',
     *     'tags'       => ['tenant_aware']
     * ]
     */
    'doctrine' => [
        'repositories' => [],
    ],
];