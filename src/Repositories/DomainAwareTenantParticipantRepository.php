<?php

namespace Somnambulist\Tenancy\Repositories;

use Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipant as TenantParticipantContract;
use Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipantRepository as RepositoryContract;

/**
 * Class DomainAwareTenantParticipantRepository
 *
 * Wrapper around the repository that will act as the primary source of tenants for
 * the application. This allows the repository to then be directly injected without
 * having to rely on the Entity Manager and a class resolution look-up.
 *
 * This should be mapped in the TenantRepositoryServiceProvider register call.
 *
 * @package    Somnambulist\Tenancy
 * @subpackage Somnambulist\Tenancy\Repositories\DomainAwareTenantParticipantRepository
 */
class DomainAwareTenantParticipantRepository extends TenantParticipantRepository implements RepositoryContract
{

    /**
     * @var RepositoryContract
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param RepositoryContract $repository
     */
    public function __construct(RepositoryContract $repository)
    {
        parent::__construct($repository);
    }

    /**
     * @param string $domain
     *
     * @return null|TenantParticipantContract
     */
    public function findOneByDomain($domain)
    {
        return $this->repository->findOneByDomain($domain);
    }
}
