<?php

namespace Somnambulist\Tenancy\Tests;

use PHPUnit\Framework\TestCase;
use function shell_exec;

/**
 * Class TenantCommandTest
 *
 * @package    Somnambulist\Tenancy\Tests
 * @subpackage Somnambulist\Tenancy\Tests\TenantCommandTest
 *
 * @group commands
 */
class TenantCommandTest extends TestCase
{

    public function testCanListTenants()
    {
        $res = shell_exec(__DIR__ . '/Stubs/artisan tenant:list');

        $this->assertStringContainsString('example.dev', $res);
        $this->assertStringContainsString('testme.com', $res);
    }

    public function testCanListTenantRoutes()
    {
        $res = shell_exec(__DIR__ . '/Stubs/artisan tenant:route:list example.dev');

        $this->assertStringContainsString('admin.media.index', $res);
        $this->assertStringContainsString('Somnambulist\Tenancy\Tests\Stubs\Controllers\Admin\MediaController@indexAction', $res);
    }

    public function testCanCacheAndClearTenantRoutes()
    {
        shell_exec(__DIR__ . '/Stubs/artisan tenant:route:cache testme.com');

        $this->assertFileExists(__DIR__ . '/Stubs/laravel/bootstrap/cache/testme.com.php');

        shell_exec(__DIR__ . '/Stubs/artisan tenant:route:clear testme.com');

        $this->assertFileDoesNotExist(__DIR__ . '/Stubs/laravel/bootstrap/cache/testme.com.php');
    }

    public function testCanListTenantRoutesWithCache()
    {
        shell_exec(__DIR__ . '/Stubs/artisan tenant:route:cache testme.com');

        $res = shell_exec(__DIR__ . '/Stubs/artisan tenant:route:list example.dev');

        $this->assertStringContainsString('admin.media.index', $res);
        $this->assertStringContainsString('Somnambulist\Tenancy\Tests\Stubs\Controllers\Admin\MediaController@indexAction', $res);

        shell_exec(__DIR__ . '/Stubs/artisan tenant:route:clear testme.com');
    }

    public function testCanListTenantRoutesResolvesSiteRoutes()
    {
        $res = shell_exec(__DIR__ . '/Stubs/artisan tenant:route:list testme.com');

        $this->assertStringContainsString('admin.media.index', $res);
        $this->assertStringContainsString('Somnambulist\Tenancy\Tests\Stubs\Controllers\Admin\AltMediaController@indexAction', $res);
    }
}
