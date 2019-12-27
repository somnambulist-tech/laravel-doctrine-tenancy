<?php

namespace Somnambulist\Tenancy\Entities;

use Illuminate\Contracts\Auth\Authenticatable;
use Somnambulist\Tenancy\Contracts\Tenant as TenantContract;
use Somnambulist\Tenancy\Contracts\TenantParticipant as TenantParticipantContract;

/**
 * Class AuthTenant
 *
 * @package    Somnambulist\Tenancy\Entities
 * @subpackage Somnambulist\Tenancy\Entities\AuthTenant
 */
class Tenant implements TenantContract
{

    /**
     * @var TenantParticipantContract
     */
    protected $tenantOwner;

    /**
     * @var TenantParticipantContract
     */
    protected $tenantCreator;

    /**
     * @var Authenticatable
     */
    protected $user;



    /**
     * Constructor.
     *
     * @param Authenticatable           $user
     * @param TenantParticipantContract $owner
     * @param TenantParticipantContract $creator
     */
    public function __construct(Authenticatable $user, TenantParticipantContract $owner, TenantParticipantContract $creator)
    {
        $this->updateTenancy($user, $owner, $creator);
    }

    /**
     * Update the tenant details
     *
     * @param Authenticatable           $user
     * @param TenantParticipantContract $owner
     * @param TenantParticipantContract $creator
     *
     * @return $this
     * @internal Should not be called normally
     */
    public function updateTenancy(Authenticatable $user, TenantParticipantContract $owner, TenantParticipantContract $creator)
    {
        $this->user          = $user;
        $this->tenantOwner   = $owner;
        $this->tenantCreator = $creator;

        return $this;
    }



    /**
     * @return integer
     */
    public function getTenantOwnerId()
    {
        return $this->tenantOwner->getId();
    }

    /**
     * @return integer
     */
    public function getTenantCreatorId()
    {
        return $this->tenantCreator->getId();
    }

    /**
     * @return string
     */
    public function getTenantSecurityModel()
    {
        return $this->tenantOwner->getSecurityModel();
    }

    /**
     * @return TenantParticipantContract
     */
    public function getTenantOwner()
    {
        return $this->tenantOwner;
    }

    /**
     * @return TenantParticipantContract
     */
    public function getTenantCreator()
    {
        return $this->tenantCreator;
    }

    /**
     * @return Authenticatable
     */
    public function getUser()
    {
        return $this->user;
    }
}
