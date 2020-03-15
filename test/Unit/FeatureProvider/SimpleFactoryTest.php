<?php

namespace Riddlestone\Brokkr\Portals\Test\Unit\FeatureProvider;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Portals\Exception\ConfigurationNotFoundException;
use Riddlestone\Brokkr\Portals\FeatureProvider\Simple;
use Riddlestone\Brokkr\Portals\FeatureProvider\SimpleFactory;
use Riddlestone\Brokkr\Portals\Exception\ConfigurationNotLoadedException;
use stdClass;

class SimpleFactoryTest extends TestCase
{
    /**
     * @throws ContainerException
     * @throws ConfigurationNotLoadedException
     * @throws ConfigurationNotFoundException
     */
    public function testDefaultPortalConfigProviderFactory()
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->willReturnCallback(function ($id) {
                switch ($id) {
                    case 'Config':
                        return [
                            'portal_features' => [
                                'foo' => [
                                    'css' => ['foo.css'],
                                    'js' => ['foo.js'],
                                ],
                                'bar' => [
                                    'css' => ['bar.css'],
                                ],
                            ],
                        ];
                    default:
                        throw new ServiceNotFoundException();
                }
            });
        $factory = new SimpleFactory();

        /** @var Simple $provider */
        $provider = $factory($container, Simple::class);
        $this->assertInstanceOf(Simple::class, $provider);
        $this->assertEquals(['css' => ['foo.css'], 'js' => ['foo.js']], $provider->getFeature('foo'));
        $this->assertEquals(['foo.css'], $provider->getFeature('foo', 'css'));

        try {
            $factory($container, stdClass::class);
            $this->fail('Factory built an invalid object');
        } catch(ServiceNotCreatedException $e) {
            $this->assertInstanceOf(ServiceNotCreatedException::class, $e);
        }
    }
}
