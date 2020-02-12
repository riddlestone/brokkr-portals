<?php

namespace Riddlestone\Brokkr\Portals;

use Laminas\Config\Config;
use Riddlestone\Brokkr\Portals\Exception\ConfigurationNotLoadedException;

class FeatureManager implements FeatureProviderInterface
{
    /**
     * @var FeatureProviderInterface[]
     */
    protected $providers;

    /**
     * @param FeatureProviderInterface[] $providers
     */
    public function setProviders(array $providers): void
    {
        $this->providers = $providers;
    }

    /**
     * @param FeatureProviderInterface $provider
     */
    public function addProvider(FeatureProviderInterface $provider)
    {
        if(!is_array($this->providers)) {
            $this->providers = [];
        }
        if(!in_array($provider, $this->providers, true)) {
            $this->providers[] = $provider;
        }
    }

    /**
     * @param FeatureProviderInterface $provider
     */
    public function removeProvider(FeatureProviderInterface $provider)
    {
        $key = array_search($provider, $this->providers, true);
        if($key !== false) {
            unset($this->providers[$key]);
            $this->providers = array_values($this->providers);
        }
    }

    /**
     * @return FeatureProviderInterface[]
     * @throws ConfigurationNotLoadedException
     */
    public function getProviders(): array
    {
        if(!is_array($this->providers)) {
            throw new ConfigurationNotLoadedException();
        }
        return $this->providers;
    }

    /**
     * @inheritDoc
     * @throws ConfigurationNotLoadedException
     */
    public function hasFeature(string $name, ?string $configKey = null): bool
    {
        foreach($this->getProviders() as $provider) {
            if($provider->hasFeature($name, $configKey)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritDoc
     * @throws ConfigurationNotLoadedException
     */
    public function getFeature(string $name, ?string $configKey = null): array
    {
        $feature = new Config([]);
        foreach($this->getProviders() as $provider) {
            if(!$provider->hasFeature($name, $configKey)) {
                continue;
            }
            $feature->merge(new Config($provider->getFeature($name, $configKey)));
        }
        return $feature->toArray();
    }
}
