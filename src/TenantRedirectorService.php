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

use Somnambulist\Tenancy\Contracts\BelongsToTenantParticipant as ParticipantContract;
use Somnambulist\Tenancy\Contracts\BelongsToTenantParticipants as ParticipantsContract;

/**
 * Class TenantRedirectResolver
 *
 * @package    Somnambulist\Tenancy
 * @subpackage Somnambulist\Tenancy\TenantRedirectResolver
 * @author     Dave Redfern
 */
class TenantRedirectorService
{

    /**
     * @param ParticipantContract|ParticipantsContract $user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resolve($user)
    {
        if ($user instanceof ParticipantContract) {
            return redirect()->route('tenant.index', [
                'tenant_owner_id'   => $user->getTenantParticipant()->getTenantOwner()->getId(),
                'tenant_creator_id' => $user->getTenantParticipant()->getId()
            ]);
        }

        if ($user instanceof ParticipantsContract) {
            switch ($user->getTenantParticipants()->count()) {
                case 0: $response = redirect()->route('tenant.no_tenants'); break;

                case 1:
                    $response = redirect()->route('tenant.index', [
                        'tenant_owner_id'   => $user->getTenantParticipants()->first()->getTenantOwner()->getId(),
                        'tenant_creator_id' => $user->getTenantParticipants()->first()->getId()
                    ]);
                break;

                default:
                    $response = redirect()->route('tenant.select_tenant');
            }

            return $response;
        }

        throw new \InvalidArgumentException(
            sprintf('Supplied Authenticatable User instance does not implement tenancy')
        );
    }
}