<?php

namespace Somnambulist\Tenancy\Http;

use Illuminate\Http\RedirectResponse;
use InvalidArgumentException;
use Somnambulist\Tenancy\Contracts\BelongsToTenantParticipant as ParticipantContract;
use Somnambulist\Tenancy\Contracts\BelongsToTenantParticipants as ParticipantsContract;

/**
 * Class TenantRedirectorService
 *
 * Handles redirecting the various tenant participant types to appropriate
 * URIs after authentication or when visiting tenant aware routes without
 * tenant information.
 *
 * @package    Somnambulist\Tenancy
 * @subpackage Somnambulist\Tenancy\TenantRedirectorService
 */
class TenantRedirectorService
{

    /**
     * @param ParticipantContract|ParticipantsContract $user
     *
     * @return RedirectResponse
     */
    public function resolve($user)
    {
        if ($user instanceof ParticipantContract) {
            return redirect()->route('tenant.index', [
                'tenant_owner_id'   => $user->getTenantParticipant()->getTenantOwner()->getId(),
                'tenant_creator_id' => $user->getTenantParticipant()->getId(),
            ]);
        }

        if ($user instanceof ParticipantsContract) {
            switch ($user->getTenantParticipants()->count()) {
                case 0:
                    $response = redirect()->route('tenant.no_tenants');
                    break;

                case 1:
                    $response = redirect()->route('tenant.index', [
                        'tenant_owner_id'   => $user->getTenantParticipants()->first()->getTenantOwner()->getId(),
                        'tenant_creator_id' => $user->getTenantParticipants()->first()->getId(),
                    ]);
                    break;

                default:
                    $response = redirect()->route('tenant.select_tenant');
            }

            return $response;
        }

        throw new InvalidArgumentException(
            sprintf('Supplied Authenticatable User instance does not implement tenancy')
        );
    }
}
