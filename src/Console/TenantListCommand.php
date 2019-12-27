<?php

namespace Somnambulist\Tenancy\Console;

use Illuminate\Console\Command;
use Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipant;
use Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipantRepository;

/**
 * Class TenantListCommand
 *
 * @package    Somnambulist\Tenancy\Console
 * @subpackage Somnambulist\Tenancy\Console\TenantListCommand
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
    public function handle()
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
