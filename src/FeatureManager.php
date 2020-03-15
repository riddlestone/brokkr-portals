<?php

namespace Riddlestone\Brokkr\Portals;

use Laminas\Config\Config;
use Laminas\ServiceManager\AbstractPluginManager;

class FeatureManager extends AbstractPluginManager implements FeatureProviderInterface
{
    const PROVIDER_NAMES_CONFIG_KEY = 'provider_names';

    /**
     * {@inheritDoc}
     */
    protected $instanceOf = FeatureProviderInterface::class;

    /**
     * @var string[]
     */
    protected $providerNames = [];

    /**
     * @return string[]
     */
    public function getProviderNames(): array
    {
        return $this->providerNames;
    }

    /**
     * @return FeatureProviderInterface[]
     */
    protected function getProviders(): array
    {
        return array_map([$this, 'get'], $this->getProviderNames());
    }

    /**
     * @param string $providerName
     */
    public function addProviderName(string $providerName): void
    {
        if (!in_array($providerName, $this->providerNames)) {
            $this->providerNames[] = $providerName;
        }
    }

    /**
     * Override configure() to record provider names list.
     *
     * {@inheritDoc}
     */
    public function configure(array $config)
    {
        if (isset($config[self::PROVIDER_NAMES_CONFIG_KEY])) {
            foreach ($config[self::PROVIDER_NAMES_CONFIG_KEY] as $providerName) {
                $this->addProviderName($providerName);
            }
        }

        return parent::configure($config);
    }

    /**
     * @param FeatureProviderInterface $provider
     */
    public function addProvider(FeatureProviderInterface $provider): void
    {
        $this->addProviderName(spl_object_hash($provider));
        $this->setService(spl_object_hash($provider), $provider);
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
