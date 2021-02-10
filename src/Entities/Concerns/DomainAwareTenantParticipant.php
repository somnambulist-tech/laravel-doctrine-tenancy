<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Entities\Concerns;

/**
 * Trait DomainAwareTenantParticipant
 *
 * @package    Somnambulist\Tenancy\Entities\Concerns
 * @subpackage Somnambulist\Tenancy\Entities\Concerns\DomainAwareTenantParticipant
 */
trait DomainAwareTenantParticipant
{

    use TenantParticipant;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     *
     * @return $this
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }
}
