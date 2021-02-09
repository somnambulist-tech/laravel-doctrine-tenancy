<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Tests\Stubs;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Class Console
 *
 * @package    Somnambulist\Tenancy\Tests\Stubs
 * @subpackage Somnambulist\Tenancy\Tests\Stubs\Console
 */
class Console extends ConsoleKernel
{

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [

    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

    }
}
