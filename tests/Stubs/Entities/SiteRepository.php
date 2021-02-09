<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Tests\Stubs\Entities;

use Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipantRepository;

/**
 * Class SiteRepository
 *
 * @package    Somnambulist\Tenancy\Tests\Stubs\Entities
 * @subpackage Somnambulist\Tenancy\Tests\Stubs\Entities\SiteRepository
 * @author     Dave Redfern
 */
class SiteRepository implements DomainAwareTenantParticipantRepository
{
    public function findOneByDomain($domain)
    {
        switch ($domain)
        {
            case 'example.dev': return new Site(1, 'example.dev', 'example'); break;
            case 'testme.com': return new Site(2, 'testme.com', 'Test Site'); break;
            default:
                return null;
        }
    }

    public function find($id)
    {
        return null;
    }

    public function findAll()
    {
        return null;
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return [
            new Site(1, 'example.dev', 'example'),
            new Site(2, 'testme.com', 'Test Site'),
        ];
    }

    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return null;
    }

    public function getClassName()
    {
        return Site::class;
    }
}
