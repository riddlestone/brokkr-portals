<?php

namespace Riddlestone\Brokkr\Portals\Test\Unit;

use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Portals\FeatureManager;
use Riddlestone\Brokkr\Portals\FeatureProviderInterface;

class FeatureManagerTest extends TestCase
{
    public function testHasFeature()
    {
        $provider = $this->createMock(FeatureProviderInterface::class);
        $provider->method('hasFeature')->willReturnCallback(
            function ($name) {
                return $name === 'foo';
            }
        );
        $serviceManager = $this->createMock(ServiceManager::class);
        $manager = new FeatureManager($serviceManager);
        $manager->addProvider($provider);
        $this->assertTrue($manager->hasFeature('foo'));
        $this->assertFalse($manager->hasFeature('bar'));
    }

    public function testGetFeature()
    {
        $provider = $this->createMock(FeatureProviderInterface::class);
        $provider->method('hasFeature')->willReturnCallback(
            function ($name) {
                return $name === 'foo';
            }
        );
        $provider->method('getFeature')->willReturnCallback(
            function ($name) {
                return $name === 'foo' ? ['bar' => 'baz'] : [];
            }
        );

        $provider2 = $this->createMock(FeatureProviderInterface::class);
        $provider2->method('hasFeature')->willReturn(false);

        $serviceManager = $this->createMock(ServiceManager::class);
        $manager = new FeatureManager($serviceManager);
        $manager->addProvider($provider);
        $manager->addProvider($provider2);
        $this->assertEquals(['bar' => 'baz'], $manager->getFeature('foo'));
    }


    public function testConfigure()
    {
        $serviceManager = $this->createMock(ServiceManager::class);
        $featureManager = new FeatureManager($serviceManager);
        $this->assertFalse($featureManager->hasFeature('foo'));
        $featureManager->configure(
            [
                FeatureManager::PROVIDER_NAMES_CONFIG_KEY => [
                    'mock',
                ],
                'factories' => [
                    'mock' => function () {
                        $mock = $this->createMock(FeatureProviderInterface::class);
                        $mock->method('hasFeature')->willReturnCallback(
                            function ($feature) {
                                return $feature == 'foo';
                            }
                        );
                        return $mock;
                    },
                ],
            ]
        );
        $this->assertTrue($featureManager->hasFeature('foo'));
    }
}
