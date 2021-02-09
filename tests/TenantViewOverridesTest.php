<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Tests;

use PHPUnit\Framework\TestCase;
use Somnambulist\Tenancy\Tests\Behaviours\BootApplication;
use Symfony\Component\HttpFoundation\Request;
use function view;

/**
 * Class TenantViewOverridesTest
 *
 * @package    Somnambulist\Tenancy\Tests
 * @subpackage Somnambulist\Tenancy\Tests\TenantViewOverridesTest
 *
 * @group view
 */
class TenantViewOverridesTest extends TestCase
{

    use BootApplication;

    public function testCanRenderDefaultView()
    {
        $ret = $this->app->handle(Request::create('/', 'GET', [], [], [], ['SERVER_NAME' => 'dev.example.dev', 'HTTP_HOST' => 'dev.example.dev']));

        $expected = [
            __DIR__ . '/Stubs/laravel/resources/views',
        ];

        $this->assertEquals($expected, view()->getFinder()->getPaths());

        $this->assertStringContainsString('Laravel Default Welcome', $ret->getContent());
    }

    public function testCanOverrideDefaultViewsByTenant()
    {
        $ret = $this->app->handle(Request::create('/', 'GET', [], [], [], ['SERVER_NAME' => 'www.testme.com', 'HTTP_HOST' => 'www.testme.com']));

        $expected = [
            __DIR__ . '/Stubs/laravel/resources/views/testme.com',
            __DIR__ . '/Stubs/laravel/resources/views',
        ];

        $this->assertEquals($expected, view()->getFinder()->getPaths());

        $this->assertStringContainsString('Test Me Welcome!', $ret->getContent());
    }
}
