<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Contracts;

use UnexpectedValueException;

/**
 * Interface TenantParticipantRepository
 *
 * Borrowed from Doctrine\Common\Persistence\ObjectRepository.
 *
 * @package    Somnambulist\Tenancy\Contracts
 * @subpackage Somnambulist\Tenancy\Contracts\TenantParticipantRepository
 */
interface TenantParticipantRepository
{

    /**
     * Finds an object by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     *
     * @return TenantParticipant The tenant participant object.
     */
    public function find($id);

    /**
     * Finds all objects in the repository.
     *
     * @return array The objects.
     */
    public function findAll();

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
     * @return array The objects.
     *
     * @throws UnexpectedValueException
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * Finds a single object by a set of criteria.
     *
     * @param array $criteria The criteria.
     * @param array $orderBy
     *
     * @return TenantParticipant The tenant participant object.
     */
    public function findOneBy(array $criteria, array $orderBy = null);

    /**
     * Returns the class name of the object managed by the repository.
     *
     * @return string
     */
    public function getClassName();
}
