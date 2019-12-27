<?php

namespace Somnambulist\Tenancy\Contracts;

/**
 * Interface BelongsToTenantParticipants
 *
 * @package    Somnambulist\Tenancy\Contracts
 * @subpackage Somnambulist\Tenancy\Contracts\BelongsToTenantParticipants
 */
interface BelongsToTenantParticipants
{

    /**
     * @return TenantParticipant[]
     */
    public function getTenantParticipants();
}
