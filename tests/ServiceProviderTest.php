<?php

namespace Somnambulist\Tenancy\Tests;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Exceptions\Handler;
use PHPUnit\Framework\TestCase;
use Somnambulist\Tenancy\Entities\NullTenant;
use Somnambulist\Tenancy\Foundation\TenantAwareApplication;
use Somnambulist\Tenancy\Tests\Stubs\Http;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ServiceProviderTest
 *
 * @package    Somnambulist\Tenancy\Tests
 * @subpackage Somnambulist\Tenancy\Tests\ServiceProviderTest
 */
class ServiceProviderTest extends TestCase
{

    public function testCanResolveSite()
    {
        $app = new TenantAwareApplication(__DIR__ . '/Stubs/laravel');
        $app->singleton(Kernel::class, Http::class);
        $app->singleton(ExceptionHandler::class, Handler::class);

        $response = $app->handle(Request::create('/', 'GET', [], [], [], ['SERVER_NAME' => 'dev.example.dev', 'HTTP_HOST' => 'dev.example.dev']));

        $tenant = $app->get('auth.tenant')->getTenantOwner();

        $this->assertEquals(1, $tenant->getId());
        $this->assertEquals('example', $tenant->getName());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Laravel', $response->getContent());
    }

    public function testCanResolveAltSite()
    {
        $app = new TenantAwareApplication(__DIR__ . '/Stubs/laravel');
        $app->singleton(Kernel::class, Http::class);
        $app->singleton(ExceptionHandler::class, Handler::class);

        $response = $app->handle(Request::create('/', 'GET', [], [], [], ['SERVER_NAME' => 'www.testme.com', 'HTTP_HOST' => 'www.testme.com']));

        $tenant = $app->get('auth.tenant')->getTenantOwner();

        $this->assertEquals(2, $tenant->getId());
        $this->assertEquals('Test Site', $tenant->getName());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Test Me', $response->getContent());
    }

    public function testUnconfiguredSiteReturnsError()
    {
        $app = new TenantAwareApplication(__DIR__ . '/Stubs/laravel');
        $app->singleton(Kernel::class, Http::class);
        $app->singleton(ExceptionHandler::class, Handler::class);

        $response = $app->handle(Request::create('/', 'GET', [], [], [], ['SERVER_NAME' => 'bob.com', 'HTTP_HOST' => 'bob.com']));

        $tenant = $app->get('auth.tenant')->getTenantOwner();

        $this->assertInstanceOf(NullTenant::class, $tenant);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertStringContainsString('Server Error', $response->getContent());
    }
}
