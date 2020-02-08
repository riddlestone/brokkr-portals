<?php

namespace Riddlestone\Brokkr\Portals;

return [
    'portals' => [],
    'portal_config_providers' => [
        PortalConfigProvider\Simple::class,
    ],
    'service_manager' => [
        'factories' => [
            PortalConfigProvider\Simple::class => PortalConfigProvider\SimpleFactory::class,
            PortalManager::class => PortalManagerFactory::class,
        ],
    ],
];
