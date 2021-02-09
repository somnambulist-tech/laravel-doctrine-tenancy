<?php declare(strict_types=1);

use Somnambulist\Tenancy\Tests\Stubs\Entities\Site;
use Somnambulist\Tenancy\Tests\Stubs\Entities\SiteRepository;

return [
    'multi_account' => [
        'enabled' => false,
    ],

    'multi_site'    => [
        'enabled' => true,
        'participant' => [
            'class'      => Site::class,
            'repository' => SiteRepository::class,
            'mappings'   => [
                // 'my_alias' => Some\Class\Name::class,
            ],
        ],
        'ignorable_domain_components' => [
            'dev.',
            'test.',
            'www.',
        ],
        'router'                      => [
            'namespace' => '',
            'patterns' => [
                'id'      => '[0-9]+',
                'ordinal' => '[0-9]+',
                'page'    => '[0-9]+',
                'rev'     => '[0-9]+',
                'old'     => '[0-9]+',
                'new'     => '[0-9]+',
                'year'    => '2[0-1][0-9]{2}',
                'month'   => '[0-1][0-9]',
                'day'     => '[0-3][0-9]',
                'slug'    => '[0-9a-z-_]+',
                'uuid'    => '[a-f\d]{8}(-[a-f\d]{4}){3}-[a-f\d]{12}',
                'letter'  => '[A-Z0-9]'
            ],
        ],
    ],

    'doctrine' => [
        'repositories' => [

        ],
    ],
];
