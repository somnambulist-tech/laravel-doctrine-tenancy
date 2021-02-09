<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Repositories;

use Somnambulist\Tenancy\Contracts\TenantParticipant as TenantParticipantContract;
use Somnambulist\Tenancy\Contracts\TenantParticipantRepository as RepositoryContract;

/**
 * Class TenantParticipantRepository
 *
 * Wrapper around the repository that will act as the primary source of tenants for
 * the application. This allows the repository to then be directly injected without
 * having to rely on the Entity Manager and a class resolution look-up.
 *
 * This should be mapped in the TenantRepositoryServiceProvider register call.
 *
 * @package    Somnambulist\Tenancy
 * @subpackage Somnambulist\Tenancy\Repositories\TenantParticipantRepository
 */
class TenantParticipantRepository implements RepositoryContract
{

    /**
     * @var RepositoryContract
     */
    protected $repository;

    public function __construct(RepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Finds an object by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     *
     * @return TenantParticipantContract|null The object.
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Finds all objects in the repository.
     *
     * @return array|TenantParticipantContract[] The objects.
     */
    public function findAll()
    {
        return $this->repository->findAll();
    }

    /**
     * Finds objects by a set of criteria.
     *
     * Optionally sorting and limiting details can be passed. An implementation may throw
     * an UnexpectedValueException if certain values of the sorting or limiting details are
     * not supported.
     *
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return array|TenantParticipantContract[] The objects.
     *
     * @throws \UnexpectedValueException
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Finds a single object by a set of criteria.
     *
     * @param array      $criteria The criteria.
     * @param array|null $orderBy
     *
     * @return TenantParticipantContract The object.
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * Returns the class name of the object managed by the repository.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->repository->getClassName();
    }
}
