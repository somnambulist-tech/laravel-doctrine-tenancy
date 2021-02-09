<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Entities;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Somnambulist\Tenancy\Contracts\BelongsToTenant as BelongsToTenantContract;
use Somnambulist\Tenancy\Contracts\BelongsToTenantParticipant as BelongsToTenantParticipantContract;
use Somnambulist\Tenancy\Contracts\TenantParticipant;
use Somnambulist\Tenancy\Traits\BelongsToTenant;

/**
 * Class NullUser
 *
 * @package    Somnambulist\Tenancy\Entities
 * @subpackage Somnambulist\Tenancy\Entities\NullUser
 */
class NullUser implements
    AuthenticatableContract,
    BelongsToTenantContract,
    BelongsToTenantParticipantContract
{

    use Authenticatable;
    use BelongsToTenant;

    /**
     * @return TenantParticipant
     */
    public function getTenantParticipant()
    {
        return null;
    }
}
