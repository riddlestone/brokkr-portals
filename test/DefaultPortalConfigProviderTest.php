<?php

namespace Riddlestone\Brokkr\Portals\Test;

use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Portals\DefaultPortalConfigProvider;
use Riddlestone\Brokkr\Portals\Exception\ConfigurationNotLoadedException;

class DefaultPortalConfigProviderTest extends TestCase
{
    /**
     * @throws ConfigurationNotLoadedException
     */
    public function testDefaultPortalConfigProvider()
    {
        $provider = new DefaultPortalConfigProvider();
        try {
            $provider->getConfiguration('main');
            $this->fail('Allowed null config');
        } catch(ConfigurationNotLoadedException $e) {
            $this->assertInstanceOf(ConfigurationNotLoadedException::class, $e);
        }
        $provider->setPortalConfig(['main' => ['foo' => ['bar']]]);
        $this->assertEquals(['main'], $provider->getPortalNames());
        $this->assertFalse($provider->hasConfiguration('not-here'));
        $this->assertFalse($provider->hasConfiguration('not-here', 'foo'));
        $this->assertTrue($provider->hasConfiguration('main'));
        $this->assertTrue($provider->hasConfiguration('main', 'foo'));
        $this->assertFalse($provider->hasConfiguration('main', 'bar'));
        $this->assertEquals(['foo' => ['bar']], $provider->getConfiguration('main'));
        $this->assertEquals(['bar'], $provider->getConfiguration('main', 'foo'));
    }
}
