<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Tests\Stubs;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Somnambulist\Tenancy\Http\Middleware as TenantMiddleware;

/**
 * Class Kernel
 *
 * @package    App\Http
 * @subpackage App\Bootstrap\Kernel
 */
class Http extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        CheckForMaintenanceMode::class,

        TenantMiddleware\TenantSiteResolver::class,
        TenantMiddleware\TenantRouteResolver::class,
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth.tenant'      => TenantMiddleware\AuthenticateTenant::class,
        'auth.tenant.type' => TenantMiddleware\EnsureTenantType::class,
    ];
}
