<?php

namespace Riddlestone\Brokkr\Portals;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        return require __DIR__ . '/../config/module.config.php';
    }
}
