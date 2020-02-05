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

        $provider = $this->createMock(PortalConfigProviderInterface::class);
        $provider->method('getPortalNames')->willReturn(['main', 'admin']);
        $provider->method('hasConfiguration')->with('main', null)->willReturn(true);
        $provider->method('getConfiguration')->with('main', null)->willReturn(['foo' => ['bar'], 'bar' => 'baz']);

        $provider2 = $this->createMock(PortalConfigProviderInterface::class);
        $provider2->method('getPortalNames')->willReturn(['admin', 'special']);
        $provider2->method('hasConfiguration')->with('main', null)->willReturn(true);
        $provider2->method('getConfiguration')->with('main', null)->willReturn(['foo' => ['baz'], 'bar' => 'meh']);

        $portalManager->addPortalConfigProvider($provider);
        $portalManager->addPortalConfigProvider($provider2);

        $portalManager->setCurrentPortalName('main');
        $this->assertEquals(['foo' => ['bar', 'baz'], 'bar' => 'meh'], $portalManager->getCurrentPortalConfig());

        $this->assertEquals(['foo' => ['bar', 'baz'], 'bar' => 'meh'], $portalManager->getCurrentPortalConfig());
    }

    public function testSetPortal()
    {
        $portalManager = new PortalManager();
        $this->assertEquals('main', $portalManager->getCurrentPortalName());
        $portalManager->setCurrentPortalName('admin');
        $this->assertEquals('admin', $portalManager->getCurrentPortalName());
    }
}
