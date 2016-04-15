Change Log
==========

2016-04-14
----------

Changed:

 * Added compiles() to Provider to allow core files to be compiled in production
 * Fixed TenantAwareApplication checks for creator domain and fallsback to owner domain if not set

2016-02-21
----------

Changed:

 * SecurityModel returns value on cast to string instead of key name

2016-02-20
----------

Changed:

 * Fixed TenantRouteResolver not checking for tenant creator specific routes

2016-01-30
----------

Changed:

 * Fixed TenantRouteListCommand as Laravel 5.2 removed deprecated method call.

2016-01-10
----------

Changed:

 * Added support for Laravel 5.2
 * Fixed bug in User security model using wrong interface and method call
 * Fixed interface definition missing order by parameter
 * Fixed FileViewFinder checks for location before adding (2015-12-05)
 * Fixed TenantController to use Framework controller and not AppController (2015-12-05)
 * Fixed AuthenticateTenant middleware to use abort() instead of raising runtime exception (2015-12-05)
 * Fixed tenant parameters should be removed from route params (2015-12-13)

2015-12-03
----------

Changed:

 * Cleaned up folder / file structure
   * Moved repository classes into Repositories namespace
   * Moved Redirector service into Http namespace
   * Moved Entity -> Entities
   * Moved TenantSecurityModel -> Entities\SecurityModel
   * Moved TenantTypeResolver to Services namespace
   * Moved TenantImporterEventSubscriber -> EventSubscribers\EntityOwnerEventSubscriber
 * Changed TenantSiteResolver to better handle replacing FileViewFinder

Added:

 * Custom FileViewFinder that adds a prependLocation

2015-12-02
----------

Changed:

 * Config structures re-done to be more explicit / consistent
   * tenancy.multi_account
   * tenancy.multi_site
   * tenancy is by default disabled and must be enabled
 * Moved app.route. config to tenancy.multi_site.router as it is multi-site config
 * Moved tenancy.ignorable_domain_components into multi_site config
 * Refactored TenancyServiceProvider to actively check the Application instance
 * Refactored TwigExtension to remove TenantParticipantRepository
 * Renamed aliases of tenant repositories:
   * auth.tenant.participant_repository => auth.tenant.account_repository
   * auth.tenant.domain_participant_repository => auth.tenant.site_repository
 * Removed automatic Twig extension loading as it was causing problems


2015-11-30
----------

Changed:

 * Added missing composer dependency on Laravel 5.1+
 * Minor docblock comment alterations for better IDE auto-completion
 * Fix incorrect license details, this library is MIT licensed.

Added:

 * Tenant console commands for listing tenants and handling routes
   * tenant:list
   * tenant:route:list
   * tenant:route:cache
   * tenant:route:clear
 * TenantRouteResolver for importing routes

2015-11-29
----------

Changed:

 * Removed hard dependencies on Doctrine interfaces
   * Eloquent models should now be easily wrapped
   * Doctrine is still a required dependency owing to the traits / event subscriber

Added:

 * Initial work on multi-site tenancy
   * highly experimental requires a number of under-the-hood "hacks"
   * requires TenantAwareApplication to replace the Laravel Application

2015-11-26
----------

Initial commit.