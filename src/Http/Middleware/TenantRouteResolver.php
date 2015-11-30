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

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipant;
use Somnambulist\Tenancy\Contracts\Tenant;

/**
 * Class TenantRouteResolver
 *
 * Middleware service that defers route loading until after the tenant has been resolved.
 * This is only needed for multi-site tenants and basically replaces the RouteServiceProvider.
 * This is actually a service provider and directly extends the RouteServiceProvider so
 * that there is a consistent setup of the router.
 *
 * As this is provided as a plugin middleware, the "namespace" property can now be set
 * via a new app config option:
 *
 *  * app.route.namespace
 *
 * Simply add a new section to your config/app.php named 'route' with a sub-key of 'namespace':
 *
 * <code>
 * <?php
 * // in config/app.php
 * return [
 *      'route' => [
 *          'namespace' => 'App\Http\Controllers',
 *      ],
 *      // other stuff omitted
 * ]
 * </code>
 *
 * If the tenant is not a DomainAware participant, the standard routes.php file will be used
 * instead of a site specific file.
 *
 * @package    Somnambulist\Tenancy\Http\Middleware
 * @subpackage Somnambulist\Tenancy\Http\Middleware\TenantRouteResolver
 * @author     Dave Redfern
 */
class TenantRouteResolver extends ServiceProvider
{

    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace;


    /**
     * Constructor.
     *
     * Amazingly we have to override the constructor because the parent is not type
     * hinted so won't dependency inject properly.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->namespace = $app->make('config')->get('app.route.namespace', 'App\Http\Controllers');
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $this->boot($this->app['router']);

        return $next($request);
    }

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param \Illuminate\Routing\Router $router
     *
     * @return void
     */
    public function boot(Router $router)
    {
        parent::boot($router);
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router $router
     *
     * @return void
     */
    public function map(Router $router)
    {
        /** @var Tenant $tenant */
        $tenant = $this->app->make('auth.tenant');
        $router->group(
            ['namespace' => $this->namespace],
            function ($router) use ($tenant) {
                $file = 'routes';

                if ($tenant->getTenantOwner() instanceof DomainAwareTenantParticipant) {
                    $file = $tenant->getTenantOwner()->getDomain();
                }

                require app_path(sprintf('Http/%s.php', $file));
            }
        );
    }
}
