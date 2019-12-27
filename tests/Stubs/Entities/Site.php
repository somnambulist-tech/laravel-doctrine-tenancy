<?php

namespace Somnambulist\Tenancy\Tests\Stubs\Entities;

use Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipant;
use Somnambulist\Tenancy\Entities\SecurityModel;

/**
 * Class Site
 *
 * @package    Somnambulist\Tenancy\Tests\Stubs\Entities
 * @subpackage Somnambulist\Tenancy\Tests\Stubs\Entities\Site
 * @author     Dave Redfern
 */
class Site implements DomainAwareTenantParticipant
{

    private $id;
    private $domain;
    private $name;

    /**
     * Constructor.
     *
     * @param $id
     * @param $domain
     * @param $name
     */
    public function __construct($id, $domain, $name)
    {
        $this->id     = $id;
        $this->domain = $domain;
        $this->name   = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getTenantOwner()
    {
        return $this;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function getSecurityModel()
    {
        return SecurityModel::CLOSED();
    }
}
