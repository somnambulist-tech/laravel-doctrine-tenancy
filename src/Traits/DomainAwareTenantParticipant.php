<?php

namespace Somnambulist\Tenancy\Traits;

/**
 * Trait DomainAwareTenantParticipant
 *
 * @package    Somnambulist\Tenancy\Traits
 * @subpackage Somnambulist\Tenancy\Traits\DomainAwareTenantParticipant
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
