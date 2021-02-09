<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Traits;

use Somnambulist\Tenancy\Contracts\BelongsToTenantParticipant;
use Somnambulist\Tenancy\Contracts\BelongsToTenantParticipants;
use Somnambulist\Tenancy\Contracts\TenantParticipant as TenantParticipantContract;

/**
 * Trait AccessibleTenants
 *
 * @package    Somnambulist\Tenancy\Traits
 * @subpackage Somnambulist\Tenancy\Traits\AccessibleTenants
 */
trait BelongsToTenant
{

    /**
     * @param TenantParticipantContract|string|array $tenant
     * @param boolean                                $requireAll
     *
     * @return boolean
     */
    public function belongsToTenant($tenant, $requireAll = false)
    {
        if (is_array($tenant)) {
            foreach ($tenant as $t) {
                $hasTenant = $this->belongsToTenant($t);

                if ($hasTenant && !$requireAll) {
                    return true;
                } elseif (!$hasTenant && $requireAll) {
                    return false;
                }
            }

            return $requireAll;
        } else {
            if ($this instanceof BelongsToTenantParticipant) {
                if (
                    !is_null($this->getTenantParticipant()) &&
                    $this->getTenantName($tenant) === $this->getTenantParticipant()->getName()
                ) {
                    return true;
                }
            }
            if ($this instanceof BelongsToTenantParticipants) {
                foreach ($this->getTenantParticipants() as $t) {
                    if ($this->getTenantName($tenant) === $t->getName()) {
                        return true;
                    }
                }
            }

            return false;
        }
    }

    /**
     * @param TenantParticipantContract|string $tenant
     *
     * @return string
     */
    protected function getTenantName($tenant)
    {
        return $tenant instanceof TenantParticipantContract ? $tenant->getName() : $tenant;
    }
}
