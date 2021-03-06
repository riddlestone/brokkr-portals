<?php

namespace Riddlestone\Brokkr\Portals\FeatureProvider;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\FactoryInterface;

class SimpleFactory implements FactoryInterface
{

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $provider = new $requestedName();
        if(!($provider instanceof Simple)) {
            throw new ServiceNotCreatedException($requestedName . ' is not an instance of ' . Simple::class);
        }
        $provider->setConfig($container->get('Config')['portal_features']);
        return $provider;
    }
}
