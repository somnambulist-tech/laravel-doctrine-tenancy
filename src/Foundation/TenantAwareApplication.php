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

namespace Somnambulist\Tenancy\Foundation;

use Illuminate\Foundation\Application;
use Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipant;

/**
 * Class TenantAwareApplication
 *
 * Adds tenant awareness to the core Application so the cache files can be uniquely named.
 *
 * @package    App\Support
 * @subpackage App\Support\TenantAwareApplication
 * @author     Dave Redfern
 */
class TenantAwareApplication extends Application
{

    /**
     * @return boolean
     */
    public function isMultiSiteTenant()
    {
        return ($this['auth.tenant']->getTenantOwner() instanceof DomainAwareTenantParticipant);
    }

    /**
     * @param string $default
     *
     * @return string
     */
    protected function getTenantCacheName($default)
    {
        if ($this->isMultiSiteTenant()) {
            return $this['auth.tenant']->getTenantOwner()->getDomain();
        }

        return $default;
    }

    /**
     * Get the path to the configuration cache file.
     *
     * @return string
     */
    public function getCachedConfigPath()
    {
        return $this->basePath() . '/bootstrap/cache/' . $this->getTenantCacheName('config') . '.php';
    }

    /**
     * Get the path to the routes cache file.
     *
     * @return string
     */
    public function getCachedRoutesPath()
    {
        return $this->basePath() . '/bootstrap/cache/' . $this->getTenantCacheName('routes') . '.php';
    }

    /**
     * Get the path to the cached "compiled.php" file.
     *
     * @return string
     */
    public function getCachedCompilePath()
    {
        return $this->basePath() . '/bootstrap/cache/' . $this->getTenantCacheName('compiled') . '.php';
    }

    /**
     * Get the path to the cached services.json file.
     *
     * @return string
     */
    public function getCachedServicesPath()
    {
        return $this->basePath() . '/bootstrap/cache/' . $this->getTenantCacheName('services') . '.json';
    }
}