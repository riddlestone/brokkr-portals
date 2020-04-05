<?php


namespace Riddlestone\Brokkr\Portals;


use Laminas\Config\Config;

class PortalManager
{
    /**
     * @var string
     */
    protected $currentPortalName = 'main';

    /**
     * @var PortalConfigProviderInterface[]
     */
    protected $portalConfigProviders = [];

    /**
     * @param PortalConfigProviderInterface $configPortalProvider
     */
    public function addPortalConfigProvider(PortalConfigProviderInterface $configPortalProvider)
    {
        $this->portalConfigProviders[] = $configPortalProvider;
    }

    /**
     * Get a list of the configured portals
     *
     * @return string[]
     */
    public function getPortalNames(): array
    {
        $portals = [];
        foreach($this->portalConfigProviders as $provider) {
            foreach($provider->getPortalNames() as $portalName) {
                if(!in_array($portalName, $portals)) {
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
        foreach($this->portalConfigProviders as $provider) {
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
