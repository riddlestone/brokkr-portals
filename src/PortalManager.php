<?php

namespace Riddlestone\Brokkr\Portals;

use Laminas\Config\Config;
use Laminas\ServiceManager\AbstractPluginManager;

class PortalManager extends AbstractPluginManager
{
    const PROVIDER_NAMES_CONFIG_KEY = 'provider_names';

    /**
     * An object type that the created instance must be instanced of
     *
     * @var null|string
     */
    protected $instanceOf = ConfigProviderInterface::class;

    /**
     * @var string
     */
    protected $currentPortalName = 'main';

    /**
     * @var string[]
     */
    protected $providerNames = [];

    /**
     * @return string[]
     */
    protected function getProviderNames(): array
    {
        return $this->providerNames;
    }

    /**
     * @return ConfigProviderInterface[]
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
     * @param ConfigProviderInterface $provider
     */
    public function addPortalConfigProvider(ConfigProviderInterface $provider): void
    {
        $this->addProviderName(spl_object_hash($provider));
        $this->setService(spl_object_hash($provider), $provider);
    }

    /**
     * Get a list of the configured portals
     *
     * @return string[]
     */
    public function getPortalNames(): array
    {
        $portals = [];
        foreach ($this->getProviders() as $provider) {
            foreach ($provider->getPortalNames() as $portalName) {
                if (!in_array($portalName, $portals)) {
                    $portals[] = $portalName;
                }
            }
        }
        return $portals;
    }

    /**
     * Set the current portal
     *
     * @param string $name
     */
    public function setCurrentPortalName(string $name): void
    {
        $this->currentPortalName = $name;
    }

    /**
     * Get whether configuration exists for the named portal
     *
     * @param string $name
     * @param string|null $configKey
     * @return bool
     */
    public function hasPortalConfig(string $name, ?string $configKey = null): bool
    {
        foreach($this->portalConfigProviders as $provider) {
            if ($provider->hasConfiguration($name, $configKey)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the configuration for the named portal
     *
     * @param string $name
     * @param string|null $configKey
     * @return array
     */
    public function getPortalConfig(string $name, ?string $configKey = null): array
    {
        $config = new Config([]);
        foreach ($this->getProviders() as $provider) {
            if ($provider->hasConfiguration($name, $configKey)) {
                $config->merge(new Config($provider->getConfiguration($name, $configKey)));
            }
        }
        return $config->toArray();
    }

    /**
     * Return the currently selected portal name
     *
     * @return string
     */
    public function getCurrentPortalName(): string
    {
        return $this->currentPortalName;
    }

    /**
     * Get whether configuration exists for the currently selected portal
     *
     * @param string|null $configKey
     * @return bool
     */
    public function hasCurrentPortalConfig(?string $configKey = null): bool
    {
        return $this->hasPortalConfig($this->currentPortalName, $configKey);
    }

    /**
     * Get the configuration for the currently selected portal
     *
     * @param string|null $configKey
     * @return array
     */
    public function getCurrentPortalConfig(?string $configKey = null): array
    {
        return $this->getPortalConfig($this->currentPortalName, $configKey);
    }
}
