<?php

namespace Somnambulist\Tenancy\Http\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use RuntimeException;
use Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipant;
use Somnambulist\Tenancy\Contracts\Tenant;
use function array_unique;
use function base_path;

/**
 * Class TenantRouteResolver
 *
 * Middleware service that defers route loading until after the tenant has been resolved.
 * This is only needed for multi-site tenants and basically replaces the RouteServiceProvider.
 * This is actually a service provider and directly extends the RouteServiceProvider so
 * that there is a consistent setup of the router.
 *
 * As the routes are now setup via a middleware, some of the configuration has had to be
 * moved out to the config files. The following options have been added to config/tenancy.php:
 *
 *  * multi_site.router.namespace
 *  * multi_site.router.patterns
 *
 * router.namespace sets the namespace for your routes. The default is App\Http\Controller.
 * router.patterns allows repeat patterns to be registered with the router. This is an
 * array of key => patterns.
 *
 * <code>
 * <?php
 * // in config/tenancy.php
 * return [
 *      // other stuff omitted
 *      'multi_site' => [
 *          'router' => [
 *              'namespace' => 'App\Http\Controllers',
 *              'patterns' => [
 *                  'id' => '[0-9]+,
 *              ],
 *          ],
 *      ],
 *
 *      // other stuff omitted
 * ]
 * </code>
 *
 * If the tenant is not a DomainAware participant, the standard routes.php file will be used
 * instead of a site specific file.
 *
 * @package    Somnambulist\Tenancy\Http\Middleware
 * @subpackage Somnambulist\Tenancy\Http\Middleware\TenantRouteResolver
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
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->namespace = $app->make('config')->get('tenancy.multi_site.router.namespace', 'App\Http\Controllers');
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
        $this->boot();

        return $next($request);
    }

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        foreach ($this->app->make('config')->get('tenancy.multi_site.router.patterns', []) as $name => $pattern) {
            $this->app->make('router')->pattern($name, $pattern);
        }

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @param Router $router
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
                $tries    = ['routes', 'web',];
                $failures = [];

                if ($tenant->getTenantOwner() instanceof DomainAwareTenantParticipant) {
                    array_unshift($tries, $tenant->getTenantOwner()->getDomain());
                }
                if ($tenant->getTenantCreator() instanceof DomainAwareTenantParticipant) {
                    array_unshift($tries, $tenant->getTenantCreator()->getDomain());
                }

                $tries = array_unique($tries);

                foreach ($tries as $file) {
                    foreach (['routes', 'app/Http',] as $folder) {
                        $path = base_path(sprintf('%s/%s.php', $folder, $file));

                        if (file_exists($path)) {
                            require_once $path;

                            return;
                        }

                        $failures[] = $path;
                    }
                }

                throw new RuntimeException('No routes found, tried: ' . implode(', ', $failures));
            }
        );
    }
}
