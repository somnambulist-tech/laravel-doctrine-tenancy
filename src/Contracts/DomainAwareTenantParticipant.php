<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Contracts;

/**
 * Interface DomainAwareTenantParticipant
 *
 * @package    Somnambulist\Tenancy\Contracts
 * @subpackage Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipant
 */
interface DomainAwareTenantParticipant extends TenantParticipant
{

    /**
     * @return DomainAwareTenantParticipant
     */
    public function getTenantOwner();

    /**
     * @return string
     */
    public function getDomain();

}
