<?php

namespace Riddlestone\Brokkr\Portals\Test;

use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Portals\PortalManager;

class PortalManagerTest extends TestCase
{
    public function testMergeConfig()
    {
        $portalManager = new PortalManager(
            [
                'main' => [
                    'letters' => ['a', 'b', 'c'],
                ],
            ]
        );
        $this->assertEquals(['letters' => ['a', 'b', 'c']], $portalManager->getCurrentPortalConfig());
        $portalManager->mergeConfig(
            [
                'main' => [
                    'letters' => ['d', 'e'],
                    'numbers' => [1, 2, 3],
                ],
            ]
        );
        $this->assertEquals(
            ['letters' => ['a', 'b', 'c', 'd', 'e'], 'numbers' => [1, 2, 3]],
            $portalManager->getCurrentPortalConfig()
        );
    }

    public function testGetCurrentPortalConfig()
    {
        $portalManager = new PortalManager(
            [
                'main' => ['foo' => true],
                'admin' => ['foo' => false],
            ]
        );
        $this->assertEquals(['foo' => true], $portalManager->getCurrentPortalConfig());
        $portalManager->setCurrentPortalName('admin');
        $this->assertEquals(['foo' => false], $portalManager->getCurrentPortalConfig());
    }

    public function testGetPortals()
    {
        $portalManager = new PortalManager(
            [
                'main' => [],
                'admin' => [],
            ]
        );
        $this->assertEquals(['main', 'admin'], $portalManager->getPortalNames());
    }

    public function testSetPortal()
    {
        $portalManager = new PortalManager();
        $this->assertEquals('main', $portalManager->getCurrentPortalName());
        $portalManager->setCurrentPortalName('admin');
        $this->assertEquals('admin', $portalManager->getCurrentPortalName());
    }

    public function testGetPortalConfig()
    {
        $portalManager = new PortalManager(
            [
                'main' => ['foo' => true],
                'admin' => ['foo' => false],
            ]
        );
        $this->assertEquals(['foo' => true], $portalManager->getPortalConfig('main'));
        $this->assertEquals(['foo' => false], $portalManager->getPortalConfig('admin'));
    }
}
