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

namespace Somnambulist\Tenancy\Http\Middleware;

use Illuminate\View\FileViewFinder;
use Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipantRepository;
use Somnambulist\Tenancy\Contracts\TenantParticipant;
use Somnambulist\Tenancy\Entity\NullUser;

/**
 * Class TenantSiteResolverMiddleware
 *
 * @package    App\Http\Middleware
 * @subpackage App\Http\Middleware\TenantSiteResolverMiddleware
 * @author     Dave Redfern
 */
class TenantSiteResolver
{

    /**
     * @var DomainAwareTenantParticipantRepository
     */
    protected $repository;


    /**
     * Constructor.
     *
     * @param DomainAwareTenantParticipantRepository $repository
     */
    public function __construct(DomainAwareTenantParticipantRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $domain = str_replace(app('config')->get('tenancy.ignorable_domain_components', []), '', $request->getHost());
        $tenant = $this->repository->findOneByDomain($domain);
        $config = app('config');
        $paths  = [];

        if (!$tenant instanceof TenantParticipant) {
            throw new \RuntimeException(
                sprintf('Unable to resolve host "%s" to valid TenantParticipant.', $domain)
            );
        }

        $this->addPathToViewPaths($paths, $domain);
        $this->addPathToViewPaths($paths, $tenant->getTenantOwner()->getDomain());
        $paths = array_merge($paths, $config->get('view.paths', []));

        // update app config
        $config->set('app.url', $request->getHost());
        $config->set('view.paths', $paths);

        // replace ViewFinder in ViewManager with new instance with ordered paths
        app('view')->setFinder(new FileViewFinder(app('files'), $paths));

        // bind resolved tenant data to container
        app('auth.tenant')->updateTenancy(new NullUser(), $tenant->getTenantOwner(), $tenant);

        return $next($request);
    }

    /**
     * @param array  $paths
     * @param string $host
     */
    protected function addPathToViewPaths(array &$paths, $host)
    {
        $path = realpath(base_path('resources/views/' . $host));

        if ($path && !in_array($path, $paths)) {
            $paths[] = $path;
        }
    }
}