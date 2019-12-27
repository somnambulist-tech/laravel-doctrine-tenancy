<?php

namespace Somnambulist\Tenancy\Contracts;

/**
 * Interface TenantParticipant
 *
 * Interface that defines what we need from an entity acting as a tenant.
 * Generally this is some form of unique identifier, a name and then how
 * to get the tenant owner (might be self) and a security model to apply.
 *
 * @package    Somnambulist\Tenancy\Contracts
 * @subpackage Somnambulist\Tenancy\Contracts\TenantParticipant
 */
interface TenantParticipant
{

    /**
     * @return TenantParticipant
     */
    public function getTenantOwner();

    /**
     * @return string
     */
    public function getSecurityModel();
}
