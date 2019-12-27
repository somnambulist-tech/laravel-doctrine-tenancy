<?php

namespace Somnambulist\Tenancy\Entities;

use Eloquent\Enumeration\AbstractEnumeration;
use Somnambulist\Tenancy\Contracts\SecurityModel as SecurityModelContract;

/**
 * Class SecurityModel
 *
 * Defines the various security levels that the tenant system has implemented.
 * This is used by the TenantAwareRepository to apply query filters to requests
 * to limit the information that is pulled back.
 *
 * Note: enumerations cannot be extended. Implement your own SecurityModelContract
 * instead.
 *
 * @package    Somnambulist\Tenancy
 * @subpackage Somnambulist\Tenancy\Entities\SecurityModel
 *
 * @method static SecurityModel SHARED()
 * @method static SecurityModel USER()
 * @method static SecurityModel CLOSED()
 * @method static SecurityModel INHERIT()
 */
class SecurityModel extends AbstractEnumeration implements SecurityModelContract
{

    const SHARED  = 'shared';
    const USER    = 'user';
    const CLOSED  = 'closed';
    const INHERIT = 'inherit';

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value();
    }
}
