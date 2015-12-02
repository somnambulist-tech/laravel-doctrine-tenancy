Change Log
==========

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