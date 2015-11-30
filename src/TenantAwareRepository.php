<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace Somnambulist\Tenancy;

use Somnambulist\Tenancy\Contracts\BelongsToTenant;
use Somnambulist\Tenancy\Contracts\Tenant as TenantContract;
use Somnambulist\Tenancy\Contracts\TenantAware as TenantAwareContract;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class TenantAwareRepository
 *
 * Prepends the current tenant information into queries that are dispatched to the
 * repository. The passed EntityRepository must be for an entity that implements
 * the TenantAwareContract.
 *
 * The rules for tenancy can be overridden depending on the defined Security Model.
 * There are three basic rules already implemented:
 *
 *  * shared - all data within the tenant owner is shared to all tenant creators
 *  * user - data within the tenant owner is available to the user if they have the creator mapped
 *  * closed - only data owned by the creator within the owner is permitted
 *
 * Additional schemes can be added by extending and implementing a method that contains
 * the security model name capitalised and then prefixed with apply and suffixed by
 * SecurityModel.
 *
 * In the case of "user", the User entity must implement the AccessibleTenants contract
 * otherwise, only the current creator tenant is used.
 *
 * @package    Somnambulist\Tenancy
 * @subpackage Somnambulist\Tenancy\TenantAwareRepository
 * @author     Dave Redfern
 */
abstract class TenantAwareRepository implements ObjectRepository
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var TenantContract
     */
    protected $tenant;



    /**
     * Constructor.
     *
     * @param EntityManager    $em
     * @param EntityRepository $repository
     * @param TenantContract   $tenant
     */
    public function __construct(EntityManager $em, EntityRepository $repository, TenantContract $tenant)
    {
        $this->em         = $em;
        $this->repository = $repository;
        $this->tenant     = $tenant;
        $repoClass        = $this->repository->getClassName();

        if ( !in_array(TenantAwareContract::class, class_implements($repoClass)) ) {
            throw new \RuntimeException(
                sprintf('Class "%s" does not implement "%s"', $repoClass, TenantAwareContract::class)
            );
        }
    }

    /**
     * For consistency, allow standard calls, but fail for everything
     *
     * @param $method
     * @param $arguments
     *
     * @throws ORMException
     */
    public function __call($method, $arguments)
    {
        throw ORMException::invalidFindByCall($this->getClassName(), '*', $method);
    }

    /**
     * Creates a new QueryBuilder instance that is prepopulated for this entity name.
     *
     * @param string $alias
     * @param string $indexBy The index for the from.
     *
     * @return QueryBuilder
     */
    public function createQueryBuilder($alias, $indexBy = null)
    {
        $qb = $this->repository->createQueryBuilder($alias, $indexBy);

        $this->applySecurityModel($qb, $alias);

        return $qb;
    }

    /**
     * Applies the rules for the selected Security Model
     *
     * The security model name is capitalised, and then turned into a method prefixed with
     * apply and suffixed with SecurityModel e.g.: shared -> applySharedSecurityModel.
     *
     * @param QueryBuilder $qb
     * @param string       $alias
     */
    protected function applySecurityModel(QueryBuilder $qb, $alias)
    {
        $model  = $this->tenant->getTenantSecurityModel();
        $method = 'apply' . ucfirst($model) . 'SecurityModel';

        if ( method_exists($this, $method) ) {
            $this->$method($qb, $alias);
        } else {
            throw new \RuntimeException(
                sprintf('Security model "%s" has not been implemented by "%s"', $model, $method)
            );
        }
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $alias
     */
    protected function applySharedSecurityModel(QueryBuilder $qb, $alias)
    {
        $qb
            ->where("{$alias}.tenantOwnerId = :tenantOwnerId")
            ->setParameters([
                ':tenantOwnerId' => $this->tenant->getTenantOwnerId(),
            ])
        ;
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $alias
     */
    protected function applyUserSecurityModel(QueryBuilder $qb, $alias)
    {
        $user = $this->tenant->getUser();
        if ($user instanceof BelongsToTenant) {
            $tenants = $user->getAccessibleTenants();
        } else {
            $tenants = new ArrayCollection([$this->tenant->getTenantCreator()]);
        }

        $qb
            ->where("{$alias}.tenantOwnerId = :tenantOwnerId")
            ->andWhere("{$alias}.tenantCreatorId IN (:tenantCreators)")
            ->setParameters([
                ':tenantOwnerId' => $this->tenant->getTenantOwnerId(),
                ':tenantCreators' => $tenants,
            ])
        ;
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $alias
     */
    protected function applyClosedSecurityModel(QueryBuilder $qb, $alias)
    {
        $qb
            ->where("{$alias}.tenantOwnerId = :tenantOwnerId")
            ->andWhere("{$alias}.tenantCreatorId = :tenantCreatorId")
            ->setParameters([
                ':tenantOwnerId'   => $this->tenant->getTenantOwnerId(),
                ':tenantCreatorId' => $this->tenant->getTenantCreatorId(),
            ])
        ;
    }

    /**
     * Finds an object by its primary key / identifier.
     *
     * @todo Might have performance issues since we are accessing EM to get class
     *       meta data to reference the correct primary identifier.
     *
     * @todo Allow composite primary key look up?
     *
     * @param mixed $id The identifier.
     *
     * @return object|null The object.
     */
    public function find($id)
    {
        $qb   = $this->createQueryBuilder('o');
        $meta = $this->em->getClassMetadata($this->repository->getClassName());

        $qb->andWhere("o.{$meta->getSingleIdentifierFieldName()} = :id")->setParameter(':id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Finds all objects in the repository.
     *
     * @return array The objects.
     */
    public function findAll()
    {
        return $this->findBy([]);
    }

    /**
     * Finds objects by a set of criteria.
     *
     * Optionally sorting and limiting details can be passed. An implementation may throw
     * an UnexpectedValueException if certain values of the sorting or limiting details are
     * not supported.
     *
     * @param array      $criteria
     * @param array|null $orderBy  ['field', 'ASC|DESC']
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return array The objects.
     *
     * @throws \UnexpectedValueException
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('o');
        if (null !== $offset) $qb->setFirstResult($offset);
        if (null !== $limit)  $qb->setMaxResults($limit);

        $this->applyCriteriaAndOrderByToQueryBuilder($qb, $criteria, $orderBy);

        return $qb->getQuery()->getResult();
    }

    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array      $criteria
     * @param array|null $orderBy  ['field', 'ASC|DESC']
     *
     * @return object|null The entity instance or NULL if the entity can not be found.
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        $qb = $this->createQueryBuilder('o');

        $this->applyCriteriaAndOrderByToQueryBuilder($qb, $criteria, $orderBy);

        return $qb->getQuery()->getOneOrNullResult();
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



    /**
     * @param QueryBuilder $qb
     * @param array        $criteria
     * @param array        $orderBy
     */
    protected function applyCriteriaAndOrderByToQueryBuilder(QueryBuilder $qb, array $criteria, array $orderBy = null)
    {
        if (null !== $orderBy) {
            $qb->addOrderBy('o.' . $orderBy[0], $orderBy[1]);
        }

        foreach ($criteria as $key => $value) {
            if (is_array($value)) {
                $qb->andWhere($qb->expr()->in("o.{$key}", $value));
            } else {
                $qb->andWhere("o.{$key} = :{$key}_value")->setParameter(":{$key}_value", $value);
            }
        }
    }
}