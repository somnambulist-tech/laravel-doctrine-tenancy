<?php

namespace Somnambulist\Tenancy\Routing;

use Illuminate\Http\Request;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\UrlGenerator as BaseUrlGenerator;
use Somnambulist\Tenancy\Contracts\Tenant as TenantContract;

/**
 * Class UrlGenerator
 *
 * @package    Somnambulist\Tenancy\Routing
 * @subpackage Somnambulist\Tenancy\Routing\UrlGenerator
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
        if (!$this->routeGenerator) {
            $this->routeGenerator = new RouteUrlGenerator($this->tenant, $this, $this->request);
        }

        return $this->routeGenerator;
    }
}
