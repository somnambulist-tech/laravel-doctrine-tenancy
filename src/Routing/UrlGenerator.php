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

namespace Somnambulist\Tenancy\Routing;

use Illuminate\Support\Str;
use Somnambulist\Tenancy\Contracts\Tenant as TenantContract;
use Illuminate\Http\Request;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\UrlGenerator as BaseUrlGenerator;
use Illuminate\Support\Arr;

/**
 * Class UrlGenerator
 *
 * @package    Somnambulist\Tenancy\Routing
 * @subpackage Somnambulist\Tenancy\Routing\UrlGenerator
 * @author     Dave Redfern
 */
class UrlGenerator extends BaseUrlGenerator
{

    /**
     * @var TenantContract
     */
    protected $tenant;



    /**
     * Constructor.
     *
     * @param RouteCollection $routes
     * @param Request         $request
     * @param TenantContract  $tenant
     */
    public function __construct(RouteCollection $routes, Request $request, TenantContract $tenant)
    {
        parent::__construct($routes, $request);

        $this->tenant = $tenant;
    }

    /**
     * Replace all of the wildcard parameters for a route path.
     *
     * @param  string  $path
     * @param  array  $parameters
     * @return string
     */
    protected function replaceRouteParameters($path, array &$parameters)
    {
        $this->ensureTenancyInParameters($path, $parameters);

        $path = $this->replaceNamedParameters($path, $parameters);

        $path = preg_replace_callback('/\{.*?\}/', function ($match) use (&$parameters) {
            return (empty($parameters) && !Str::endsWith($match[0], '?}'))
                ? $match[0]
                : array_shift($parameters);
        }, $path);

        return trim(preg_replace('/\{.*?\?\}/', '', $path), '/');
    }

    /**
     * Replace all of the named parameters in the path.
     *
     * @param  string $path
     * @param  array  $parameters
     *
     * @return string
     */
    protected function replaceNamedParameters($path, &$parameters)
    {
        $this->ensureTenancyInParameters($path, $parameters);

        return preg_replace_callback('/\{(.*?)\??\}/', function ($m) use (&$parameters) {
            return isset($parameters[$m[1]]) ? Arr::pull($parameters, $m[1]) : $m[0];
        }, $path);
    }

    /**
     * @param string $path
     * @param array  $parameters
     */
    protected function ensureTenancyInParameters($path, &$parameters)
    {
        if (stripos($path, 'tenant_owner_id') !== false && !isset($parameters['tenant_owner_id'])) {
            $parameters['tenant_owner_id'] = $this->tenant->getTenantOwnerId();
        }
        if (stripos($path, 'tenant_creator_id') !== false && !isset($parameters['tenant_creator_id'])) {
            $parameters['tenant_creator_id'] = $this->tenant->getTenantCreatorId();
        }
    }
}
