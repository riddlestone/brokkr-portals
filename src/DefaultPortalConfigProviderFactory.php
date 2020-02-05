<?php

namespace Riddlestone\Brokkr\Portals;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\FactoryInterface;

class DefaultPortalConfigProviderFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $provider = new $requestedName();
        if(!($provider instanceof DefaultPortalConfigProvider)) {
            throw new ServiceNotCreatedException($requestedName . ' not an instance of ' . DefaultPortalConfigProvider::class);
        }
        $provider->setPortalConfig($container->get('Config')['portals']);
        return $provider;
    }
}