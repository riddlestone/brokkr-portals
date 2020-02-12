<?php

namespace Riddlestone\Brokkr\Portals\Test;

use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Portals\Exception\ConfigurationNotLoadedException;
use Riddlestone\Brokkr\Portals\FeatureManager;
use Riddlestone\Brokkr\Portals\FeatureProviderInterface;

class FeatureManagerTest extends TestCase
{
    /**
     * @throws \Riddlestone\Brokkr\Portals\Exception\ConfigurationNotLoadedException
     */
    public function testEmptyProviders()
    {
        $manager = new FeatureManager();
        $this->expectException(ConfigurationNotLoadedException::class);
        $manager->getProviders();
    }

    /**
     * @throws \Riddlestone\Brokkr\Portals\Exception\ConfigurationNotLoadedException
     */
    public function testSetProviders()
    {
        $manager = new FeatureManager();
        $providers = [
            $this->createMock(FeatureProviderInterface::class),
            $this->createMock(FeatureProviderInterface::class),
        ];
        $manager->setProviders($providers);
        $this->assertEquals($providers, $manager->getProviders());
    }

    /**
     * @throws \Riddlestone\Brokkr\Portals\Exception\ConfigurationNotLoadedException
     */
    public function testAddProvider()
    {
        $manager = new FeatureManager();
        $providers = [
            $this->createMock(FeatureProviderInterface::class),
            $this->createMock(FeatureProviderInterface::class),
        ];
        $manager->addProvider($providers[0]);
        $manager->addProvider($providers[1]);
        $this->assertEquals($providers, $manager->getProviders());
    }

    /**
     * @throws \Riddlestone\Brokkr\Portals\Exception\ConfigurationNotLoadedException
     */
    public function testRemoveProvider()
    {
        $manager = new FeatureManager();
        $providers = [
            $this->createMock(FeatureProviderInterface::class),
            $this->createMock(FeatureProviderInterface::class),
        ];
        $manager->setProviders($providers);
        $this->assertEquals($providers, $manager->getProviders());

        $manager->removeProvider($providers[0]);
        $this->assertEquals([$providers[1]], $manager->getProviders());
    }

    /**
     * @throws ConfigurationNotLoadedException
     */
    public function testHasFeature()
    {
        $provider = $this->createMock(FeatureProviderInterface::class);
        $provider->method('hasFeature')->willReturnCallback(function($name){
            return $name === 'foo';
        });
        $manager = new FeatureManager();
        $manager->addProvider($provider);
        $this->assertTrue($manager->hasFeature('foo'));
        $this->assertFalse($manager->hasFeature('bar'));
    }

    /**
     * @throws ConfigurationNotLoadedException
     */
    public function testGetFeature()
    {
        $provider = $this->createMock(FeatureProviderInterface::class);
        $provider->method('hasFeature')->willReturnCallback(function($name){
            return $name === 'foo';
        });
        $provider->method('getFeature')->willReturnCallback(function($name){
            return $name === 'foo' ? ['bar' => 'baz'] : [];
        });

        $provider2 = $this->createMock(FeatureProviderInterface::class);
        $provider2->method('hasFeature')->willReturn(false);

        $manager = new FeatureManager();
        $manager->addProvider($provider);
        $manager->addProvider($provider2);
        $this->assertEquals(['bar' => 'baz'], $manager->getFeature('foo'));
    }

}
