## Multi-Tenancy for Laravel and Laravel-Doctrine

This library provides the necessary infra-structure for a complex multi-tenant application.
Multi-tenancy allows an application to be silo'd into protected areas by some form of tenant
identifier. This could be by sub-domain, URL parameter or some other scheme.

### Terminology

#### Tenant

Identifies the currently loaded account that the user belongs to. There are two components:

 * Tenant Owner: tenant_owner_id
 * Tenant Creator: tenant_creator_id

The tenant owner is the root account that actual "owns" the tenant-aware data.

The tenant creator is the instance that is adding or manipulating data that belongs to the tenant owner.

The tenant owner and creator may be the same entity.

The Tenant is its own object, registered in the container as: auth.tenant.

#### Tenant Participant

A tenant participant identifies the entity that is actually providing the tenancy reference.
This must be defined for this library to work and there can only be a single entity.

Typically this will be an Account class or User or (from laravel-doctrine/acl), an organization.

The tenant participant may be a polymorphic entity e.g.: one that uses single table inheritance.

#### Tenant Participant Mapping

Provides an alias to the tenant participant for easier referencing.

_Note:_ this is not a container alias but used internally for tagging routes. e.g.:
the participant class is \App\Entity\SomeType\TheActualInstanceClass and in the routes we want
to restrict to this type. Instead of using the whole class name, it can be aliased to "short_name".

#### Tenant Aware

An entity that implements the TenantAware contract (interface). This allows the data to be portioned
by the tenant owner / creator.

A tenant aware entity requires:

 * get/set TenantOwnerId
 * get/set TenantCreatorId
 * importTenancyFrom

#### Tenant Aware Repository

A specific repository that will enforce the tenant requirements ensuring that any fetch request
will be correctly bound with the tenant owner and creator, depending on the security scheme
that has been implemented on the tenant owners data.

A tenant aware repository usually wraps the standard entities repository class. This may be the
standard Doctrine EntityRepository.

#### Security Model

Defines how data is shared within a tenant owners account. In many situations this will be just
the tenant owner and creator only, however this library allows a hierarchy and a user to have
multiple tenants associated with them. In this instance the security level will determine what
information is available to the user depending on their current creator instance.

The provided security models are:

 * shared - all data within the tenant owner is shared to all tenant creators
 * user - the user can access all data they are allowed access to within the tenant owner
 * closed - only the current creator within the owner is permitted
 * inherit - defer to the parent to get the security model.

Additional models can be implemented. The default configuration is closed, with no sharing.

#### Domain Aware Tenant Participant

A domain aware tenant participant adds support for a domain name to the interface. This allows
the tenant information to be resolved from the current host name passed into the application.
This is used with the TenantSiteResolver middleware.

#### Domain Aware Tenant Participant Repository

The repository for the domain aware tenant participants. It is separate to the tenant
participant allowing separate instances to be used. Domain aware is used with the
TenantSiteResolver middleware.

### Forms of Tenancy

This library provides the following tenant setups, in increasing order of complexity:

 * single app, URI tenancy
 * multi-site, domain name tenancy
 * multi-site with URI tenancy

#### Single App, URI Tenancy

The simplest case is a single App, that all users register for and the tenancy is defined by
the tenant_creator_id in the route URI. The tenancy is resolved on User login meaning that this
offers the smallest impact in your application.

If you need to serve static, non-tenant pages or your app does not need theming support, this is
the preferred tenancy model.

#### Multi-Site, Domain Name Tenancy

Increasing in complexity, the next level is domain-name based tenancy. Multiple sites running from
a single app folder. This is usually some form of white-labelling setup i.e. the same application
is re-skinned with different branding but the underlying app is practically the same.

_Note:_ this is a substantial increase in difficulty from single app tenancy. You will need to
change the Application instance in /bootstrap/app.php to use:

    Somnambulist\Tenancy\Foundation\TenantAwareApplication

_Note:_ you have to remove RouteServiceProvider and add TenantRouteResolver middleware.

_Note:_ you must ensure that any caches you use can handle per-site caching.

In addition, this form of tenancy requires a middleware to run all the time to resolve the current
tenant information before any users login or the main app actually runs. If using a database for
the tenant source, this could increase site overhead and a high-performance cache is highly
recommended for production environments e.g.: APCu or an in memory-cache that persists between
requests to reduce the overhead of the tenant resolution.

A file-system repository can be easily created instead of using the database, or a combination of
both where a cache file is generated when the tenant sources change.

In multi-site, changes must be made to your app config:

 * view.paths: should have the default path changed to views/default
 * view.compiled: should have the default path changed to views/default

