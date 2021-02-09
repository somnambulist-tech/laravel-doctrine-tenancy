<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Console;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Router;
use Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipantRepository as DomainRepository;
use Somnambulist\Tenancy\Http\Middleware\TenantRouteResolver;

/**
 * Class TenantRouteClearCommand
 *
 * @package    Somnambulist\Tenancy\Console
 * @subpackage Somnambulist\Tenancy\Console\TenantRouteClearCommand
 */
class TenantRouteClearCommand extends AbstractTenantCommand
{

    /**
     * @var string
     */
    protected $name = 'tenant:route:clear';

    /**
     * @var string
     */
    protected $description = 'Clear the route cache for a specific tenant.';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    public function __construct(DomainRepository $repository, Router $router, TenantRouteResolver $resolver, FileSystem $files)
    {
        parent::__construct($repository, $router, $resolver);

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->resolveTenantRoutes($this->argument('domain'));

        $this->files->delete($this->laravel->getCachedRoutesPath());

        $this->info('Tenant route cache cleared!');
    }
}
