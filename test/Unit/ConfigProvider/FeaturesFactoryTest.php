<?php

namespace Riddlestone\Brokkr\Portals\Test\Unit\ConfigProvider;

use Exception;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Portals\ConfigProvider\Features;
use Riddlestone\Brokkr\Portals\ConfigProvider\FeaturesFactory;
use Riddlestone\Brokkr\Portals\FeatureManager;
use Riddlestone\Brokkr\Portals\PortalManager;
use stdClass;

class FeaturesFactoryTest extends TestCase
{
    /**
     * @throws ContainerException
     * @throws Exception
     */
    public function testFactory()
    {
        $portalManager = $this->createMock(PortalManager::class);
        $featureManager = $this->createMock(FeatureManager::class);

        /** @var MockObject&ContainerInterface $container */
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->willReturnCallback(function ($id) use ($portalManager, $featureManager) {
                switch ($id) {
                    case PortalManager::class:
                        return $portalManager;
                    case FeatureManager::class:
                        return $featureManager;
                    default:
                        throw new ServiceNotFoundException();
                }
            });
        $factory = new FeaturesFactory();

        /** @var Features $provider */
        $provider = $factory($container, Features::class);
        $this->assertInstanceOf(Features::class, $provider);
        $this->assertEquals($portalManager, $provider->getPortalManager());
        $this->assertEquals($featureManager, $provider->getFeatureManager());

        try {
            $factory($container, stdClass::class);
            $this->fail('Factory built an invalid object');
        } catch(ServiceNotCreatedException $e) {
            $this->assertInstanceOf(ServiceNotCreatedException::class, $e);
        }
    }
}
