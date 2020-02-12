<?php

namespace Riddlestone\Brokkr\Portals\FeatureProvider;

use Riddlestone\Brokkr\Portals\Exception\ConfigurationNotFoundException;
use Riddlestone\Brokkr\Portals\Exception\ConfigurationNotLoadedException;
use Riddlestone\Brokkr\Portals\FeatureProviderInterface;

class Simple implements FeatureProviderInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * @return array
     * @throws ConfigurationNotLoadedException
     */
    public function getConfig(): array
    {
        if($this->config === null) {
            throw new ConfigurationNotLoadedException();
        }
        return $this->config;
    }

    /**
     * @inheritDoc
     * @throws ConfigurationNotLoadedException
     */
    public function hasFeature(string $name, ?string $configKey = null): bool
    {
        return array_key_exists($name, $this->getConfig())
            && ($configKey === null || array_key_exists($configKey, $this->getConfig()[$name]));
    }

    /**
     * @inheritDoc
     * @throws ConfigurationNotFoundException
     * @throws ConfigurationNotLoadedException
     */
    public function getFeature(string $name, ?string $configKey = null): array
    {
        if(!$this->hasFeature($name, $configKey)) {
            throw new ConfigurationNotFoundException();
        }
        if($configKey === null) {
            return $this->getConfig()[$name];
        }
        return $this->getConfig()[$name][$configKey];
    }
}
