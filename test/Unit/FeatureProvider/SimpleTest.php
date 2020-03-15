<?php

namespace Riddlestone\Brokkr\Portals\Test\Unit\FeatureProvider;

use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Portals\Exception\ConfigurationNotFoundException;
use Riddlestone\Brokkr\Portals\Exception\ConfigurationNotLoadedException;
use Riddlestone\Brokkr\Portals\FeatureProvider\Simple;

class SimpleTest extends TestCase
{
    /**
     * @throws ConfigurationNotLoadedException
     */
    public function testMissingConfig()
    {
        $simple = new Simple();
        $this->expectException(ConfigurationNotLoadedException::class);
        $simple->getConfig();
    }

    /**
     * @throws ConfigurationNotLoadedException
     */
    public function testSetAndGetConfig()
    {
        $simple = new Simple();
        $config = ['foo' => ['css' => ['foo.css']]];
        $simple->setConfig($config);
        $this->assertEquals($config, $simple->getConfig());
    }

    /**
     * @throws ConfigurationNotLoadedException
     */
    public function testHasFeature()
    {
        $simple = new Simple();
        $config = ['foo' => ['css' => ['foo.css']]];
        $simple->setConfig($config);
        $this->assertTrue($simple->hasFeature('foo'));
        $this->assertTrue($simple->hasFeature('foo', 'css'));
        $this->assertFalse($simple->hasFeature('foo', 'js'));
        $this->assertFalse($simple->hasFeature('bar'));
        $this->assertFalse($simple->hasFeature('bar', 'css'));
    }

    /**
     * @throws ConfigurationNotFoundException
     * @throws ConfigurationNotLoadedException
     */
    public function testGetFeature()
    {
        $simple = new Simple();
        $config = ['foo' => ['css' => ['foo.css']]];
        $simple->setConfig($config);
        $this->assertEquals($config['foo'], $simple->getFeature('foo'));
        $this->assertEquals($config['foo']['css'], $simple->getFeature('foo', 'css'));
    }

    /**
     * @throws ConfigurationNotFoundException
     * @throws ConfigurationNotLoadedException
     */
    public function testGetMissingFeature()
    {
        $simple = new Simple();
        $config = ['foo' => ['css' => 'foo.css']];
        $simple->setConfig($config);
        $this->expectException(ConfigurationNotFoundException::class);
        $simple->getFeature('bar', 'css');
    }
}
