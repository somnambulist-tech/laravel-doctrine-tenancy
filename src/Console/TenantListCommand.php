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

namespace Somnambulist\Tenancy\Console;

use Illuminate\Console\Command;
use Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipant;
use Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipantRepository;

/**
 * Class TenantListCommand
 *
 * @package    Somnambulist\Tenancy\Console
 * @subpackage Somnambulist\Tenancy\Console\TenantListCommand
 * @author     Dave Redfern
 */
class TenantListCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'tenant:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List the available tenants.';

    /**
     * The table headers for the command.
     *
     * @var array
     */
    protected $headers = ['ID', 'Name', 'Domain', 'Tenant Owner', 'Security Model'];

    /**
     * @var DomainAwareTenantParticipantRepository
     */
    protected $repository;



    /**
     * Constructor.
     *
     * @param DomainAwareTenantParticipantRepository $repository
     */
    public function __construct(DomainAwareTenantParticipantRepository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $data = [];

        /** @var DomainAwareTenantParticipant $tenant */
        foreach ($this->repository->findBy([], ['name' => 'ASC']) as $tenant) {
            $data[] = [
                'id'     => $tenant->getId(),
                'name'   => $tenant->getName(),
                'domain' => $tenant->getDomain(),
                'owner'  => $tenant->getTenantOwner()->getName(),
                'model'  => $tenant->getSecurityModel(),
            ];
        }

        $this->table($this->headers, $data);
    }
}