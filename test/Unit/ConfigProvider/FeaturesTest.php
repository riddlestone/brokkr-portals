<?php

namespace Riddlestone\Brokkr\Portals\Test\Unit\ConfigProvider;

use Exception;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Portals\ConfigProvider\Features;
use Riddlestone\Brokkr\Portals\FeatureManager;
use Riddlestone\Brokkr\Portals\PortalManager;

class FeaturesTest extends TestCase
{
    public function testGetPortalNames()
    {
        $provider = new Features();
        $this->assertEmpty($provider->getPortalNames());
    }

    /**
     * @throws Exception
     */
    public function testGetFeatureManagerFailure()
    {
        $provider = new Features();
        $this->expectExceptionMessage('Feature manager not provided');
        $provider->getFeatureManager();
    }

    /**
     * @throws Exception
     */
    public function testGetFeatureManagerSuccess()
    {
        $provider = new Features();
        $featureManager = $this->createMock(FeatureManager::class);
        $provider->setFeatureManager($featureManager);
        $this->assertEquals($featureManager, $provider->getFeatureManager());
    }

    /**
     * @throws Exception
     */
    public function testFeaturesPortalConfigProviderWithoutPortalManager()
    {
        $provider = new Features();
        $provider->setFeatureManager($this->createMock(FeatureManager::class));
        $this->expectExceptionMessage('Portal manager not provided');
        $provider->getConfiguration('main');
    }

    /**
     * @throws Exception
     */
    public function testFeaturesPortalConfigProvider()
    {
        $provider = new Features();

        $portalManager = $this->createMock(PortalManager::class);
        $portalManager->method('getPortalConfig')->willReturnCallback(function ($portal, $configKey = null) {
            switch ($portal) {
                case 'main':
                    switch ($configKey) {
                        case null:
                            return [
                                'features' => [
                                    'foo',
                                    'bar',
                                ],
                            ];
                        case 'features':
                            return [
                                'foo',
                                'bar',
                            ];
                    }
            }
            throw new Exception();
        });
        $provider->setPortalManager($portalManager);

        $featureManager = $this->createMock(FeatureManager::class);
        $featureManager->method('hasFeature')->willReturnCallback(function ($feature, $configKey) {
            switch ($feature) {
                case 'foo':
                    switch ($configKey) {
                        case 'css':
                        case 'js':
                        case null:
                            return true;
                        default:
                            return false;
                    }
                case 'bar':
                    switch ($configKey) {
                        case 'css':
                        case null:
                            return true;
                        default:
                            return false;
                    }
                default:
                    return false;
            }
        });
        $featureManager->method('getFeature')->willReturnCallback(function ($feature, $configKey) {
            switch ($feature) {
                case 'foo':
                    switch ($configKey) {
                        case 'css':
                            return ['foo.css'];
                        case 'js':
                            return ['foo.js'];
                        case null:
                            return [
                                'css' => ['foo.css'],
                                'js' => ['foo.js'],
                            ];
                        default:
                            throw new Exception('Nope');
                    }
                case 'bar':
                    switch ($configKey) {
                        case 'css':
                            return ['bar.css'];
                        case null:
                            return [
                                'css' => ['bar.css'],
                            ];
                        default:
                            throw new Exception('Nope');
                    }
                default:
                    throw new Exception('Nope');
            }
        });
        $provider->setFeatureManager($featureManager);

        $this->assertTrue($provider->hasConfiguration('main'));
        $this->assertTrue($provider->hasConfiguration('main', 'css'));
        $this->assertFalse($provider->hasConfiguration('main', 'images'));
        $this->assertEquals(['css' => ['foo.css', 'bar.css'], 'js' => ['foo.js']], $provider->getConfiguration('main'));
    }

    /**
     * @throws Exception
     */
    public function testRecursiveFeatures()
    {
        $features = new Features();
        $this->assertEquals([], $features->getConfiguration('main', 'features'));
    }
}
