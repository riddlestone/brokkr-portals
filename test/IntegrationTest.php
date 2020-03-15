<?php

namespace Riddlestone\Brokkr\Portals\Test;

use Laminas\Config\Config;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Portals\Module;
use Riddlestone\Brokkr\Portals\PortalManager;

class IntegrationTest extends TestCase
{
    public function testWorkingExample()
    {
        $module = new Module();
        $config = new Config($module->getConfig());
        $config->merge(
            new Config(
                [
                    'portals' => [
                        'main' => [
                            'css' => [
                                'foo.css',
                                'bar.css',
                            ],
                        ],
                        'admin' => [
                            'css' => [
                                'foo.css',
                                'baz.css',
                            ],
                            'features' => [
                                'package',
                            ],
                        ],
                    ],
                    'portal_features' => [
                        'package' => [
                            'css' => [
                                'package.css',
                            ],
                        ],
                    ],
                ]
            )
        );
        $serviceManager = new ServiceManager($config->toArray()['service_manager']);
        $serviceManager->setService('Config', $config->toArray());
        /** @var PortalManager $portalManager */
        $portalManager = $serviceManager->get(PortalManager::class);
        $this->assertEquals(['main', 'admin'], $portalManager->getPortalNames());
        $this->assertEquals(
            [
                'foo.css',
                'baz.css',
                'package.css',
            ],
            $portalManager->getPortalConfig('admin', 'css')
        );
    }
}
