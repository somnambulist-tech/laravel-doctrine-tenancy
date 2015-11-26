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

use Somnambulist\Tenancy\TenantSecurityModel;

/**
 * Class TenantParticipant
 *
 * A very basic TenantParticipant can use this trait to essentially create a
 * restricted, no data sharing setup. This is included as an example, but is
 * not necessarily practical.
 *
 * @package    Somnambulist\Tenancy\Traits
 * @subpackage Somnambulist\Tenancy\Traits\TenantParticipant
 * @author     Dave Redfern
 */
trait TenantParticipant
{

    /**
     * @return TenantParticipant
     */
    public function getTenantOwner()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getSecurityModel()
    {
        return TenantSecurityModel::CLOSED;
    }
}