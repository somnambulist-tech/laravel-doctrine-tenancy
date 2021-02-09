<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Foundation;

use Illuminate\Foundation\Application;
use Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipant;
use Somnambulist\Tenancy\Contracts\Tenant as TenantContract;
use Somnambulist\Tenancy\Entities\NullTenant;
use Somnambulist\Tenancy\Entities\NullUser;
use Somnambulist\Tenancy\Entities\Tenant;

/**
 * Class TenantAwareApplication
 *
 * Adds tenant awareness to the core Application so the cache files can be uniquely named.
 *
 * @package    Somnambulist\Tenancy\Foundation
 * @subpackage Somnambulist\Tenancy\Foundation\TenantAwareApplication
 */
class TenantAwareApplication extends Application
{

    /**
     * Create a new Illuminate application instance.
     *
     * In tenant aware apps, the Tenant service needs to be registered extremely early in
     * the application startup cycle. The only guaranteed way to do that is to bind it
     * after the core services in the main Application. Then auth.tenant is available
     * very early on, and the caches will auto-update after resolving the tenant later.
     *
     * @param string|null $basePath
     */
    public function __construct($basePath = null)
    {
        parent::__construct($basePath);

        $this->registerBaseTenantBindings();
    }

    /**
     * Register the root Tenant instance
     *
     * @return void
     */
    protected function registerBaseTenantBindings()
    {
        $this->singleton(TenantContract::class, function ($app) {
            return new Tenant(new NullUser(), new NullTenant(), new NullTenant());
        });

        $this->alias(TenantContract::class, 'auth.tenant');
    }

    /**
     * @return boolean
     */
    public function isMultiSiteTenant()
    {
        return ($this['auth.tenant']->getTenantOwner() instanceof DomainAwareTenantParticipant);
    }

    /**
     * @param string $default
     *
     * @return string
     */
    protected function getTenantCacheName($default)
    {
        if ($this->isMultiSiteTenant()) {
            $creator = $this['auth.tenant']->getTenantCreator()->getDomain();
            $owner   = $this['auth.tenant']->getTenantOwner()->getDomain();

            if ($creator && $creator != $owner) {
                return $creator;
            } else {
                return $owner;
            }
        }

        return $default;
    }

    public function getCachedConfigPath()
    {
        return $this->basePath() . '/bootstrap/cache/' . $this->getTenantCacheName('config') . '.php';
    }

    public function getCachedRoutesPath()
    {
        return $this->basePath() . '/bootstrap/cache/' . $this->getTenantCacheName('routes') . '.php';
    }
}
