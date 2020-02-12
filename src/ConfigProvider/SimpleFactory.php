<?php

namespace Riddlestone\Brokkr\Portals\ConfigProvider;

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
            throw new ServiceNotCreatedException($requestedName . ' not an instance of ' . Simple::class);
        }
        $provider->setPortalConfig($container->get('Config')['portals']);
        return $provider;
    }
}