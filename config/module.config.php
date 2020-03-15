<?php

namespace Riddlestone\Brokkr\Portals;

return [
    'portals' => [],
    'portal_manager' => [
        PortalManager::PROVIDER_NAMES_CONFIG_KEY => [
            ConfigProvider\Simple::class,
            ConfigProvider\Features::class,
        ],
        'factories' => [
            ConfigProvider\Simple::class => ConfigProvider\SimpleFactory::class,
            ConfigProvider\Features::class => ConfigProvider\FeaturesFactory::class,
        ],
    ],
    'portal_features' => [],
    'portal_feature_manager' => [
        FeatureManager::PROVIDER_NAMES_CONFIG_KEY => [
            FeatureProvider\Simple::class,
        ],
        'factories' => [
            FeatureProvider\Simple::class => FeatureProvider\SimpleFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            FeatureManager::class => FeatureManagerFactory::class,
            PortalManager::class => PortalManagerFactory::class,
        ],
    ],
];
