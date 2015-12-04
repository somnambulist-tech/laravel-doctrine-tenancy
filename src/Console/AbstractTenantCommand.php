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

use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;
use Illuminate\Console\Command;
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
 * @author     Dave Redfern
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
     * @var \Illuminate\Routing\Router
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



    /**
     * Constructor.
     *
     * @param DomainRepository    $repository
     * @param Router              $router
     * @param TenantRouteResolver $resolver
     */
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
     * @return RouteCollection
     */
    protected function resolveTenantRoutes($domain)
    {
        if (null === $dt = $this->repository->findOneByDomain($domain)) {
            return $this->error(
                sprintf('No tenant found for "%s", are you sure you entered the correct domain?', $domain)
            );
        }

        /** @var Tenant $tenant */
        $tenant = app('auth.tenant');
        $tenant->updateTenancy(new NullUser(), $dt->getTenantOwner(), $dt);

        $this->resolver->boot($this->router);
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