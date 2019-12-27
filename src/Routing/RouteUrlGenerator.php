<?php

namespace Somnambulist\Tenancy\Routing;

use Illuminate\Http\Request;
use Illuminate\Routing\RouteUrlGenerator as BaseRouteUrlGenerator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Somnambulist\Tenancy\Contracts\Tenant as TenantContract;

/**
 * Class RouteUrlGenerator
 *
 * @package    Somnambulist\Tenancy\Routing
 * @subpackage Somnambulist\Tenancy\Routing\RouteUrlGenerator
 */
class RouteUrlGenerator extends BaseRouteUrlGenerator
{

    /**
     * @var TenantContract
     */
    protected $tenant;

    /**
     * Constructor.
     *
     * @param TenantContract $tenant
     * @param UrlGenerator   $url
     * @param Request        $request
     */
    public function __construct(TenantContract $tenant, $url, $request)
    {
        parent::__construct($url, $request);

        $this->tenant = $tenant;
    }

    /**
     * Replace all of the wildcard parameters for a route path.
     *
     * @param string $path
     * @param array  $parameters
     *
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
     * @param string $path
     * @param array  $parameters
     *
     * @return string
     */
    protected function replaceNamedParameters($path, &$parameters)
    {
        $this->ensureTenancyInParameters($path, $parameters);

        return preg_replace_callback('/\{(.*?)\??\}/', function ($m) use (&$parameters) {
            if (isset($parameters[$m[1]])) {
                return Arr::pull($parameters, $m[1]);
            } elseif (isset($this->defaultParameters[$m[1]])) {
                return $this->defaultParameters[$m[1]];
            } else {
                return $m[0];
            }
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
