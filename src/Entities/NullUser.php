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

namespace Somnambulist\Tenancy\Entities;

use Somnambulist\Tenancy\Contracts\BelongsToTenant as BelongsToTenantContract;
use Somnambulist\Tenancy\Contracts\BelongsToTenantParticipant as BelongsToTenantParticipantContract;
use Somnambulist\Tenancy\Contracts\TenantParticipant;
use Somnambulist\Tenancy\Traits\BelongsToTenant;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

/**
 * Class NullUser
 *
 * @package    Somnambulist\Tenancy\Entities
 * @subpackage Somnambulist\Tenancy\Entities\NullUser
 * @author     Dave Redfern
 */
class NullUser implements
    AuthenticatableContract,
    BelongsToTenantContract,
    BelongsToTenantParticipantContract
{

    use Authenticatable;
    use BelongsToTenant;

    /**
     * @return TenantParticipant
     */
    public function getTenantParticipant()
    {
        return null;
    }
}