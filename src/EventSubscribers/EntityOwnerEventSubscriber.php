<?php

namespace Somnambulist\Tenancy\EventSubscribers;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Somnambulist\Tenancy\Contracts\Tenant as TenantContract;
use Somnambulist\Tenancy\Contracts\TenantAware as TenantAwareContract;

/**
 * Class EntityOwnerEventSubscriber
 *
 * @package    Somnambulist\Tenancy\EventSubscribers
 * @subpackage Somnambulist\Tenancy\EventSubscribers\EntityOwnerEventSubscriber
 */
class EntityOwnerEventSubscriber implements EventSubscriber
{

    /**
     * @var TenantContract
     */
    protected $tenant;



    /**
     * Constructor.
     *
     * @param TenantContract $tenant
     */
    public function __construct(TenantContract $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [Events::prePersist];
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        if (
            $entity instanceof TenantAwareContract &&
            (!$entity->getTenantCreatorId() && !$entity->getTenantOwnerId())
        ) {
            $entity->importTenancyFrom($this->tenant);
        }
    }
}
