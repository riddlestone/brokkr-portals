<?php

namespace Riddlestone\Brokkr\Portals;

return [
    'portals' => [],
    'portal_config_providers' => [
        DefaultPortalConfigProvider::class,
    ],
    'service_manager' => [
        'factories' => [
            DefaultPortalConfigProvider::class => DefaultPortalConfigProviderFactory::class,
        ],
    ],
];
