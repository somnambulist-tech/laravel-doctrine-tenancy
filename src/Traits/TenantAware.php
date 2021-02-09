<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Traits;

use Somnambulist\Tenancy\Contracts\Tenant as TenantContract;

/**
 * Trait TenantAware
 *
 * @package    Somnambulist\Tenancy\Traits
 * @subpackage Somnambulist\Tenancy\Traits\TenantAware
 */
trait TenantAware
{

    /**
     * @var integer
     */
    protected $tenantOwnerId;

    /**
     * @var integer
     */
    protected $tenantCreatorId;



    /**
     * @param TenantContract $tenant
     *
     * @return $this
     */
    public function importTenancyFrom(TenantContract $tenant)
    {
        $this->setTenantOwnerId($tenant->getTenantOwnerId());
        $this->setTenantCreatorId($tenant->getTenantCreatorId());

        return $this;
    }

    /**
     * @return integer
     */
    public function getTenantOwnerId()
    {
        return $this->tenantOwnerId;
    }

    /**
     * @param integer $id
     *
     * @return $this
     */
    public function setTenantOwnerId($id)
    {
        $this->tenantOwnerId = $id;

        return $this;
    }

    /**
     * @return integer
     */
    public function getTenantCreatorId()
    {
        return $this->tenantCreatorId;
    }

    /**
     * @param integer $id
     *
     * @return $this
     */
    public function setTenantCreatorId($id)
    {
        $this->tenantCreatorId = $id;

        return $this;
    }
}
