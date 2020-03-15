<?php

namespace Riddlestone\Brokkr\Portals\Test\Unit;

use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Portals\Module;

class ModuleTest extends TestCase
{
    public function testGetConfig()
    {
        $module = new Module();
        $config = $module->getConfig();
        $this->assertIsArray($config);
        $this->assertArrayHasKey('portals', $config);
        $this->assertArrayHasKey('portal_manager', $config);
        $this->assertArrayHasKey('portal_features', $config);
        $this->assertArrayHasKey('portal_feature_manager', $config);
    }
}
