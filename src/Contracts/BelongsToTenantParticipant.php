<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Contracts;

/**
 * Interface BelongsToTenantParticipant
 *
 * @package    Somnambulist\Tenancy\Contracts
 * @subpackage Somnambulist\Tenancy\Contracts\BelongsToTenantParticipant
 */
interface BelongsToTenantParticipant
{

    /**
     * @return TenantParticipant
     */
    public function getTenantParticipant();
}
