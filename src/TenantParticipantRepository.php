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
 * @subpackage Somnambulist\Tenancy\TenantParticipantRepository
 * @author     Dave Redfern
 */
class TenantParticipantRepository implements RepositoryContract
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