<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace Somnambulist\Tenancy\Http\Middleware;

use Somnambulist\Tenancy\Contracts\BelongsToTenant as BelongsToTenantContract;
use Somnambulist\Tenancy\Contracts\Tenant as TenantContract;
use Somnambulist\Tenancy\Contracts\TenantParticipant as TenantParticipantContract;
use Somnambulist\Tenancy\TenantParticipantRepository;
use Closure;
use Illuminate\Contracts\Auth\Guard;

/**
 * Class AuthenticateTenant
 *
 * @package    Somnambulist\Tenancy\Http\Middleware
 * @subpackage Somnambulist\Tenancy\Http\Middleware\AuthenticateTenant
 * @author     Dave Redfern
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



    /**
     * Create a new filter instance.
     *
     * @param Guard                       $auth
     * @param TenantParticipantRepository $repository
     */
    public function __construct(Guard $auth, TenantParticipantRepository $repository)
    {
        $this->auth       = $auth;
        $this->repository = $repository;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $owner  = $creator = null;
        /** @var TenantContract $tenant */
        $tenant = app('auth.tenant');

        /** @var TenantParticipantContract $owner */
        if (null !== $tenantOwnerId = $request->route('tenant_owner_id')) {
            if ($tenant->getTenantOwnerId() && $tenantOwnerId != $tenant->getTenantOwnerId()) {
                throw new \RuntimeException(
                    sprintf(
                        'tenant_owner_id "%s" in route parameters does not match the resolved owner "%s"',
                        $tenantOwnerId, $tenant->getTenantOwnerId()
                    )
                );
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
            throw new \RuntimeException(
                sprintf('Authenticatable User entity does not implement BelongsToTenant contract.')
            );
        }

        if (!$creator || !$user->belongsToTenant($creator)) {
            return redirect()->route('tenant.access_denied');
        }
        if ($owner && $creator->getTenantOwner() !== $owner) {
            return redirect()->route('tenant.invalid_tenant_hierarchy');
        }

        // bind resolved tenant data to container
        $tenant->updateTenancy($user, $creator->getTenantOwner(), $creator);

        return $next($request);
    }
}
