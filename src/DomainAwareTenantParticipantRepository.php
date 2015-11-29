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
 * @subpackage Somnambulist\Tenancy\DomainAwareTenantParticipantRepository
 * @author     Dave Redfern
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