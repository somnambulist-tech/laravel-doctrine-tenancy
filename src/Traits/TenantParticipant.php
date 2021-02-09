<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Traits;

use Somnambulist\Tenancy\Entities\SecurityModel;

/**
 * Class TenantParticipant
 *
 * A very basic TenantParticipant can use this trait to essentially create a
 * restricted, no data sharing setup. This is included as an example, but is
 * not necessarily practical.
 *
 * @package    Somnambulist\Tenancy\Traits
 * @subpackage Somnambulist\Tenancy\Traits\TenantParticipant
 */
trait TenantParticipant
{

    /**
     * @return TenantParticipant
     */
    public function getTenantOwner()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getSecurityModel()
    {
        return SecurityModel::CLOSED;
    }
}
