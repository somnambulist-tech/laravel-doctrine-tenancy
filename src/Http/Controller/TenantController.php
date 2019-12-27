<?php

namespace Somnambulist\Tenancy\Http\Controller;

use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

/**
 * Class TenantController
 *
 * Extend this class or implement your own. The protected view methods can be safely
 * overridden to use whatever template naming scheme you like.
 *
 * The route that matches each action is defined on each action method.
 *
 * Note: this controller should be wrapped in the Auth middleware.
 *
 * @package    Somnambulist\Tenancy\Http\Controller
 * @subpackage Somnambulist\Tenancy\Http\Controller\TenantController
 */
abstract class TenantController extends Controller
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Route: tenant.select_tenant
     *
     * @return Factory|View
     */
    public function selectTenantAction()
    {
        return view($this->getSelectTenantView(), ['user' => auth()->user()]);
    }

    /**
     * Route: tenant.no_tenants
     *
     * @return Factory|View
     */
    public function noTenantsAvailableAction()
    {
        return view($this->getNoTenantsAvailableView(), ['user' => auth()->user()]);
    }

    /**
     * Route: tenant.access_denied
     *
     * @return Factory|View
     */
    public function accessDeniedAction()
    {
        return view($this->getAccessDeniedView(), ['user' => auth()->user()]);
    }

    /**
     * Route: tenant.invalid_tenant_hierarchy
     *
     * @return Factory|View
     */
    public function invalidHierarchyAction()
    {
        return view($this->getInvalidHierarchyView(), ['user' => auth()->user()]);
    }

    /**
     * Route: tenant.tenant_type_not_supported
     *
     * @param Request $request
     *
     * @return Factory|View
     */
    public function tenantTypeNotSupportedAction(Request $request)
    {
        return view($this->getTenantTypeNotSupportedView(), [
            'user' => auth()->user(),
            'type' => $request->get('type', 'Not Defined'),
        ]);
    }



    /**
     * View to render when selecting a tenant
     *
     * @return string
     */
    protected function getSelectTenantView()
    {
        return 'tenant/select_tenant';
    }

    /**
     * View to render when the current user has no tenants available
     *
     * @return string
     */
    protected function getNoTenantsAvailableView()
    {
        return 'tenant/error/no_tenants';
    }

    /**
     * View to render when the current user does not have access to the tenant
     *
     * @return string
     */
    protected function getAccessDeniedView()
    {
        return 'tenant/error/access_denied';
    }

    /**
     * View to render when the type of the tenant has been rejected by EnsureTenantType
     *
     * @return string
     */
    protected function getTenantTypeNotSupportedView()
    {
        return 'tenant/error/type_not_supported';
    }

    /**
     * View to render when the User tries to access a tenant with an invalid owner
     *
     * @return string
     */
    protected function getInvalidHierarchyView()
    {
        return 'tenant/error/invalid_hierarchy';
    }
}
