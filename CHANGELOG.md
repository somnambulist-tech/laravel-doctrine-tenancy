Change Log
==========

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