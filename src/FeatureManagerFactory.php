<?php

namespace Riddlestone\Brokkr\Portals;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\FactoryInterface;

class FeatureManagerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if(!is_a($requestedName, FeatureManager::class, true)) {
            throw new ServiceNotCreatedException($requestedName . ' is not an instance of ' . FeatureManager::class);
        }
        return new $requestedName($container, $container->get('Config')['portal_feature_manager']);
    }
}