When creating your app, you will need to create a "default" view theme and then mirror this for
each domain you serve from the app. The view folder should be named after the domain that is
bound to the tenant.

    www.example.com -> resources/views/www.example.com

Your views folder will end up looking like:

    resources/views/default
    resources/views/www.example.com
    resources/views/store.example2.com
    resources/views/store.example3.com

Once the tenant information has been resolved, several updates are made to the container
configuration:

 * app.url is replaced with the current host domain (not resolved domain name)
 * template paths are re-computed as a hierarchy and the finder reset

Template path order is reset to:

 * tenant creator domain
 * tenant owner domain (if different)
 * default / existing paths

This way templates should be evaluated from most specific to least specific.

_Note:_ auth.tenant is initialised with the tenant owner / creator and a NullUser.

#### Multi-Site with URI tenancy

_Note:_ this is most complex scenario. TenantAwareApplication is required.

_Note:_ you have to remove RouteServiceProvider and add TenantRouteResolver middleware.

_Note:_ you must ensure that any caches you use can handle per-site caching.

This is a combination of both methods where there are multiple tenants per multi-site. In this
configuration there are limitations on the security that can be implemented unless a custom
implementation is made:

 * there is only one tenant owner per domain
 * all tenant owners should have the closed security model
 * all tenant creators should have the closed security model

It is possible to allow further tenanting however this would have to be a custom implementation
as your tenant creator would have to allow child tenants and implement a security model that is
appropriate in this situation. One possible example would be to cascade up through the parents
to set the tenant owner (which would be the domain tenant owner).

This setup has the highest impact on site performance and requires users login to resolve their
tenancy. As such, this essentially results in double tenancy resolution.

This setup is not recommended as it could lead to hard to diagnose issues, but is included as it
is technically feasible with the current implementation.

_Note:_ auth.tenant is initialised with the tenant owner / creator and a NullUser but after
User authentication will be updated with the current, authenticated user and any changes to the
creator tenant as needed. The owner tenant should still be the same as the creator must be a
child of the owner.

### Requirements

 * PHP 5.5+
 * laravel 5.1+
 * laravel-doctrine/orm
 * somnambulist/laravel-doctrine-behaviours

### Installation

Install using composer, or checkout / pull the files from github.com.

 * composer install somnambulist/laravel-doctrine-tenancy

### Setup / Getting Started

 * add \Somnambulist\Tenancy\TenancyServiceProvider::class to your config/app.php
 * add \Somnambulist\Tenancy\TenantImporterEventSubscriber::class to config/doctrine.php subscribers
 * create or import the config/tenancy.php file
 * create your TenantParticipant entity / repository and add to the config file
 * create your participant mappings in the config file (at least class => class)
 * create your User with tenancy support
 * create an App\Http\Controller\TenantController to handle the various tenant redirects
 * add the basic routes
 * for multi-site
   * in bootstrap/app.php
     * change Application instance to \Somnambulist\Tenancy\Foundation\TenantAwareApplication
   * in HttpKernel:
     * add TenantSiteResolver middleware to middleware, after CheckForMaintenanceMode
     * add TenantRouteResolver middleware to middleware, after TenantSiteResolver
     * remove RouteServiceProvider from config/app.php
 * for standard app tenancy and/or for tenancy within multi-site
   * add AuthenticateTenant as auth.tenant to HttpKernel route middlewares
   * add EnsureTenantType as auth.tenant.type to HttpKernel route middlewares

#### Doctrine Event Subscriber

An event subscriber is provided that will automatically set the tenancy information on any
tenant aware entity upon persist. Note that this only occurs on prePersist and once created
should not be modified. If this information is subsequently removed, then records may simply
disappear when accessing the tenant aware repositories.

#### Example User

The following is an example of a tenant aware user that has a single tenant:

    <?php
    namespace App\Entity;

    use Somnambulist\Tenancy\Contracts\BelongsToTenant as BelongsToTenantContract;
    use Somnambulist\Tenancy\Contracts\BelongsToTenantParticipants;
    use Somnambulist\Tenancy\Contracts\TenantParticipant;
    use Somnambulist\Tenancy\Traits\BelongsToTenant;
    class User implements AuthenticatableContract, AuthorizableContract,
           CanResetPasswordContract, BelongsToTenantContract, BelongsToTenantParticipant
    {
        use BelongsToTenant;

        protected $tenant;

        public function getTenantParticipant()
        {
            return $this->tenant;
        }

        public function setTenantParticipant(TenantParticipant $tenant)
        {
            $this->tenant = $tenant;
        }
    }

