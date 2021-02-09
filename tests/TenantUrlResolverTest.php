<?php declare(strict_types=1);

namespace Somnambulist\Tenancy\Tests;

use PHPUnit\Framework\TestCase;
use Somnambulist\Tenancy\Tests\Behaviours\BootApplication;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TenantUrlResolverTest
 *
 * @package    Somnambulist\Tenancy\Tests
 * @subpackage Somnambulist\Tenancy\Tests\TenantUrlResolverTest
 *
 * @group url
 */
class TenantUrlResolverTest extends TestCase
{

    use BootApplication;

    public function testCanGenerateUrlsToTenantByName()
    {
        $ret = $this->app->handle(Request::create('/routes', 'GET', [], [], [], ['SERVER_NAME' => 'dev.example.dev', 'HTTP_HOST' => 'dev.example.dev']));

        $this->assertStringContainsString('http://dev.example.dev/admin/1/on/1/this/media', $ret->getContent());
        $this->assertStringContainsString('http://dev.example.dev/admin/1/on/1/this/media/create', $ret->getContent());
    }

    public function testCanGenerateUrlsToTenantAltSiteByName()
    {
        $ret = $this->app->handle(Request::create('/routes', 'GET', [], [], [], ['SERVER_NAME' => 'www.testme.com', 'HTTP_HOST' => 'www.testme.com']));

        $this->assertStringContainsString('http://www.testme.com/admin/2/site/2/catalogue/media', $ret->getContent());
        $this->assertStringContainsString('http://www.testme.com/admin/2/site/2/catalogue/media/store', $ret->getContent());
    }

    public function testCanOverrideTenantParameters()
    {
        $ret = $this->app->handle(Request::create('/routes2', 'GET', [], [], [], ['SERVER_NAME' => 'www.testme.com', 'HTTP_HOST' => 'www.testme.com']));

        $this->assertStringContainsString('http://www.testme.com/admin/4/site/5/catalogue/media', $ret->getContent());
    }
}
