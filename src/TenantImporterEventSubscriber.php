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

namespace Somnambulist\Tenancy;

use Somnambulist\Tenancy\Contracts\Tenant as TenantContract;
use Somnambulist\Tenancy\Contracts\TenantAware as TenantAwareContract;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

/**
 * Class TenantImporterEventSubscriber
 *
 * @package    Somnambulist\Tenancy\Doctrine
 * @subpackage Somnambulist\Tenancy\Doctrine\TenantImporterEventSubscriber
 * @author     Dave Redfern
 */
class TenantImporterEventSubscriber implements EventSubscriber
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
        return [ Events::prePersist ];
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