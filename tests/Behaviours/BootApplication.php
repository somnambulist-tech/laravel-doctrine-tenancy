<?php

namespace Somnambulist\Tenancy\Tests\Behaviours;

use Illuminate\Console\Application as Artisan;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Support\Facades\Facade;
use Somnambulist\Tenancy\Foundation\TenantAwareApplication;
use Somnambulist\Tenancy\Tests\Stubs\Http;

/**
 * Trait BootApplication
 *
 * @package    Somnambulist\Tenancy\Tests\Behaviours
 * @subpackage Somnambulist\Tenancy\Tests\Behaviours\BootApplication
 */
trait BootApplication
{
    /**
     * @var Application
     */
    protected $app;

    protected function setUp(): void
    {
        Facade::clearResolvedInstances();

        if (! $this->app) {
            $this->refreshApplication();
        }
    }

    protected function tearDown(): void
    {
        if ($this->app) {
            $this->app->flush();

            $this->app = null;
        }

        Artisan::forgetBootstrappers();
    }

    protected function refreshApplication()
    {
        $this->app = $this->createAppInstance();
    }

    private function createAppInstance(): Application
    {
        $app = new TenantAwareApplication(__DIR__ . '/../Stubs/laravel');
        $app->singleton(Kernel::class, Http::class);
        $app->singleton(ExceptionHandler::class, Handler::class);
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
