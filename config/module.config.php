<?php

namespace Riddlestone\Brokkr\Portals;

return [
    'portals' => [],
    'portal_config_providers' => [
        ConfigProvider\Features::class,
        ConfigProvider\Simple::class,
    ],
    'portal_features' => [],
    'portal_feature_providers' => [
        FeatureProvider\Simple::class,
    ],
    'service_manager' => [
        'factories' => [
            ConfigProvider\Features::class => ConfigProvider\FeaturesFactory::class,
            ConfigProvider\Simple::class => ConfigProvider\SimpleFactory::class,
            FeatureProvider\Simple::class => FeatureProvider\SimpleFactory::class,
            FeatureManager::class => FeatureManagerFactory::class,
            PortalManager::class => PortalManagerFactory::class,
        ],
    ],
];
