<?php

namespace Riddlestone\Brokkr\Portals;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\FactoryInterface;

class PortalManagerFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $manager = new $requestedName();
        if (! ($manager instanceof PortalManager)) {
            throw new ServiceNotCreatedException($requestedName . ' not an instance of ' . PortalManager::class);
        }

        foreach($container->get('Config')['portal_config_providers'] as $provider) {
            $manager->addPortalConfigProvider($container->get($provider));
        }

        return $manager;
    }
}
