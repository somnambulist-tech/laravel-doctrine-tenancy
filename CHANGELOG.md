Change Log
==========

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