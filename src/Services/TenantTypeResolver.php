<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Services;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use Somnambulist\Tenancy\Contracts\TenantParticipant as TenantParticipantContract;

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
 */
class TenantTypeResolver
{

    /**
     * @var Collection
     */
    private $mappings;

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
        if (null === $class = $this->getMapping($type)) {
            throw new InvalidArgumentException(
                sprintf('Type "%s" is not mapped to the TenantParticipant class', $type)
            );
        }

        if ($tenant instanceof $class) {
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
            if ($value == $class) {
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

        foreach ($mappings as $key) {
            $this->mappings->forget($key);
        }

        return $this;
    }
}
