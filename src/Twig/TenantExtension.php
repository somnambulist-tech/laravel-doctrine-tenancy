<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Twig;

use Somnambulist\Tenancy\Contracts\Tenant as TenantContract;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class TenantExtension
 *
 * Tenant aware twig functions / filters that will ensure the current tenant information
 * is embedded into the request.
 *
 * @package    Somnambulist\Tenancy\Twig
 * @subpackage Somnambulist\Tenancy\Twig\TenantExtension
 */
class TenantExtension extends AbstractExtension
{

    /**
     * @var TenantContract
     */
    protected $tenant;

    public function __construct(TenantContract $tenant)
    {
        $this->tenant = $tenant;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('current_tenant_owner_id', [$this->tenant, 'getTenantOwnerId']),
            new TwigFunction('current_tenant_creator_id', [$this->tenant, 'getTenantCreatorId']),
            new TwigFunction('current_tenant_owner', [$this->tenant, 'getTenantOwner']),
            new TwigFunction('current_tenant_creator', [$this->tenant, 'getTenantCreator']),
            new TwigFunction('current_tenant_security_model', [$this->tenant, 'getSecurityModel']),
        ];
    }
}
