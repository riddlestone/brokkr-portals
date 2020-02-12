<?php

namespace Riddlestone\Brokkr\Portals;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\FactoryInterface;

class FeatureManagerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $manager = new $requestedName();
        if(!($manager instanceof FeatureManager)) {
            throw new ServiceNotCreatedException($requestedName . ' is not an instance of ' . FeatureManager::class);
        }
        foreach($container->get('Config')['portal_feature_providers'] as $providerName) {
            $provider = $container->get($providerName);
            $manager->addProvider($provider);
        }
        return $manager;
    }
}
