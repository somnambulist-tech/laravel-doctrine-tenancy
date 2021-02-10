<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use RuntimeException;
use Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipant as TenantParticipant;
use Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipantRepository as ParticipantRepository;
use Somnambulist\Tenancy\Entities\NullUser;
use Somnambulist\Tenancy\View\FileViewFinder as TenantViewFinder;
use function app;
use function array_filter;
use function array_merge;
use function array_unique;
use function array_values;
use function base_path;
use function realpath;

/**
 * Class TenantSiteResolver
 *
 * @package    Somnambulist\Tenancy\Http\Middleware
 * @subpackage Somnambulist\Tenancy\Http\Middleware\TenantSiteResolver
 */
class TenantSiteResolver
{

    /**
     * @var ParticipantRepository
     */
    protected $repository;

    public function __construct(ParticipantRepository $repository)
    {
        $this->repository = $repository;
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
        $config = app('config');
        $view   = app('view');
        $domain = str_replace((array)$config->get('tenancy.multi_site.ignorable_domain_components'), '', $request->getHost());
        $tenant = $this->repository->findOneByDomain($domain);

        if (!$tenant instanceof TenantParticipant) {
            throw new RuntimeException(
                sprintf('Unable to resolve host "%s" to valid TenantParticipant.', $domain)
            );
        }

        // update app config
        $config->set('app.url', $request->getHost());
        $config->set('view.paths', array_merge((array)$config->get('view.paths'), $this->registerPathsInFinder($view, $tenant)));

        // bind resolved tenant data to container & set route defaults
        app('auth.tenant')->updateTenancy(new NullUser(), $tenant->getTenantOwner(), $tenant);
        app('url')->defaults([
            'tenant_owner_id'   => app('auth.tenant')->getTenantOwnerId(),
            'tenant_creator_id' => app('auth.tenant')->getTenantCreatorId(),
        ]);

        return $next($request);
    }

    /**
     * Registers the view paths by creating a new array and injecting a new FileViewFinder
     *
     * @param Factory           $view
     * @param TenantParticipant $tenant
     *
     * @return array
     */
    protected function registerPathsInFinder(Factory $view, TenantParticipant $tenant)
    {
        $tenantViewPaths = [
            realpath(base_path('resources/views/' . $tenant->getDomain())),
            realpath(base_path('resources/views/' . $tenant->getTenantOwner()->getDomain())),
        ];

        $paths = array_values(array_unique(array_merge(array_filter($tenantViewPaths), $view->getFinder()->getPaths())));

        $view->getFinder()->setPaths($paths);

        return $paths;
    }
}
