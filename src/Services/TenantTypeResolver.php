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

namespace Somnambulist\Tenancy\Services;

use Somnambulist\Tenancy\Contracts\TenantParticipant as TenantParticipantContract;
use Illuminate\Support\Collection;

/**
 * Class TenantTypeResolver
 *
 * Allows the tenant participant to be referred to using alternative signatures.
 * For example: if you want to use the EnsureTenantType middleware, you don't want
 * to have to specify a very long class name so they can be aliased using this
 * resolver service.
 *
 * @package    Somnambulist\Tenancy
 * @subpackage Somnambulist\Tenancy\Services\TenantTypeResolver
 * @author     Dave Redfern
 */
class TenantTypeResolver
{

    /**
     * @var Collection
     */
    private $mappings;



    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->mappings = new Collection();
    }

    /**
     * @param TenantParticipantContract $tenant
     * @param string                    $type
     *
     * @return boolean
     */
    public function hasType(TenantParticipantContract $tenant, $type)
    {
        if ( null === $class = $this->getMapping($type) ) {
            throw new \InvalidArgumentException(
                sprintf('Type "%s" is not mapped to the TenantParticipant class', $type)
            );
        }

        if ( $tenant instanceof $class ) {
            return true;
        }

        return false;
    }

    /**
     * @return Collection
     */
    public function getMappings()
    {
        return $this->mappings;
    }

    /**
     * @param array $mappings
     *
     * @return $this
     */
    public function setMappings(array $mappings)
    {
        $this->mappings = new Collection($mappings);

        return $this;
    }

    /**
     * @param string $class
     *
     * @return array
     */
    public function getMappingsForClass($class)
    {
        $return = [];

        foreach ($this->mappings as $key => $value) {
            if ( $value == $class ) {
                $return[] = $key;
            }
        }

        return $return;
    }

    /**
     * @param string $type
     *
     * @return boolean
     */
    public function hasMapping($type)
    {
        return $this->mappings->has($type);
    }

    /**
     * @param string $type
     *
     * @return string|null
     */
    public function getMapping($type)
    {
        return $this->mappings->get($type);
    }

    /**
     * @param string $type
     * @param string $class
     *
     * @return $this
     */
    public function addMapping($type, $class)
    {
        $this->mappings[$type]  = $class;
        $this->mappings[$class] = $class;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function removeMapping($type)
    {
        $this->mappings->forget($type);
        $mappings = $this->getMappingsForClass($type);

        foreach ( $mappings as $key ) {
            $this->mappings->forget($key);
        }

        return $this;
    }
}