#### Example Tenant Participant

The following is an example of a tenant participant:

    <?php
    namespace App\Entity;

    use Somnambulist\Doctrine\Traits\Identifiable;
    use Somnambulist\Doctrine\Traits\Nameable;
    use Somnambulist\Tenancy\Contracts\TenantParticipant as TenantParticipantContract;
    use Somnambulist\Tenancy\Traits\TenantParticipant;

    class Account implements TenantParticipantContract
    {
        use Identifiable;
        use Nameable;
        use TenantParticipant;
    }

#### Basic Routes

The two authentication middlewares expect the following routes to be defined and available:

    // tenant selection and error routes
    Route::group(['prefix' => 'tenant', 'as' => 'tenant.', 'middleware' => ['auth']], function () {
        Route::get('select',          ['as' => 'select_tenant',             'uses' => 'TenantController@selectTenantAction']);
        Route::get('no-tenants',      ['as' => 'no_tenants',                'uses' => 'TenantController@noTenantsAvailableAction']);
        Route::get('no-access',       ['as' => 'access_denied',             'uses' => 'TenantController@accessDeniedAction']);
        Route::get('not-supported',   ['as' => 'tenant_type_not_supported', 'uses' => 'TenantController@tenantTypeNotSupportedAction']);
        Route::get('invalid-request', ['as' => 'invalid_tenant_hierarchy',  'uses' => 'TenantController@invalidHierarchyAction']);
    });

As a separate block (or within the previous section) add the areas of the application that
require tenancy support / enforcement. These routes should be prefixed with at least:
{tenant_creator_id}. {tenant_owner_id} can be used (first) which will force the auth.tenant
middleware to validate that the creator belongs to the owner as well as the current user
having access to the creator.

_Note:_ the user does not need access to the tenant owner, access to the tenant creator implies
permission to access a sub-set of the data.

    // Tenant Aware Routes
    Route::group(['prefix' => 'account/{tenant_creator_id}', 'as' => 'tenant.', 'namespace' => 'Tenant', 'middleware' => ['auth', 'auth.tenant']], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'DashboardController@indexAction']);

        // routes that should be limited to certain ParticipantTypes
        Route::group(['prefix' => 'customer', 'as' => 'customer.', 'namespace' => 'Customer', 'middleware' => ['auth.tenant.type:crm']], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'CustomerController@indexAction']);
        });
    });

#### AuthController Changes

When using tenancy, the AuthController must be modified to include the redirector service
to know where to go to after a successful login. If your AuthController is the standard
Laravel provided one, simply add an authenticated method:

    class AuthController extends Controller
    {
        /**
         * @param Request $request
         * @param User    $user
         *
         * @return \Illuminate\Http\RedirectResponse
         */
        protected function authenticated($request, $user)
        {
            // do post authentication stuff...
            //$user->setLastLogin(Carbon::now());
            //$em = app('em');
            //$em->flush($user);

            // redirect to tenant uri
            return app('auth.tenant.redirector')->resolve($user);
        }
    }

In addition, if you allow registration of new users you will need to now add support for the
tenancy component. This must be done by overriding the postRegister method:

    class AuthController ...
    {
        public function postRegister(Request $request)
        {
            $validator = $this->validator($request->all());

            if ($validator->fails()) {
                $this->throwValidationException(
                    $request,
                    $validator
                );
            }

            $user = $this->create($request->all());
            Auth::login($user);

            // call into redirector which was previously mapped above
            return $this->authenticated($request, $user);
        }
    }

It is up to the implementer to figure out what to do with new registrations or if this
should even be allowed.

#### Tenant Aware Entity

Finally you need something that is actually tenant aware! So lets create a really basic
customer:

    <?php
    namespace App\Entity;

    use Somnambulist\Doctrine\Contracts\GloballyTrackable as GloballyTrackableContract;
    use Somnambulist\Doctrine\Traits\GloballyTrackable;
    use Somnambulist\Tenancy\Contracts\TenantAware as TenantAwareContract;
    use Somnambulist\Tenancy\Traits\TenantAware;

    class Customer implements GloballyTrackableContract, TenantAwareContract
    {
        use GloballyTrackable;
        use TenantAware;
    }

