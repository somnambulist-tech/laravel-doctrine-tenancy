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

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * Class SecurityModel
 *
 * Defines the various security levels that the tenant system has implemented.
 * This is used by the TenantAwareRepository to apply query filters to requests
 * to limit the information that is pulled back.
 *
 * Deliberately left open to allow adding additional security model types.
 *
 * @package    Somnambulist\Tenancy
 * @subpackage Somnambulist\Tenancy\Entities\SecurityModel
 * @author     Dave Redfern
 */
class SecurityModel extends AbstractEnumeration
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