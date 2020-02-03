<?php

namespace Riddlestone\ZF\Portals\Test;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Portals\DefaultPortalConfigProvider;
use Riddlestone\Brokkr\Portals\PortalManager;
use Riddlestone\Brokkr\Portals\PortalManagerFactory;
use stdClass;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;

class PortalManagerFactoryTest extends TestCase
{

    /**
     * @throws ContainerException
     * @covers \Riddlestone\Brokkr\Portals\PortalManagerFactory::__invoke
     */
    public function test__invoke()
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->willReturnCallback(function ($id) {
                switch ($id) {
                    case 'Config':
                        return [
                            'portal_config_providers' => [
                                DefaultPortalConfigProvider::class,
                            ],
                        ];
                    case DefaultPortalConfigProvider::class:
                        $provider = new DefaultPortalConfigProvider();
                        $provider->setPortalConfig([
                            'main' => [
                                'foo' => 'bar',
                            ],
                        ]);
                        return $provider;
                    default:
                        throw new ServiceNotFoundException();
                }
            });
        $factory = new PortalManagerFactory();

        $portalManager = $factory($container, PortalManager::class);
        $this->assertInstanceOf(PortalManager::class, $portalManager);

        try {
            $factory($container, stdClass::class);
            $this->fail('Factory built an invalid object');
        } catch(ServiceNotCreatedException $e) {
            $this->assertInstanceOf(ServiceNotCreatedException::class, $e);
        }
    }
}
