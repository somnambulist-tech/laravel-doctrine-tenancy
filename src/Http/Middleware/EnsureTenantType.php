<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Somnambulist\Tenancy\Contracts\Tenant as TenantContract;
use Somnambulist\Tenancy\Services\TenantTypeResolver;

/**
 * Class EnsureTenantType
 *
 * @package    Somnambulist\Tenancy\Http\Middleware
 * @subpackage Somnambulist\Tenancy\Http\Middleware\EnsureTenantType
 */
class EnsureTenantType
{

    /**
     * @var TenantContract
     */
    private $tenant;

    /**
     * @var TenantTypeResolver
     */
    private $typeResolver;

    public function __construct(TenantContract $tenant, TenantTypeResolver $typeResolver)
    {
        $this->tenant       = $tenant;
        $this->typeResolver = $typeResolver;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string  $type
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $type)
    {
        if (!$this->typeResolver->hasType($this->tenant->getTenantCreator(), $type)) {
            return redirect()->route('tenant.tenant_type_not_supported', ['type' => $type]);
        }

        return $next($request);
    }
}
