<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Tests\Stubs\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('em', function ($app) {
            return new class {
                function getClassMetadata()
                {
                    return [];
                }
            };
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
