<?php

namespace Somnambulist\Tenancy\Tests\Stubs\Controllers\Admin;

use Somnambulist\Tenancy\Http\Controller\TenantController;
use function view;

/**
 * Class MediaController
 *
 * @package    Somnambulist\Tenancy\Tests\Stubs\Controllers\Admin
 * @subpackage Somnambulist\Tenancy\Tests\Stubs\Controllers\Admin\MediaController
 */
class MediaController extends TenantController
{

    public function indexAction()
    {
        return view('route_test');
    }
}
