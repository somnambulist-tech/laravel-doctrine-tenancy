<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Contracts;

/**
 * Interface BelongsToTenant
 *
 * @package    Somnambulist\Tenancy\Contracts
 * @subpackage Somnambulist\Tenancy\Contracts\BelongsToTenant
 */
interface BelongsToTenant
{

    /**
     * @param TenantParticipant|string $tenant
     * @param boolean                  $requireAll
     *
     * @return boolean
     */
    public function belongsToTenant($tenant, $requireAll = false);
}
