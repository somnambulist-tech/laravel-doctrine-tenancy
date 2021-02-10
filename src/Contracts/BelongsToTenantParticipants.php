<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Contracts;

use Doctrine\Common\Collections\Collection;

/**
 * Interface BelongsToTenantParticipants
 *
 * @package    Somnambulist\Tenancy\Contracts
 * @subpackage Somnambulist\Tenancy\Contracts\BelongsToTenantParticipants
 */
interface BelongsToTenantParticipants
{

    /**
     * @return Collection|TenantParticipant[]
     */
    public function getTenantParticipants();
}
