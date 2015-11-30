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

namespace Somnambulist\Tenancy;

use Somnambulist\Tenancy\Console;
use Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipantRepository as DomainTenantRepositoryContract;
use Somnambulist\Tenancy\Contracts\TenantParticipantRepository as TenantRepositoryContract;
use Somnambulist\Tenancy\Contracts\Tenant as TenantContract;
use Somnambulist\Tenancy\Entity\NullTenant;
use Somnambulist\Tenancy\Entity\NullUser;
use Somnambulist\Tenancy\Entity\Tenant;
use Somnambulist\Tenancy\Routing\UrlGenerator;
use Somnambulist\Tenancy\Twig\TenantExtension;
use Illuminate\Support\ServiceProvider;

/**
 * Class TenancyServiceProvider
 *
 * @package    Somnambulist\Tenancy\Providers
 * @subpackage Somnambulist\Tenancy\Providers\TenancyServiceProvider
 * @author     Dave Redfern
 */
class TenancyServiceProvider extends ServiceProvider
{

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([ $this->getConfigPath() => config_path('tenancy.php'), ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();

        $this->registerTenantService();
        $this->registerAuthenticatorServices();
        $this->registerUrlGenerator();
        $this->registerTenantParticipantMappings();
        $this->registerTenantParticipantRepository();
        $this->registerDomainAwareTenantParticipantRepository();
        $this->registerTenantAwareRepositories();
        $this->registerTwigExtension();
    }



    /**
     * Merge config
     */
    protected function mergeConfig()
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'tenancy');
    }

    /**
     * Registers the root Tenant instance
     *
     * @return void
     */
    protected function registerTenantService()
    {
        if (!$this->app->resolved(TenantContract::class)) {
            $this->app->singleton(TenantContract::class, function ($app) {
                return new Tenant(new NullUser(), new NullTenant(), new NullTenant());
            });

            $this->app->alias(TenantContract::class, 'auth.tenant');
        }
    }

    /**
     * Register the tenancy authenticator services
     *
     * @return void
     */
    protected function registerAuthenticatorServices()
    {
        $this->app->singleton(TenantRedirectorService::class, function ($app) {
            return new TenantRedirectorService();
        });
        $this->app->singleton(TenantTypeResolver::class, function ($app) {
            return new TenantTypeResolver();
        });

        /* Aliases */
        $this->app->alias(TenantRedirectorService::class, 'auth.tenant.redirector');
        $this->app->alias(TenantTypeResolver::class, 'auth.tenant.type_resolver');
    }

    /**
     * Register the URL generator service.
     *
     * @return void
     */
    protected function registerUrlGenerator()
    {
        $this->app['url'] = $this->app->share(
            function ($app) {
                $routes = $app['router']->getRoutes();

                // The URL generator needs the route collection that exists on the router.
                // Keep in mind this is an object, so we're passing by references here
                // and all the registered routes will be available to the generator.
                $app->instance('routes', $routes);

                $url = new UrlGenerator(
                    $routes, $app->rebinding('request', $this->requestRebinder()), $app['auth.tenant']
                );

                $url->setSessionResolver(function () {
                    return $this->app['session'];
                });

                // If the route collection is "rebound", for example, when the routes stay
                // cached for the application, we will need to rebind the routes on the
                // URL generator instance so it has the latest version of the routes.
                $app->rebinding('routes', function ($app, $routes) {
                    $app['url']->setRoutes($routes);
                });

                return $url;
            }
        );
    }

    /**
     * Register the participant mapping aliases
     *
     * @return void
     */
    protected function registerTenantParticipantMappings()
    {
        $resolver = $this->app->make('auth.tenant.type_resolver');
        foreach ($this->app->make('config')->get('tenancy.participant_mappings', []) as $alias => $class) {
            $resolver->addMapping($alias, $class);
        }
    }

    /**
     * Register the main tenant participant repository
     */
    protected function registerTenantParticipantRepository()
    {
        $repository = $this->app->make('config')->get('tenancy.participant_repository');
        $entity     = $this->app->make('config')->get('tenancy.participant_class');

        $this->app->singleton(TenantRepositoryContract::class, function ($app) use ($repository, $entity) {
            return new TenantParticipantRepository(
                new $repository($app['em'], $app['em']->getClassMetaData($entity))
            );
        });

        $this->app->alias(TenantRepositoryContract::class, 'auth.tenant.participant_repository');
    }

    /**
     * Register the main tenant participant repository
     */
    protected function registerDomainAwareTenantParticipantRepository()
    {
        $repository = $this->app->make('config')->get('tenancy.domain_participant_repository');
        $entity     = $this->app->make('config')->get('tenancy.domain_participant_class');

        if ( $repository && $entity ) {
            $this->app->singleton(DomainTenantRepositoryContract::class,
                function ($app) use ($repository, $entity) {
                    return new DomainAwareTenantParticipantRepository(
                        new $repository($app['em'], $app['em']->getClassMetaData($entity))
                    );
                }
            );

            $this->app->alias(DomainTenantRepositoryContract::class, 'auth.tenant.domain_participant_repository');
        }
    }

    /**
     * Register any bound tenant aware repositories
     *
     * @return void
     */
    protected function registerTenantAwareRepositories()
    {
        foreach ($this->app->make('config')->get('tenancy.repositories', []) as $details) {
            if (!isset($details['repository']) && !isset($details['base'])) {
                throw new \InvalidArgumentException(
                    sprintf('Failed to process tenant repository data: missing repository/base from definition')
                );
            }

            $this->app->singleton($details['repository'], function ($app) use ($details) {
                $class = $details['repository'];
                return new $class($app['em'], $app[$details['base']], $app['auth.tenant']);
            });

            if (isset($details['alias'])) {
                $this->app->alias($details['repository'], $details['alias']);
            }
            if (isset($details['tags'])) {
                $this->app->tag($details['repository'], $details['tags']);
            }
        }
    }

    /**
     * If twig is enabled, register the extension
     *
     * @return void
     */
    protected function registerTwigExtension()
    {
        if ( $this->app->resolved('twig') ) {
            $this->app->singleton(TenantExtension::class, function ($app) {
                return new TenantExtension($app[TenantRepositoryContract::class], $app['auth.tenant']);
            });

            $this->app['twig']->addExtension($this->app[TenantExtension::class]);
        }
    }

    /**
     * Get the URL generator request rebinder.
     *
     * @return \Closure
     */
    protected function requestRebinder()
    {
        return function ($app, $request) {
            $app['url']->setRequest($request);
        };
    }

    /**
     * @return boolean
     */
    protected function hasMultiSiteTenancy()
    {
        $repository = $this->app->make('config')->get('tenancy.domain_participant_repository');
        $entity     = $this->app->make('config')->get('tenancy.domain_participant_class');

        return ($repository && $entity);
    }

    /**
     * @return string
     */
    protected function getConfigPath()
    {
        return __DIR__ . '/../config/tenancy.php';
    }

    /**
     * Register console commands
     */
    protected function registerConsoleCommands()
    {
        if ( $this->hasMultiSiteTenancy() ) {
            $this->commands(
                [
                    Console\TenantListCommand::class,
                    Console\TenantRouteListCommand::class,
                    Console\TenantRouteCacheCommand::class,
                    Console\TenantRouteClearCommand::class,
                ]
            );
        }
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [
            'auth.tenant',
            'auth.tenant.type_resolver',
            'auth.tenant.redirector',
            'auth.tenant.participant_repository',
            'auth.tenant.domain_participant_repository',
        ];
    }
}