<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Console;

use Closure;
use Illuminate\Routing\AbstractRouteCollection;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class TenantRouteListCommand
 *
 * Copy of RouteListCommand, except it is tenant aware allowing the routes for the specified
 * tenant to be shown. Requires that Domain Tenancy is enabled as this is the only form that
 * uses domains to define the routes.
 *
 * @package    Somnambulist\Tenancy\Console
 * @subpackage Somnambulist\Tenancy\Console\TenantRouteListCommand
 */
class TenantRouteListCommand extends AbstractTenantCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'tenant:route:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all registered routes in the specified tenant';

    /**
     * An array of all the registered routes.
     *
     * @var \Illuminate\Routing\RouteCollection
     */
    protected $routes;

    /**
     * The table headers for the command.
     *
     * @var array
     */
    protected $headers = ['Domain', 'Method', 'URI', 'Name', 'Action', 'Middleware'];

    public function handle()
    {
        if (is_null($this->resolveTenantRoutes($this->argument('domain')))) {
            return;
        }

        if (!$this->routes instanceof AbstractRouteCollection) {
            $this->error("The specified tenant does not have any routes.");

            return;
        }

        $this->displayRoutes($this->getRoutes());
    }

    /**
     * Compile the routes into a displayable format.
     *
     * @return array
     */
    protected function getRoutes()
    {
        $routes = collect($this->routes)->map(function ($route) {
            return $this->getRouteInformation($route);
        })->all();

        if ($sort = $this->option('sort')) {
            $routes = $this->sortRoutes($sort, $routes);
        }

        if ($this->option('reverse')) {
            $routes = array_reverse($routes);
        }

        return array_filter($routes);
    }

    /**
     * Get the route information for a given route.
     *
     * @param Route $route
     *
     * @return array
     */
    protected function getRouteInformation(Route $route)
    {
        return $this->filterRoute([
            'host'       => $route->domain(),
            'method'     => implode('|', $route->methods()),
            'uri'        => $route->uri(),
            'name'       => $route->getName(),
            'action'     => $route->getActionName(),
            'middleware' => $this->getMiddleware($route),
        ]);
    }

    /**
     * Sort the routes by a given element.
     *
     * @param string $sort
     * @param array  $routes
     *
     * @return array
     */
    protected function sortRoutes($sort, $routes)
    {
        return Arr::sort($routes, function ($route) use ($sort) {
            return $route[$sort];
        });
    }

    /**
     * Display the route information on the console.
     *
     * @param array $routes
     *
     * @return void
     */
    protected function displayRoutes(array $routes)
    {
        $this->table($this->headers, $routes);
    }

    /**
     * Get before filters.
     *
     * @param Route $route
     *
     * @return string
     */
    protected function getMiddleware($route)
    {
        return collect($route->gatherMiddleware())
            ->map(function ($middleware) {
                return $middleware instanceof Closure ? 'Closure' : $middleware;
            })
            ->implode(',')
        ;
    }

    /**
     * Filter the route by URI and / or name.
     *
     * @param array $route
     *
     * @return array|null
     */
    protected function filterRoute(array $route)
    {
        if (
            ($this->option('name') && !Str::contains($route['name'], $this->option('name'))) ||
            $this->option('path') && !Str::contains($route['uri'], $this->option('path')) ||
            $this->option('method') && !Str::contains($route['method'], $this->option('method'))
        ) {
            return null;
        }

        return $route;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['method', null, InputOption::VALUE_OPTIONAL, 'Filter the routes by method.'],
            ['name', null, InputOption::VALUE_OPTIONAL, 'Filter the routes by name.'],
            ['path', null, InputOption::VALUE_OPTIONAL, 'Filter the routes by path.'],
            ['reverse', 'r', InputOption::VALUE_NONE, 'Reverse the ordering of the routes.'],
            ['sort', null, InputOption::VALUE_OPTIONAL, 'The column (host, method, uri, name, action, middleware) to sort by.', 'uri',],
        ];
    }
}
