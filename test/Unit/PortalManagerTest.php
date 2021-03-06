<?php

namespace Riddlestone\Brokkr\Portals\Test\Unit;

use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Portals\ConfigProviderInterface;
use Riddlestone\Brokkr\Portals\PortalManager;

class PortalManagerTest extends TestCase
{
    public function testGetPortals()
    {
        $provider = $this->createMock(ConfigProviderInterface::class);
        $provider->method('getPortalNames')->willReturn(['main', 'admin']);

        $provider2 = $this->createMock(ConfigProviderInterface::class);
        $provider2->method('getPortalNames')->willReturn(['main', 'special']);

        $serviceManager = $this->createMock(ServiceManager::class);
        $portalManager = new PortalManager($serviceManager);
        $portalManager->addPortalConfigProvider($provider);
        $portalManager->addPortalConfigProvider($provider2);

        $this->assertEquals(['main', 'admin', 'special'], $portalManager->getPortalNames());
    }

    public function testGetPortalConfig()
    {
        $serviceManager = $this->createMock(ServiceManager::class);
        $portalManager = new PortalManager($serviceManager);
        $portalManager->configure(
            [
                PortalManager::PROVIDER_NAMES_CONFIG_KEY => [
                    'provider1',
                    'provider2',
                ],
                'factories' => [
                    'provider1' => function () {
                        $provider1 = $this->createMock(ConfigProviderInterface::class);
                        $provider1->method('getPortalNames')->willReturn(['main']);
                        $provider1->method('hasConfiguration')
                            ->willReturnCallback(function ($portalName, $key) {
                                if ($portalName != 'main') {
                                    return false;
                                }
                                $data = ['css', 'js'];
                                if ($key === null) {
                                    return true;
                                }
                                if (in_array($key, $data)) {
                                    return true;
                                }
                                return false;
                            });
                        $provider1->method('getConfiguration')
                            ->willReturnCallback(function ($portalName, $key) {
                                if ($portalName != 'main') {
                                    return [];
                                }
                                $data = ['css' => ['styles.css'], 'js' => ['scripts.js']];
                                if ($key === null) {
                                    return $data;
                                }
                                if (isset($data[$key])) {
                                    return $data[$key];
                                }
                                return [];
                            });
                        return $provider1;
                    },
                    'provider2' => function () {
                        $provider2 = $this->createMock(ConfigProviderInterface::class);
                        $provider2->method('getPortalNames')->willReturn(['main']);
                        $provider2->method('hasConfiguration')
                            ->willReturnCallback(function ($portalName, $key) {
                                if ($portalName != 'main') {
                                    return false;
                                }
                                $data = ['css'];
                                if ($key === null) {
                                    return true;
                                }
                                if (in_array($key, $data)) {
                                    return true;
                                }
                                return false;
                            });
                        $provider2->method('getConfiguration')
                            ->willReturnCallback(function ($portalName, $key) {
                                if ($portalName != 'main') {
                                    return [];
                                }
                                $data = ['css' => ['more-styles.css']];
                                if ($key === null) {
                                    return $data;
                                }
                                if (isset($data[$key])) {
                                    return $data[$key];
                                }
                                return [];
                            });
                        return $provider2;
                    },
                ],
            ]
        );

        $this->assertTrue($portalManager->hasPortalConfig('main'));
        $this->assertTrue($portalManager->hasPortalConfig('main', 'css'));
        $this->assertFalse($portalManager->hasPortalConfig('main', 'img'));
        $this->assertEquals(['css' => ['styles.css', 'more-styles.css'], 'js' => ['scripts.js']], $portalManager->getPortalConfig('main'));
        $this->assertEquals(['styles.css', 'more-styles.css'], $portalManager->getPortalConfig('main', 'css'));
        $this->assertEquals([], $portalManager->getPortalConfig('main', 'img'));

        $this->assertFalse($portalManager->hasPortalConfig('missing-portal'));
        $this->assertFalse($portalManager->hasPortalConfig('missing-portal', 'css'));
        $this->assertEquals([], $portalManager->getPortalConfig('missing-portal'));
        $this->assertEquals([], $portalManager->getPortalConfig('missing-portal', 'css'));

        $portalManager->setCurrentPortalName('main');
        $this->assertTrue($portalManager->hasCurrentPortalConfig());
        $this->assertTrue($portalManager->hasCurrentPortalConfig('css'));
        $this->assertFalse($portalManager->hasCurrentPortalConfig('img'));
        $this->assertEquals(['css' => ['styles.css', 'more-styles.css'], 'js' => ['scripts.js']], $portalManager->getCurrentPortalConfig());
        $this->assertEquals(['styles.css', 'more-styles.css'], $portalManager->getCurrentPortalConfig('css'));
        $this->assertEquals([], $portalManager->getCurrentPortalConfig('img'));

        $portalManager->setCurrentPortalName('missing-portal');
        $this->assertFalse($portalManager->hasCurrentPortalConfig());
        $this->assertFalse($portalManager->hasCurrentPortalConfig('css'));
        $this->assertEquals([], $portalManager->getCurrentPortalConfig());
        $this->assertEquals([], $portalManager->getCurrentPortalConfig('css'));
    }

    public function testSetPortal()
    {
        $serviceManager = $this->createMock(ServiceManager::class);
        $portalManager = new PortalManager($serviceManager);
        $this->assertEquals('main', $portalManager->getCurrentPortalName());
        $portalManager->setCurrentPortalName('admin');
        $this->assertEquals('admin', $portalManager->getCurrentPortalName());
    }

    public function testConfigure()
    {
        $serviceManager = $this->createMock(ServiceManager::class);
        $portalManager = new PortalManager($serviceManager);
        $this->assertEmpty($portalManager->getPortalNames());
        $portalManager->configure(
            [
                PortalManager::PROVIDER_NAMES_CONFIG_KEY => [
                    'mock',
                ],
                'factories' => [
                    'mock' => function () {
                        $mock = $this->createMock(ConfigProviderInterface::class);
                        $mock->method('getPortalNames')->willReturn(['main']);
                        return $mock;
                    },
                ],
            ]
        );
        $this->assertEquals(['main'], $portalManager->getPortalNames());
    }
}
