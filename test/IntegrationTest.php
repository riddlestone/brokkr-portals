<?php

namespace Riddlestone\Brokkr\Portals\Test;

use Exception;
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

    public function featureTreeData(): array
    {
        return [
            'simple dependency' => [
                [
                    'portals' => ['main' => ['features' => ['feature1']]],
                    'portal_features' => [
                        'feature1' => ['features' => ['feature2'], 'css' => ['feature1.css']],
                        'feature2' => ['css' => ['feature2.css']],
                    ],
                ],
                [
                    'feature2.css',
                    'feature1.css',
                ],
            ],
            'common dependency' => [
                [
                    'portals' => ['main' => ['features' => ['feature1', 'feature2']]],
                    'portal_features' => [
                        'feature1' => ['features' => ['feature3'], 'css' => ['feature1.css']],
                        'feature2' => ['features' => ['feature3'], 'css' => ['feature2.css']],
                        'feature3' => ['css' => ['feature3.css']],
                    ],
                ],
                [
                    'feature3.css',
                    'feature1.css',
                    'feature2.css',
                ],
            ],
            'resource type only in dependency' => [
                [
                    'portals' => ['main' => ['features' => ['feature1', 'feature2']]],
                    'portal_features' => [
                        'feature1' => ['features' => ['feature3']],
                        'feature2' => ['features' => ['feature3']],
                        'feature3' => ['css' => ['feature3.css']],
                    ],
                ],
                [
                    'feature3.css',
                ],
            ],
            'chained dependencies' => [
                [
                    'portals' => ['main' => ['features' => ['feature1']]],
                    'portal_features' => [
                        'feature1' => ['features' => ['feature2'], 'css' => ['feature1.css']],
                        'feature2' => ['features' => ['feature3'], 'css' => ['feature2.css']],
                        'feature3' => ['css' => ['feature3.css']],
                    ],
                ],
                [
                    'feature3.css',
                    'feature2.css',
                    'feature1.css',
                ],
            ],
        ];
    }

    /**
     * Test features which depend on other features
     *
     * @dataProvider featureTreeData
     * @param array $configData
     * @param array $expectedResult
     * @throws Exception
     */
    public function testFeatureTreeExample(array $configData, array $expectedResult)
    {
        $module = new Module();
        $config = new Config($module->getConfig());
        $config->merge(
            new Config($configData)
        );
        $serviceManager = new ServiceManager($config->toArray()['service_manager']);
        $serviceManager->setService('Config', $config->toArray());
        /** @var PortalManager $portalManager */
        $portalManager = $serviceManager->get(PortalManager::class);
        $this->assertEquals(
            $expectedResult,
            $portalManager->getPortalConfig('main', 'css')
        );
    }
}
