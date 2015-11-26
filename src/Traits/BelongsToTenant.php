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

use Somnambulist\Tenancy\Contracts\BelongsToTenantParticipant;
use Somnambulist\Tenancy\Contracts\BelongsToTenantParticipants;
use Somnambulist\Tenancy\Contracts\TenantParticipant as TenantParticipantContract;

/**
 * Trait AccessibleTenants
 *
 * @package    Somnambulist\Tenancy\Traits
 * @subpackage Somnambulist\Tenancy\Traits\AccessibleTenants
 * @author     Dave Redfern
 */
trait BelongsToTenant
{

    /**
     * @param  TenantParticipantContract|string|array $tenant
     * @param  boolean                                $requireAll
     *
     * @return boolean
     */
    public function belongsToTenant($tenant, $requireAll = false)
    {
        if (is_array($tenant)) {
            foreach ($tenant as $t) {
                $hasTenant = $this->belongsToTenant($t);

                if ($hasTenant && !$requireAll) {
                    return true;
                } elseif (!$hasTenant && $requireAll) {
                    return false;
                }
            }

            return $requireAll;
        } else {
            if ($this instanceof BelongsToTenantParticipant) {
                if (
                    !is_null($this->getTenantParticipant()) &&
                    $this->getTenantName($tenant) === $this->getTenantParticipant()->getName()
                ) {
                    return true;
                }
            }
            if ($this instanceof BelongsToTenantParticipants) {
                foreach ($this->getTenantParticipants() as $t) {
                    if ($this->getTenantName($tenant) === $t->getName()) {
                        return true;
                    }
                }
            }

            return false;
        }
    }

    /**
     * @param TenantParticipantContract|string $tenant
     *
     * @return string
     */
    protected function getTenantName($tenant)
    {
        return $tenant instanceof TenantParticipantContract ? $tenant->getName() : $tenant;
    }
}