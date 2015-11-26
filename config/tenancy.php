<?php

return [

    /*
     * Participant class and repository must be a valid Doctrine Entity + Repository.
     * The Doctrine EntityRepository class should be fine.
     *
     * It is recommended to use the ::class property to allow auto-updating the class
     * names when using IDE refactoring tools.
     */
    'participant_class'      => '',
    'participant_repository' => '',

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
     *     'repository => '\App\Repo\Tenant\MyRepo',
     *     'base' => '\App\Repo\MyRepo',
     *     'alias' => 'tenant.my_repo', 'tags' => ['tenant_aware']
     * ]
     */
    'repositories' => [],
];