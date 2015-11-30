<?php

return [

    /*
     * Participant class and repository must be a valid Doctrine Entity + Repository.
     * The Doctrine EntityRepository class should be fine.
     *
     * It is recommended to use the ::class property to allow auto-updating the class
     * names when using IDE refactoring tools.
     */
    'participant_class'             => '',
    'participant_repository'        => '',

    /*
     * For multi-site tenancy i.e. by domain name, specify the
     * DomainAwareTenantParticipantRepository here. This will be used to find tenants
     * by the domain name. The entity class is also required.
     *
     * Note: that the interface extends TenantParticipant so the same details can be
     * used here as in the previous participant settings.
     */
    'domain_participant_class'      => null,
    'domain_participant_repository' => null,

    /*
     * Map aliases to the various participant classes
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
     *
     * It is best to reference the ::class property so that if you rename or
     * move your entity classes, these will all auto-update (if using an IDE).
     */
    'participant_mappings' => [],

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
        'dev.', 'test.',
    ],

    /*
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
    'repositories' => [],
];