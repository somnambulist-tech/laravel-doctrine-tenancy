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

namespace Somnambulist\Tenancy\Twig;

use Somnambulist\Tenancy\Contracts\Tenant as TenantContract;
use Somnambulist\Tenancy\Contracts\TenantAware as TenantAwareContract;
use Somnambulist\Tenancy\Contracts\TenantParticipantRepository;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Class TenantExtension
 *
 * Tenant aware twig functions / filters that will ensure the current tenant information
 * is embedded into the request.
 *
 * @package    Somnambulist\Tenancy\Twig
 * @subpackage Somnambulist\Tenancy\Twig\TenantExtension
 * @author     Dave Redfern
 */
class TenantExtension extends Twig_Extension
{

    /**
     * @var TenantParticipantRepository
     */
    protected $repository;

    /**
     * @var TenantContract
     */
    protected $tenant;

    /**
     * Create a new html extension
     *
     * @param TenantParticipantRepository $repository
     * @param TenantContract              $tenant
     */
    public function __construct(TenantParticipantRepository $repository, TenantContract $tenant)
    {
        $this->repository = $repository;
        $this->tenant     = $tenant;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'App_Service_Tenant_Twig_TenantExtension';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('current_tenant_owner_id',   [$this->tenant, 'getTenantOwnerId']),
            new Twig_SimpleFunction('current_tenant_creator_id', [$this->tenant, 'getTenantCreatorId']),
            new Twig_SimpleFunction('current_tenant_owner',      [$this->tenant, 'getTenantOwner']),
            new Twig_SimpleFunction('current_tenant_creator',    [$this->tenant, 'getTenantCreator']),

            new Twig_SimpleFunction('get_entity_tenant_owner',   [$this, 'getTenantOwner']),
            new Twig_SimpleFunction('get_entity_tenant_creator', [$this, 'getTenantCreator']),
        ];
    }

    /**
     * @param TenantAwareContract $entity
     *
     * @return null|\Somnambulist\Tenancy\Contracts\TenantParticipant
     */
    public function getTenantOwner(TenantAwareContract $entity)
    {
        return $this->repository->find($entity->getTenantOwnerId());
    }

    /**
     * @param TenantAwareContract $entity
     *
     * @return null|\Somnambulist\Tenancy\Contracts\TenantParticipant
     */
    public function getTenantCreator(TenantAwareContract $entity)
    {
        return $this->repository->find($entity->getTenantCreatorId());
    }
}