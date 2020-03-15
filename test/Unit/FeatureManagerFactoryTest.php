<?php

namespace Riddlestone\ZF\Portals\Test;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Portals\FeatureManager;
use Riddlestone\Brokkr\Portals\FeatureManagerFactory;
use Riddlestone\Brokkr\Portals\FeatureProviderInterface;
use stdClass;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;

class FeatureManagerFactoryTest extends TestCase
{

    /**
     * @throws ContainerException
     */
    public function test__invoke()
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->willReturnCallback(function ($id) {
                switch ($id) {
                    case 'Config':
                        return [
                            'portal_feature_manager' => [
                                'factories' => [
                                    'SomePortalFeatureProvider' => function(){
                                        return $this->createMock(FeatureProviderInterface::class);
                                    },
                                ],
                            ],
                        ];
                    default:
                        throw new ServiceNotFoundException();
                }
            });
        $factory = new FeatureManagerFactory();

        $featureManager = $factory($container, FeatureManager::class);
        $this->assertInstanceOf(FeatureManager::class, $featureManager);

        try {
            $factory($container, stdClass::class);
            $this->fail('Factory built an invalid object');
        } catch (ServiceNotCreatedException $e) {
            $this->assertInstanceOf(ServiceNotCreatedException::class, $e);
        }
    }
}
