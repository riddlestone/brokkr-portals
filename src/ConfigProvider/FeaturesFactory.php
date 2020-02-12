<?php

namespace Riddlestone\Brokkr\Portals\ConfigProvider;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Riddlestone\Brokkr\Portals\FeatureManager;
use Riddlestone\Brokkr\Portals\PortalManager;

class FeaturesFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $provider = new $requestedName();
        if(!($provider instanceof Features)) {
            throw new ServiceNotCreatedException($requestedName . ' not an instance of ' . Features::class);
        }
        $provider->setFeatureManager($container->get(FeatureManager::class));
        $provider->setPortalManager($container->get(PortalManager::class));
        return $provider;
    }
}
