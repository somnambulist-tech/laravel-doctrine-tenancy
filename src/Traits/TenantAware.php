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

namespace Somnambulist\Tenancy\Traits;

use Somnambulist\Tenancy\Contracts\Tenant as TenantContract;

/**
 * Trait TenantAware
 *
 * @package    Somnambulist\Tenancy\Traits
 * @subpackage Somnambulist\Tenancy\Traits\TenantAware
 * @author     Dave Redfern
 */
trait TenantAware
{

    /**
     * @var integer
     */
    protected $tenantOwnerId;

    /**
     * @var integer
     */
    protected $tenantCreatorId;



    /**
     * @param TenantContract $tenant
     *
     * @return $this
     */
    public function importTenancyFrom(TenantContract $tenant)
    {
        $this->setTenantOwnerId($tenant->getTenantOwnerId());
        $this->setTenantCreatorId($tenant->getTenantCreatorId());

        return $this;
    }

    /**
     * @return integer
     */
    public function getTenantOwnerId()
    {
        return $this->tenantOwnerId;
    }

    /**
     * @param integer $id
     *
     * @return $this
     */
    public function setTenantOwnerId($id)
    {
        $this->tenantOwnerId = $id;

        return $this;
    }

    /**
     * @return integer
     */
    public function getTenantCreatorId()
    {
        return $this->tenantCreatorId;
    }

    /**
     * @param integer $id
     *
     * @return $this
     */
    public function setTenantCreatorId($id)
    {
        $this->tenantCreatorId = $id;

        return $this;
    }
}