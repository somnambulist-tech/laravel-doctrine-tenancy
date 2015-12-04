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

namespace Somnambulist\Tenancy\Http\Middleware;

use Somnambulist\Tenancy\Contracts\Tenant as TenantContract;
use Somnambulist\Tenancy\Services\TenantTypeResolver;
use Closure;

/**
 * Class EnsureTenantType
 *
 * @package    Somnambulist\Tenancy\Http\Middleware
 * @subpackage Somnambulist\Tenancy\Http\Middleware\EnsureTenantType
 * @author     Dave Redfern
 */
class EnsureTenantType
{

    /**
     * @var TenantContract
     */
    private $tenant;

    /**
     * @var \Somnambulist\Tenancy\Services\TenantTypeResolver
     */
    private $typeResolver;



    /**
     * Constructor.
     *
     * @param TenantContract     $tenant
     * @param TenantTypeResolver $typeResolver
     */
    public function __construct(TenantContract $tenant, TenantTypeResolver $typeResolver)
    {
        $this->tenant       = $tenant;
        $this->typeResolver = $typeResolver;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string                   $type
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $type)
    {
        if (!$this->typeResolver->hasType($this->tenant->getTenantCreator(), $type)) {
            return redirect()->route('tenant.tenant_type_not_supported', ['type' => $type]);
        }

        return $next($request);
    }
}
