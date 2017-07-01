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

use Somnambulist\Tenancy\Contracts\Tenant as TenantContract;
use Illuminate\Http\Request;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\UrlGenerator as BaseUrlGenerator;

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
     * Get the Route URL generator instance.
     *
     * @return RouteUrlGenerator
     */
    protected function routeUrl()
    {
        if (! $this->routeGenerator) {
            $this->routeGenerator = new RouteUrlGenerator($this->tenant, $this, $this->request);
        }

        return $this->routeGenerator;
    }
}
