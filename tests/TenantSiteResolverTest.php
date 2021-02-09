<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Tests;

use PHPUnit\Framework\TestCase;
use Somnambulist\Tenancy\Entities\NullTenant;
use Somnambulist\Tenancy\Tests\Behaviours\BootApplication;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TenantSiteResolverTest
 *
 * @package    Somnambulist\Tenancy\Tests
 * @subpackage Somnambulist\Tenancy\Tests\TenantSiteResolverTest
 *
 * @group site
 */
class TenantSiteResolverTest extends TestCase
{

    use BootApplication;

    public function testCanResolveSite()
    {
        $response = $this->app->handle(Request::create('/', 'GET', [], [], [], ['SERVER_NAME' => 'dev.example.dev', 'HTTP_HOST' => 'dev.example.dev']));

        $tenant = $this->app->get('auth.tenant')->getTenantOwner();

        $this->assertEquals(1, $tenant->getId());
        $this->assertEquals('example', $tenant->getName());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Laravel', $response->getContent());
    }

    public function testCanResolveAltSite()
    {
        $response = $this->app->handle(Request::create('/', 'GET', [], [], [], ['SERVER_NAME' => 'www.testme.com', 'HTTP_HOST' => 'www.testme.com']));

        $tenant = $this->app->get('auth.tenant')->getTenantOwner();

        $this->assertEquals(2, $tenant->getId());
        $this->assertEquals('Test Site', $tenant->getName());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Test Me', $response->getContent());
    }

    public function testUnconfiguredSiteReturnsError()
    {
        $response = $this->app->handle(Request::create('/', 'GET', [], [], [], ['SERVER_NAME' => 'bob.com', 'HTTP_HOST' => 'bob.com']));

        $tenant = $this->app->get('auth.tenant')->getTenantOwner();

        $this->assertInstanceOf(NullTenant::class, $tenant);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertStringContainsString('Server Error', $response->getContent());
    }
}
