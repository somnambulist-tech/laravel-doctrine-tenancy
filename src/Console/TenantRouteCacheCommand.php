<?php

namespace Somnambulist\Tenancy\Console;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;
use Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipantRepository as DomainRepository;
use Somnambulist\Tenancy\Http\Middleware\TenantRouteResolver;

/**
 * Class TenantRouteCacheCommand
 *
 * @package    Somnambulist\Tenancy\Console
 * @subpackage Somnambulist\Tenancy\Console\TenantRouteCacheCommand
 */
class TenantRouteCacheCommand extends AbstractTenantCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'tenant:route:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a route cache for the specified tenant.';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;



    /**
     * Constructor.
     *
     * @param DomainRepository    $repository
     * @param Router              $router
     * @param TenantRouteResolver $resolver
     * @param Filesystem          $files
     */
    public function __construct(DomainRepository $repository, Router $router, TenantRouteResolver $resolver, FileSystem $files)
    {
        parent::__construct($repository, $router, $resolver);

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $domain = $this->argument('domain');
        $this->call('tenant:route:clear', ['domain' => $domain]);

        $routes = $this->getFreshApplicationRoutes($domain);

        if (count($routes) == 0) {
            $this->error("The specified tenant does not have any routes.");

            return;
        }

        foreach ($routes as $route) {
            $route->prepareForSerialization();
        }

        $this->files->put(
            $this->laravel->getCachedRoutesPath(),
            $this->buildRouteCacheFile($routes)
        );

        $this->info('Tenant routes cached successfully!');
    }

    /**
     * Boot a fresh copy of the application and get the routes.
     *
     * @param string $domain
     *
     * @return RouteCollection
     */
    protected function getFreshApplicationRoutes($domain)
    {
        return $this->resolveTenantRoutes($domain);
    }

    /**
     * Build the route cache file.
     *
     * @param RouteCollection $routes
     *
     * @return string
     */
    protected function buildRouteCacheFile(RouteCollection $routes)
    {
        $stub = $this->files->get(__DIR__ . '/stubs/routes.stub');

        return str_replace('{{routes}}', base64_encode(serialize($routes)), $stub);
    }
}
