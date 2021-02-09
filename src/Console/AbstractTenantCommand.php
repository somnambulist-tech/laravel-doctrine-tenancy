<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Console;

use Illuminate\Console\Command;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;
use Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipantRepository as DomainRepository;
use Somnambulist\Tenancy\Contracts\Tenant;
use Somnambulist\Tenancy\Entities\NullUser;
use Somnambulist\Tenancy\Http\Middleware\TenantRouteResolver;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class AbstractTenantCommand
 *
 * @package    Somnambulist\Tenancy\Console
 * @subpackage Somnambulist\Tenancy\Console\AbstractTenantCommand
 */
abstract class AbstractTenantCommand extends Command
{

    /**
     * The domain aware tenant repository instance
     *
     * @var DomainRepository
     */
    protected $repository;

    /**
     * The router instance.
     *
     * @var Router
     */
    protected $router;

    /**
     * Tenant route resolver middleware
     *
     * @var TenantRouteResolver
     */
    protected $resolver;

    /**
     * @var RouteCollection
     */
    protected $routes;

    public function __construct(DomainRepository $repository, Router $router, TenantRouteResolver $resolver)
    {
        parent::__construct();

        $this->router     = $router;
        $this->repository = $repository;
        $this->resolver   = $resolver;
    }

    /**
     * @param string $domain
     *
     * @return RouteCollection|null
     */
    protected function resolveTenantRoutes($domain)
    {
        if (null === $dt = $this->repository->findOneByDomain($domain)) {
            $this->error(
                sprintf('No tenant found for "%s", are you sure you entered the correct domain?', $domain)
            );

            return null;
        }

        /** @var Tenant $tenant */
        $tenant = app('auth.tenant');
        $tenant->updateTenancy(new NullUser(), $dt->getTenantOwner(), $dt);

        $this->resolver->boot();

        return $this->routes = $this->router->getRoutes();
    }

    /**
     * Get the console command arguments
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['domain', InputArgument::REQUIRED, 'The tenant domain to display route information for.'],
        ];
    }
}
