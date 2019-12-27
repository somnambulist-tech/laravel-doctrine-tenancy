<?php

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



    /**
     * Constructor.
     *
     * @param ParticipantRepository $repository
     */
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

        $finder = $view->getFinder();
        if ($finder instanceof TenantViewFinder) {
            $paths = $this->appendPathsInFinder($finder, $tenant);
        } else {
            $paths = $this->registerPathsInFinder($view, $finder, $tenant);
        }

        // update app config
        $config->set('app.url', $request->getHost());
        $config->set('view.paths', array_merge((array)$config->get('view.paths'), $paths));

        // bind resolved tenant data to container
        app('auth.tenant')->updateTenancy(new NullUser(), $tenant->getTenantOwner(), $tenant);

        return $next($request);
    }

    /**
     * Prepends the view paths to the FileViewFinder, provided it has been replaced
     *
     * @param TenantViewFinder  $finder
     * @param TenantParticipant $tenant
     *
     * @return array
     */
    protected function appendPathsInFinder(TenantViewFinder $finder, TenantParticipant $tenant)
    {
        $finder->prependLocation($this->createViewPath($tenant->getTenantOwner()->getDomain()));
        $finder->prependLocation($this->createViewPath($tenant->getDomain()));

        return $finder->getPaths();
    }

    /**
     * Registers the view paths by creating a new array and injecting a new FileViewFinder
     *
     * @param Factory           $view
     * @param FileViewFinder    $finder
     * @param TenantParticipant $tenant
     *
     * @return array
     */
    protected function registerPathsInFinder(Factory $view, FileViewFinder $finder, TenantParticipant $tenant)
    {
        $paths = [];
        $this->addPathToViewPaths($paths, $tenant->getDomain());
        $this->addPathToViewPaths($paths, $tenant->getTenantOwner()->getDomain());
        $paths = array_merge($paths, $finder->getPaths());

        $finder = $this->createViewFinder($finder, $paths);

        // replace ViewFinder in ViewManager with new instance with ordered paths
        $view->setFinder($finder);

        return $paths;
    }

    /**
     * Creates a new FileViewFinder, copying the current contents
     *
     * @param FileViewFinder $finder
     * @param array          $paths
     *
     * @return FileViewFinder
     */
    protected function createViewFinder(FileViewFinder $finder, array $paths = [])
    {
        $new = new FileViewFinder(app('files'), $paths, $finder->getExtensions());

        foreach ($finder->getHints() as $namespace => $hints) {
            $new->addNamespace($namespace, $hints);
        }

        return $new;
    }

    /**
     * @param array  $paths
     * @param string $host
     *
     * @return string
     */
    protected function addPathToViewPaths(array &$paths, $host)
    {
        $path = $this->createViewPath($host);

        if ($path && !in_array($path, $paths)) {
            $paths[] = $path;
        }

        return $path;
    }

    /**
     * @param string $host
     *
     * @return string
     */
    protected function createViewPath($host)
    {
        return realpath(base_path('resources/views/' . $host));
    }
}
