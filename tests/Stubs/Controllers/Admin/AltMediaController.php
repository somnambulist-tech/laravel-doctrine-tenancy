<?php

namespace Somnambulist\Tenancy\Tests\Stubs\Controllers\Admin;

use Somnambulist\Tenancy\Http\Controller\TenantController;
use function view;

/**
 * Class AltMediaController
 *
 * @package    Somnambulist\Tenancy\Tests\Stubs\Controllers\Admin
 * @subpackage Somnambulist\Tenancy\Tests\Stubs\Controllers\Admin\AltMediaController
 */
class AltMediaController extends TenantController
{

    public function indexAction()
    {
        return view('route_test');
    }
}
