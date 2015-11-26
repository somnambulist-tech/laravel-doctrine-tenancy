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

namespace Somnambulist\Tenancy\Entity;

use Somnambulist\Tenancy\Contracts\Tenant as TenantContract;
use Somnambulist\Tenancy\Contracts\TenantParticipant as TenantParticipantContract;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class AuthTenant
 *
 * @package    Somnambulist\Tenancy\Entity
 * @subpackage Somnambulist\Tenancy\Entity\AuthTenant
 * @author     Dave Redfern
 */
class Tenant implements TenantContract
{

    /**
     * @var TenantParticipantContract
     */
    protected $tenantOwner;

    /**
     * @var TenantParticipantContract
     */
    protected $tenantCreator;

    /**
     * @var Authenticatable
     */
    protected $user;



    /**
     * Constructor.
     *
     * @param Authenticatable           $user
     * @param TenantParticipantContract $owner
     * @param TenantParticipantContract $creator
     */
    public function __construct(Authenticatable $user, TenantParticipantContract $owner, TenantParticipantContract $creator)
    {
        $this->updateTenancy($user, $owner, $creator);
    }

    /**
     * Update the tenant details
     *
     * @param Authenticatable           $user
     * @param TenantParticipantContract $owner
     * @param TenantParticipantContract $creator
     *
     * @return $this
     * @internal Should not be called normally
     */
    public function updateTenancy(Authenticatable $user, TenantParticipantContract $owner, TenantParticipantContract $creator)
    {
        $this->user          = $user;
        $this->tenantOwner   = $owner;
        $this->tenantCreator = $creator;

        return $this;
    }



    /**
     * @return integer
     */
    public function getTenantOwnerId()
    {
        return $this->tenantOwner->getId();
    }

    /**
     * @return integer
     */
    public function getTenantCreatorId()
    {
        return $this->tenantCreator->getId();
    }

    /**
     * @return string
     */
    public function getTenantSecurityModel()
    {
        return $this->tenantOwner->getSecurityModel();
    }

    /**
     * @return TenantParticipantContract
     */
    public function getTenantOwner()
    {
        return $this->tenantOwner;
    }

    /**
     * @return TenantParticipantContract
     */
    public function getTenantCreator()
    {
        return $this->tenantCreator;
    }

    /**
     * @return Authenticatable
     */
    public function getUser()
    {
        return $this->user;
    }
}