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

use Illuminate\Config\Repository;
use Somnambulist\Tenancy\Console;
use Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipantRepository as DomainTenantRepositoryContract;
use Somnambulist\Tenancy\Contracts\TenantParticipantRepository as TenantRepositoryContract;
use Somnambulist\Tenancy\Contracts\Tenant as TenantContract;
use Somnambulist\Tenancy\Entities\NullTenant;
use Somnambulist\Tenancy\Entities\NullUser;
use Somnambulist\Tenancy\Entities\Tenant;
use Somnambulist\Tenancy\Foundation\TenantAwareApplication;
use Somnambulist\Tenancy\Http\TenantRedirectorService;
use Somnambulist\Tenancy\Repositories\DomainAwareTenantParticipantRepository;
use Somnambulist\Tenancy\Repositories\TenantParticipantRepository;
use Somnambulist\Tenancy\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;
use Somnambulist\Tenancy\Services\TenantTypeResolver;
use Somnambulist\Tenancy\View\FileViewFinder;

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

        /** @var Repository $config */
        $config = $this->app->make('config');

        if (
            !$config->get('tenancy.multi_account.enabled', false) &&
            !$config->get('tenancy.multi_site.enabled', false)
        ) {
            return;
        }

        $this->registerTenantCoreServices($config);
        $this->registerTenantAwareViewFinder($config);
        $this->registerTenantAwareUrlGenerator($config);

        $this->registerMultiAccountTenancy($config);
        $this->registerMultiSiteTenancy($config);
        $this->registerTenantAwareRepositories($config);
    }



    /**
     * Merge config
     */
    protected function mergeConfig()
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'tenancy');
    }

    /**
     * Registers the core Tenant services
     *
     * @param Repository $config
     *
     * @return void
     */
    protected function registerTenantCoreServices(Repository $config)
    {
        if (!$this->app->resolved(TenantContract::class)) {
            // might be registered by TenantAwareApplication already
            $this->app->singleton(TenantContract::class, function ($app) {
                return new Tenant(new NullUser(), new NullTenant(), new NullTenant());
            });

            $this->app->alias(TenantContract::class, 'auth.tenant');
        }

        $this->app->singleton(TenantRedirectorService::class, function ($app) {
            return new TenantRedirectorService();
        });
        $this->app->singleton(TenantTypeResolver::class, function ($app) {
            return new TenantTypeResolver();
        });

        /* Aliases */
        $this->app->alias(TenantRedirectorService::class, 'auth.tenant.redirector');
        $this->app->alias(TenantTypeResolver::class,      'auth.tenant.type_resolver');
    }

    /**
     * Re-register the view finder with one that allows manipulating the paths array
     *
     * @param Repository $config
     *
     * @return void
     */
    protected function registerTenantAwareViewFinder(Repository $config)
    {
        $this->app->bind('view.finder', function ($app) use ($config) {
            $paths = $config['view.paths'];

            return new FileViewFinder($app['files'], $paths);
        });
    }

    /**
     * Register the URL generator service.
     *
     * Copy of the Laravel URL generator registering steps
     *
     * @param Repository $config
     *
     * @return void
     */
    protected function registerTenantAwareUrlGenerator(Repository $config)
    {
        $this->app['url'] = $this->app->share(
            function ($app) {
                $routes = $app['router']->getRoutes();

                $app->instance('routes', $routes);

                $url = new UrlGenerator(
                    $routes, $app->rebinding('request', $this->requestRebinder()), $app['auth.tenant']
                );

                $url->setSessionResolver(function () {
                    return $this->app['session'];
                });

                $app->rebinding('routes', function ($app, $routes) {
                    $app['url']->setRoutes($routes);
                });

                return $url;
            }
        );
    }

    /**
     * Set-up multi-account tenancy services
     *
     * @param Repository $config
     */
    protected function registerMultiAccountTenancy(Repository $config)
    {
        if (!$config->get('tenancy.multi_account.enabled', false)) {
            return;
        }

        $this->registerMultiAccountParticipantRepository($config);
        $this->registerTenantParticipantMappings($config->get('tenancy.multi_account.participant.mappings'));
    }

    /**
     * Set-up and check the app for multi-site tenancy
     *
     * @param Repository $config
     */
    protected function registerMultiSiteTenancy(Repository $config)
    {
        if (!$config->get('tenancy.multi_site.enabled', false)) {
            return;
        }

        if (!$this->app instanceof TenantAwareApplication) {
            throw new \RuntimeException(
                'Multi-site requires updating your bootstrap/app.php to use TenantAwareApplication'
            );
        }

        /*
         * @todo Need a way to detect if RouteServiceProvider (or at least an instance of
         *       Foundation\RouteServiceProvider) has been registered and to fail with a
         *       warning if so.
         */

        $this->registerMultiSiteParticipantRepository($config);
        $this->registerMultiSiteConsoleCommands();
        $this->registerTenantParticipantMappings($config->get('tenancy.multi_site.participant.mappings'));
    }



    /**
     * Register the participant mapping aliases
     *
     * @param array $mappings
     *
     * @return void
     */
    protected function registerTenantParticipantMappings(array $mappings = [])
    {
        $resolver = $this->app->make('auth.tenant.type_resolver');
        foreach ($mappings as $alias => $class) {
            $resolver->addMapping($alias, $class);
        }
    }

    /**
     * Register the main tenant participant repository
     *
     * @param Repository $config
     */
    protected function registerMultiAccountParticipantRepository(Repository $config)
    {
        $repository = $config->get('tenancy.multi_account.participant.repository');
        $entity     = $config->get('tenancy.multi_account.participant.class');

        $this->app->singleton(TenantRepositoryContract::class, function ($app) use ($repository, $entity) {
            return new TenantParticipantRepository(
                new $repository($app['em'], $app['em']->getClassMetaData($entity))
            );
        });

        $this->app->alias(TenantRepositoryContract::class, 'auth.tenant.account_repository');
    }

    /**
     * Register the main tenant participant repository
     *
     * @param Repository $config
     */
    protected function registerMultiSiteParticipantRepository(Repository $config)
    {
        $repository = $config->get('tenancy.multi_site.participant.repository');
        $entity     = $config->get('tenancy.multi_site.participant.class');

        $this->app->singleton(DomainTenantRepositoryContract::class,
            function ($app) use ($repository, $entity) {
                return new DomainAwareTenantParticipantRepository(
                    new $repository($app['em'], $app['em']->getClassMetaData($entity))
                );
            }
        );

        $this->app->alias(DomainTenantRepositoryContract::class, 'auth.tenant.site_repository');
    }

    /**
     * Register console commands
     */
    protected function registerMultiSiteConsoleCommands()
    {
        $this->commands([
            Console\TenantListCommand::class,
            Console\TenantRouteListCommand::class,
            Console\TenantRouteCacheCommand::class,
            Console\TenantRouteClearCommand::class,
        ]);
    }

    /**
     * Register any bound tenant aware repositories
     *
     * @return void
     */
    protected function registerTenantAwareRepositories(Repository $config)
    {
        foreach ($config->get('tenancy.doctrine.repositories', []) as $details) {
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
     * @return string
     */
    protected function getConfigPath()
    {
        return __DIR__ . '/../config/tenancy.php';
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
            'auth.tenant.account_repository',
            'auth.tenant.site_repository',
        ];
    }
}