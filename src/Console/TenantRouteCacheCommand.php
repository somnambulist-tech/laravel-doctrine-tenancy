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
 * @author     Dave Redfern
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
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;



    /**
     * Constructor.
     *
     * @param DomainRepository $repository
     * @param Router $router
     * @param TenantRouteResolver $resolver
     * @param Filesystem $files
     */
    public function __construct(DomainRepository $repository, Router $router, TenantRouteResolver $resolver, FileSystem $files) {
        parent::__construct($repository, $router, $resolver);

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $domain = $this->argument('domain');
        $this->call('tenant:route:clear', ['domain' => $domain]);

        $routes = $this->getFreshApplicationRoutes($domain);

        if (count($routes) == 0) {
            return $this->error("The specified tenant does not have any routes.");
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
     * @return \Illuminate\Routing\RouteCollection
     */
    protected function getFreshApplicationRoutes($domain)
    {
        return $this->resolveTenantRoutes($domain);
    }

    /**
     * Build the route cache file.
     *
     * @param  \Illuminate\Routing\RouteCollection $routes
     *
     * @return string
     */
    protected function buildRouteCacheFile(RouteCollection $routes)
    {
        $stub = $this->files->get(__DIR__ . '/stubs/routes.stub');

        return str_replace('{{routes}}', base64_encode(serialize($routes)), $stub);
    }
}
