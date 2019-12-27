<?php

namespace Somnambulist\Tenancy\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Somnambulist\Tenancy\Contracts\TenantParticipant as TenantParticipantContract;

/**
 * Interface Tenant
 *
 * @package    Somnambulist\Tenancy\Contracts
 * @subpackage Somnambulist\Tenancy\Contracts\Tenant
 */
interface Tenant
{

    /**
     * @param Authenticatable           $user
     * @param TenantParticipantContract $owner
     * @param TenantParticipantContract $creator
     *
     * @return $this
     */
    public function updateTenancy(Authenticatable $user, TenantParticipantContract $owner, TenantParticipantContract $creator);

    /**
     * @return integer
     */
    public function getTenantOwnerId();

    /**
     * @return integer
     */
    public function getTenantCreatorId();

    /**
     * @return string
     */
    public function getTenantSecurityModel();

    /**
     * @return TenantParticipant
     */
    public function getTenantOwner();

    /**
     * @return TenantParticipant
     */
    public function getTenantCreator();

    /**
     * @return Authenticatable
     */
    public function getUser();
}