This creates a Customer entity that will track the tenant information. To save typing
this uses the built-in trait. A corresponding repository will need to be created along with
the Doctrine mapping file. Here is an example yaml file:

    App\Entity\Customer:
        type: entity
        table: customers
        repositoryClass: App\Repository\CustomerRepository

        uniqueConstraints:
            uniq_users_uuid:
                columns: [ uuid ]

        id:
            id:
                type: bigint
                generator:
                    strategy: auto

        fields:
            uuid:
                type: guid

            tenantOwnerId:
                type: integer

            tenantCreatorId:
                type: integer

            name:
                type: string
                length: 255

            createdBy:
                type: string
                length: 36

            updatedBy:
                type: string
                length: 36

            createdAt:
                type: datetime

            updatedAt:
                type: datetime

#### Tenant Aware Repositories

Tenant aware repositories simply wrap an existing entity repository with the standard
repository interface. They should be defined and created as we actually want to be
able to inject these as dependency and set them up in the container.

First you will need to create an App level TenantAwareRepository that extends:

 * Somnambulist\Tenancy\TenantAwareRepository

For example:

    <?php
    namespace App\Repository;

    use Somnambulist\Tenancy\TenantAwareRepository;

    class AppTenantAwareRepository extends TenantAwareRepository
    {

    }

Provided you don't have a custom security model, this should be good to extend again
as a namespaced "tenant" repository for our customer:

    <?php
    namespace App\Repository\TenantAware;

    use App\Repository\AppTenantAwareRepository;

    class CustomerRepository extends AppTenantAwareRepository
    {

    }

Now, the config/tenancy.php can be updated to add a repository config definition so this class
will be automatically available in the container.

_Note:_ this step presumes the standard repository is already mapped to the container using
the repository class as the key.

    [
        'repository' => \App\Repository\TenantAware\CustomerRepository::class,
        'base'       => \App\Repository\CustomerRepository::class,
        //'alias'      => 'app.repository.tenant_aware_customer', // optionally alias
        //'tags'       => ['repository', 'tenant_aware'], // optionally tag
    ],

### Security Models

The security model defines how data within a tenant owner should be shared. The default is no
sharing at all. In fact the security model only applies when the User implements the
BelongsToTenantParticipants and there can be multiple tenants on one user.

#### Shared

In this situation, the tenant owner may decide that any data can be shared by all child tenants
of the owner. This model is called "shared" and means that all data in the tenant owner is
available to all tenant creators at any time.

To set the security model, simply save the TenantParticipant instance with the security model
set to: TenantSecurityModel::SHARED()

Behind the scenes, when the TenantAwareRepository is queried, the current Tenant information is
extracted and the query builder instance modified to set the tenant owner and/or creator. For
shared data, only the owner is set.

The other pre-built models are:

 * user
 * closed
 * inherit

#### User

The User model restricts the queries to the current tenant owner and any mapped tenant. So if
a User has 4 child tenants, they will be able to access the data created only by those 4
child tenants. All other data will be excluded.

#### Closed

If the security model is set to closed, then all queries are created with the tenant owner and
current creator only. The user in this scheme, even with multiple tenant creators, will only
ever see data that was created by the current creator.

#### Inherit

Inherit allows the security model to be adopted from a parent tenant. If the parent model
is inherit, or there is no parent then the model will be set to closed automatically. This
library attempts to favour least access whenever possible.

#### Applying / Adding Security Models

The security model rules are applied by methods within the TenantAwareRepository. The model
name is capitalised, prefixed with "appy" and suffixed with SecurityModel so "shared" becomes
"applySharedSecurityModel".

This is why an App level repository is strongly suggested as you can then implement your own
security models simply by extending the TenantSecurityModel, defining some new constants and
then adding the appropriate method in your App repository.

For example: say you want to have a "global" policy where all unowned data is shared all over
but you also have your own data that is private to your tenant, you could add this as a new
method:

    class AppTenantAwareRepository extends TenantAwareRepository
    {

        protected function applyGlobalSecurityModel(QueryBuilder $qb, $alias)
        {
            $qb
                ->where("({$alias}.tenantOwnerId IS NULL OR {$alias}.tenantOwnerId = :tenantOwnerId)")
                ->setParameters([
                    ':tenantOwnerId' => $this->tenant->getTenantOwnerId(),
                ])
            ;
        }
    }

Additional schemes can be added as needed.

_Note:_ while in theory you can mix security models within a tenant e.g.: some children are
closed, others shared, some user; this may result in strange results or inconsistencies.
It may lead to a large increase in duplicate records. It is up to you to manage this
accordingly.

## Routing

For any route within the tenant group and provided that the placeholder name is tenant_creator_id,
any route generated for a tenant controller will automatically embed the current tenant information.
In fact both the owner and creator are automatically checked for and injected when creating
routes.

