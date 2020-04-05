<?php

namespace Riddlestone\Brokkr\Portals\Test;

use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Portals\PortalConfigProviderInterface;
use Riddlestone\Brokkr\Portals\PortalManager;

class PortalManagerTest extends TestCase
{
    public function testGetPortals()
    {
        $provider = $this->createMock(PortalConfigProviderInterface::class);
        $provider->method('getPortalNames')->willReturn(['main', 'admin']);

        $provider2 = $this->createMock(PortalConfigProviderInterface::class);
        $provider2->method('getPortalNames')->willReturn(['main', 'special']);

        $portalManager = new PortalManager();
        $portalManager->addPortalConfigProvider($provider);
        $portalManager->addPortalConfigProvider($provider2);

        $this->assertEquals(['main', 'admin', 'special'], $portalManager->getPortalNames());
    }

    public function testGetPortalConfig()
    {
        $portalManager = new PortalManager();

        $provider1 = $this->createMock(PortalConfigProviderInterface::class);
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

        $provider2 = $this->createMock(PortalConfigProviderInterface::class);
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

        $portalManager->addPortalConfigProvider($provider1);
        $portalManager->addPortalConfigProvider($provider2);

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
        $portalManager = new PortalManager();
        $this->assertEquals('main', $portalManager->getCurrentPortalName());
        $portalManager->setCurrentPortalName('admin');
        $this->assertEquals('admin', $portalManager->getCurrentPortalName());
    }
}
