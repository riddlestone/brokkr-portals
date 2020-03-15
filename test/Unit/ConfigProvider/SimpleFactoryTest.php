<?php

namespace Riddlestone\Brokkr\Portals\Test\Unit\ConfigProvider;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Portals\ConfigProvider\Simple;
use Riddlestone\Brokkr\Portals\ConfigProvider\SimpleFactory;
use Riddlestone\Brokkr\Portals\Exception\ConfigurationNotLoadedException;
use stdClass;

class SimpleFactoryTest extends TestCase
{
    /**
     * @throws ContainerException
     * @throws ConfigurationNotLoadedException
     */
    public function testFactory()
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->willReturnCallback(function ($id) {
                switch ($id) {
                    case 'Config':
                        return [
                            'portals' => [
                                'main' => [
                                    'foo' => 'bar',
                                ],
                                'admin' => [
                                    'foo' => 'baz',
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
        $this->assertEquals(['main', 'admin'], $provider->getPortalNames());

        try {
            $factory($container, stdClass::class);
            $this->fail('Factory built an invalid object');
        } catch(ServiceNotCreatedException $e) {
            $this->assertInstanceOf(ServiceNotCreatedException::class, $e);
        }
    }
}
