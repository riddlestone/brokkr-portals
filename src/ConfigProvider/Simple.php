<?php

namespace Riddlestone\Brokkr\Portals\ConfigProvider;

use Riddlestone\Brokkr\Portals\Exception\ConfigurationNotLoadedException;
use Riddlestone\Brokkr\Portals\ConfigProviderInterface;

class Simple implements ConfigProviderInterface
{
    /**
     * @var array|null
     */
    protected $portalConfig;

    /**
     * @param array $portalConfig
     */
    public function setPortalConfig(array $portalConfig)
    {
        $this->portalConfig = $portalConfig;
    }

    /**
     * @return array
     * @throws ConfigurationNotLoadedException
     */
    public function getPortalConfig(): ?array
    {
        if($this->portalConfig === null) {
            throw new ConfigurationNotLoadedException();
        }
        return $this->portalConfig;
    }

    /**
     * @inheritDoc
     * @throws ConfigurationNotLoadedException
     */
    public function getPortalNames(): array
    {
        return array_keys($this->getPortalConfig());
    }

    /**
     * @inheritDoc
     * @throws ConfigurationNotLoadedException
     */
    public function hasConfiguration(string $portalName, ?string $configKey = null): bool
    {
        return array_key_exists($portalName, $this->getPortalConfig())
            && (
                $configKey === null
                || (
                    is_array($this->getPortalConfig()[$portalName])
                    && array_key_exists($configKey, $this->getPortalConfig()[$portalName])
                )
            );
    }

    /**
     * @inheritDoc
     * @throws ConfigurationNotLoadedException
     */
    public function getConfiguration(string $portalName, ?string $configKey = null)
    {
        $config = $this->getPortalConfig()[$portalName];
        return $configKey === null ? $config : $config[$configKey];
    }
}
