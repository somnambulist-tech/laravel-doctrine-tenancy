<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Somnambulist\Tenancy\Contracts\BelongsToTenant as BelongsToTenantContract;
use Somnambulist\Tenancy\Contracts\Tenant as TenantContract;
use Somnambulist\Tenancy\Contracts\TenantParticipant as TenantParticipantContract;
use Somnambulist\Tenancy\Contracts\TenantParticipantRepository;

/**
 * Class AuthenticateTenant
 *
 * @package    Somnambulist\Tenancy\Http\Middleware
 * @subpackage Somnambulist\Tenancy\Http\Middleware\AuthenticateTenant
 */
class AuthenticateTenant
{

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * @var TenantParticipantRepository
     */
    protected $repository;

    public function __construct(Guard $auth, TenantParticipantRepository $repository)
    {
        $this->auth       = $auth;
        $this->repository = $repository;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $owner = $creator = null;
        /** @var TenantContract $tenant */
        $tenant = app('auth.tenant');

        /** @var TenantParticipantContract $owner */
        if (null !== $tenantOwnerId = $request->route('tenant_owner_id')) {
            if ($tenant->getTenantOwnerId() && $tenantOwnerId != $tenant->getTenantOwnerId()) {
                abort(500, sprintf(
                    'Selected tenant_owner_id "%s" in route parameters does not match the resolved owner "%s: %s"',
                    $tenantOwnerId, $tenant->getTenantOwnerId(), $tenant->getTenantOwner()->getName()
                ));
            }

            $owner = $this->repository->find($tenantOwnerId);
        }

        /** @var TenantParticipantContract $creator */
        if (null !== $tenantCreatorId = $request->route('tenant_creator_id')) {
            $creator = $this->repository->find($tenantCreatorId);
        }

        /** @var BelongsToTenantContract $user */
        $user = $this->auth->user();

        if (!$user instanceof BelongsToTenantContract) {
            abort(500, sprintf('The Authenticatable User entity does not implement BelongsToTenant contract.'));
        }

        if (!$creator || !$user->belongsToTenant($creator)) {
            return redirect()->route('tenant.access_denied');
        }
        if ($owner && $creator->getTenantOwner() !== $owner) {
            return redirect()->route('tenant.invalid_tenant_hierarchy');
        }

        // remove the tenant parameters, TenantAware URL generator has access to Tenant
        $request->route()->forgetParameter('tenant_owner_id');
        $request->route()->forgetParameter('tenant_creator_id');

        $tenant->updateTenancy($user, $creator->getTenantOwner(), $creator);

        return $next($request);
    }
}
