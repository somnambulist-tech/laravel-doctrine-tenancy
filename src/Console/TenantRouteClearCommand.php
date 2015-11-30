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

use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Router;
use Somnambulist\Tenancy\Contracts\DomainAwareTenantParticipantRepository as DomainRepository;
use Somnambulist\Tenancy\Http\Middleware\TenantRouteResolver;

/**
 * Class TenantRouteClearCommand
 *
 * @package    Somnambulist\Tenancy\Console
 * @subpackage Somnambulist\Tenancy\Console\TenantRouteClearCommand
 * @author     Dave Redfern
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
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;



    /**
     * Constructor.
     *
     * @param DomainRepository    $repository
     * @param Router              $router
     * @param TenantRouteResolver $resolver
     * @param Filesystem          $files
     */
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
    public function fire()
    {
        $this->resolveTenantRoutes($this->argument('domain'));

        $this->files->delete($this->laravel->getCachedRoutesPath());

        $this->info('Tenant route cache cleared!');
    }
}