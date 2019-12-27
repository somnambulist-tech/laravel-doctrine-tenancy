<?php

namespace Somnambulist\Tenancy\Contracts;

use Somnambulist\Tenancy\Contracts\Tenant as TenantContract;

/**
 * Interface TenantAware
 *
 * @package    Somnambulist\Tenancy\Contracts
 * @subpackage Somnambulist\Tenancy\Contracts\TenantAware
 */
interface TenantAware
{

    /**
     * @param TenantContract $tenant
     *
     * @return $this
     */
    public function importTenancyFrom(TenantContract $tenant);

    /**
     * @return integer
     */
    public function getTenantOwnerId();

    /**
     * @param integer $id
     *
     * @return $this
     */
    public function setTenantOwnerId($id);

    /**
     * @return integer
     */
    public function getTenantCreatorId();

    /**
     * @param integer $id
     *
     * @return $this
     */
    public function setTenantCreatorId($id);
}
