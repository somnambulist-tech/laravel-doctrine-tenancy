<?php

namespace Somnambulist\Tenancy\Contracts;

/**
 * Interface DomainAwareTenantParticipant
 *
 * Borrowed from Doctrine\Common\Persistence\ObjectRepository.
 *
 * @package    Somnambulist\Tenancy\Contracts
 * @subpackage Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipant
 */
interface DomainAwareTenantParticipantRepository extends TenantParticipantRepository
{

    /**
     * Finds a single object by a set of criteria.
     *
     * Note: this should return NULL if not found.
     *
     * @param string $domain The domain to search for
     *
     * @return null|DomainAwareTenantParticipant The tenant participant object.
     */
    public function findOneByDomain($domain);
}