This is done by overriding the default UrlGenerator with one that adds the Tenant entity and then
checking the route information for both {tenant_owner_id} and {tenant_creator_id}. The properties
are then automatically injected.

This only occurs when using named routes and within a tenancy group.

For paths, you will have to include the tenant information yourself; similarly when creating a tenant
selection list, you must supply the tenant information as parameters when outputting the links.

The tenant parameters can of course be overridden by simply setting them when calling link_to_route.

You should use named routes for tenancy, as this makes it easier to make changes to the routing
structure.

Finally: as with repositories you should clearly label tenant based routes so that they are not
confused with standard routes.

In addition any un-authenticated routes should be excluded from the tenancy group - unless you
implement a tenant aware anonymous user (not recommended).

### Multi-Site Routing

In a multi-site setup you may want to have different routes per site. In this case you will need
to remove your RouteServiceProvider entirely and switch it for the TenantRouteResolver middleware.
Then you will need to either create per tenant domain route files (which can include() shared
routes) or symlink the files if you wish to use the exact same routes.

A middleware is provided to handle loading the routes for a multi-site setup. This must be loaded
after the TenantSiteResolver, but before any other middlewares. In addition you must disable / remove
the default App/Providers/RouteServiceProvider. This provider is registered too early and must be
delayed / resolved via the TenantRouteResolver instead.

_Note:_ these are **not** route middlewares but Kernel middlewares.

Your Kernel.php will end up looking like the following:

    class Kernel extends HttpKernel
    {
        protected $middleware = [
            \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,

            // must appear AFTER maintenance mode, but before everything else
            \Somnambulist\Tenancy\Http\Middleware\TenantSiteResolver::class,
            \Somnambulist\Tenancy\Http\Middleware\TenantRouteResolver::class,

            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
        ];

        protected $routeMiddleware = [
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.tenant' => \Somnambulist\Tenancy\Http\Middleware\AuthenticateTenant::class,
            'auth.tenant.type' => \Somnambulist\Tenancy\Http\Middleware\EnsureTenantType::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ];
    }

The auth.tenant / auth.tenant.type are optional in multi-site, but should likely be included.

Again: ensure that the previous RouteServiceProvider in config/app.php has been removed.

_Note:_ you must **not** use the standard route:list, route:cache in a multi-site setup. Tenant
aware versions of these commands are automatically registered if a multi-site setup is detected
in the configuration settings.

#### Route Namespace

When using the TenantRouteResolver, you can still specify the route namespace by adding
a config option to the main config/app.php file. The option is: app.route.namespace:

    <?php
    // config/app.php
    return [
        // other stuff...
        'route' => [
            'namespace' => 'App\Http\Controller', // default
        ],
        // more stuff...
    ];

If left out, the default App\Http\Controller is used.

## Twig Extension

A Twig extension will be automatically loaded if Twig is detected in the container which will
provide the following template functions:

 * current_tenant_owner_id
 * current_tenant_creator_id
 * current_tenant_owner
 * current_tenant_creator
 * get_entity_tenant_owner
 * get_entity_tenant_creator

This allows access to the current, active Tenant instance and to query for the owner/creator
on TenantAware entities.

## Views

The bundled TenantController expects to find views under:

 * /resources/views/tenant
 * /resources/views/tenant/error

These are not included as they require application implementation. The TenantController class
has information about file names and route mappings.

In multi-site, these will need placing in appropriate sub-folders / duplicating where
necessary.

## Potential Issues

Working with multi-tenancy can be very complex. This library works on a shared database, not
individual databases, however you could setup specific databases based on the tenant if
necessary (if you are comfortable with multiple connections / definitions in Doctrine).

When creating repositories always ensure that tenant aware / non-tenant aware are clearly
marked to avoid using the wrong type in the wrong context. Best case: you don't see anything,
worst case - you see everything unfiltered.

You will note that in this system there are no magic SQL filters pre-applied through Doctrines
DQL filters: this is deliberate. You should be able to switch the tenancy easily at any point
and this can be done by simply updating the Tenant instance, or using the non-tenant aware
repository.

Additionally: none of the tenant ids are references to other objects. Again this is very
deliberate. It allows e.g. customer data to be in a separate database to your users and makes
it a lot more portable.

Using tenancy will add an amount of overhead to your application. How much will depend on
how much data you have and the security model you apply.

Always test and have functional tests to ensure that the tenancy is applied correctly and
whenever in doubt: **always** deny rather than grant access.

## Links

 * [Laravel Doctrine](http://laraveldoctrine.org)
 * [Laravel](http://laravel.com)
 * [Doctrine](http://doctrine-project.org)