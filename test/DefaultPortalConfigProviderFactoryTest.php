<?php

namespace Riddlestone\Brokkr\Portals\Test;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Portals\DefaultPortalConfigProvider;
use Riddlestone\Brokkr\Portals\DefaultPortalConfigProviderFactory;
use Riddlestone\Brokkr\Portals\Exception\ConfigurationNotLoadedException;
use stdClass;

class DefaultPortalConfigProviderFactoryTest extends TestCase
{
    /**
     * @throws ContainerException
     * @throws ConfigurationNotLoadedException
     */
    public function testDefaultPortalConfigProviderFactory()
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
        $factory = new DefaultPortalConfigProviderFactory();

        /** @var DefaultPortalConfigProvider $provider */
        $provider = $factory($container, DefaultPortalConfigProvider::class);
        $this->assertInstanceOf(DefaultPortalConfigProvider::class, $provider);
        $this->assertEquals(['main', 'admin'], $provider->getPortalNames());

        try {
            $factory($container, stdClass::class);
            $this->fail('Factory built an invalid object');
        } catch(ServiceNotCreatedException $e) {
            $this->assertInstanceOf(ServiceNotCreatedException::class, $e);
        }
    }
}
