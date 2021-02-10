<?php declare(strict_types=1);

namespace Somnambulist\Tenancy;

use Illuminate\Config\Repository;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use RuntimeException;
use Somnambulist\Tenancy\Console;
use Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipantRepository as DomainTenantRepositoryContract;
use Somnambulist\Tenancy\Contracts\Tenant as TenantContract;
use Somnambulist\Tenancy\Contracts\TenantParticipantRepository as TenantRepositoryContract;
use Somnambulist\Tenancy\Entities\NullTenant;
use Somnambulist\Tenancy\Entities\NullUser;
use Somnambulist\Tenancy\Entities\Tenant;
use Somnambulist\Tenancy\Foundation\TenantAwareApplication;
use Somnambulist\Tenancy\Http\TenantRedirectorService;
use Somnambulist\Tenancy\Repositories\DomainAwareTenantParticipantRepository;
use Somnambulist\Tenancy\Repositories\TenantParticipantRepository;
use Somnambulist\Tenancy\Routing\UrlGenerator;
use Somnambulist\Tenancy\Services\TenantTypeResolver;
use Somnambulist\Tenancy\View\FileViewFinder;

/**
 * Class TenancyServiceProvider
 *
 * @package    Somnambulist\Tenancy\Providers
 * @subpackage Somnambulist\Tenancy\Providers\TenancyServiceProvider
 */
class TenancyServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([$this->getConfigPath() => config_path('tenancy.php'),], 'config');
    }

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
        $this->registerMultiAccountTenancy($config);
        $this->registerMultiSiteTenancy($config);
        $this->registerTenantAwareRepositories($config);
    }

    protected function mergeConfig()
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'tenancy');
    }

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
        $this->app->alias(TenantTypeResolver::class, 'auth.tenant.type_resolver');
    }

    protected function registerMultiAccountTenancy(Repository $config)
    {
        if (!$config->get('tenancy.multi_account.enabled', false)) {
            return;
        }

        $this->registerMultiAccountParticipantRepository($config);
        $this->registerTenantParticipantMappings($config->get('tenancy.multi_account.participant.mappings'));
    }

    protected function registerMultiSiteTenancy(Repository $config)
    {
        if (!$config->get('tenancy.multi_site.enabled', false)) {
            return;
        }

        if (!$this->app instanceof TenantAwareApplication) {
            throw new RuntimeException(
                'Multi-site requires updating your bootstrap/app.php to use TenantAwareApplication'
            );
        }

        /*
         * @todo Need a way to detect if an app RouteServiceProvider is registered as it needs replacing.
         */

        $this->registerMultiSiteParticipantRepository($config);
        $this->registerMultiSiteConsoleCommands();
        $this->registerTenantParticipantMappings($config->get('tenancy.multi_site.participant.mappings'));
    }

    protected function registerTenantParticipantMappings(array $mappings = [])
    {
        $resolver = $this->app->make('auth.tenant.type_resolver');
        foreach ($mappings as $alias => $class) {
            $resolver->addMapping($alias, $class);
        }
    }

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

    protected function registerMultiSiteConsoleCommands()
    {
        $this->commands([
            Console\TenantListCommand::class,
            Console\TenantRouteListCommand::class,
            Console\TenantRouteCacheCommand::class,
            Console\TenantRouteClearCommand::class,
        ]);
    }

    protected function registerTenantAwareRepositories(Repository $config)
    {
        foreach ($config->get('tenancy.doctrine.repositories', []) as $details) {
            if (!isset($details['repository']) && !isset($details['base'])) {
                throw new InvalidArgumentException(
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

    protected function getConfigPath()
    {
        return __DIR__ . '/../config/tenancy.php';
    }
}
