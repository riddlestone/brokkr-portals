<?php

namespace Riddlestone\Brokkr\Portals\ConfigProvider;

use Exception;
use Laminas\Config\Config;
use Riddlestone\Brokkr\Portals\ConfigProviderInterface;
use Riddlestone\Brokkr\Portals\FeatureManager;
use Riddlestone\Brokkr\Portals\PortalManager;

class Features implements ConfigProviderInterface
{
    /**
     * @var FeatureManager
     */
    protected $featureManager;

    /**
     * @var PortalManager
     */
    protected $portalManager;

    /**
     * @param FeatureManager $featureManager
     */
    public function setFeatureManager(FeatureManager $featureManager): void
    {
        $this->featureManager = $featureManager;
    }

    /**
     * @return FeatureManager
     * @throws Exception
     */
    public function getFeatureManager(): FeatureManager
    {
        if ($this->featureManager === null) {
            throw new Exception('Feature manager not provided');
        }
        return $this->featureManager;
    }

    /**
     * @param PortalManager $portalManager
     */
    public function setPortalManager(PortalManager $portalManager): void
    {
        $this->portalManager = $portalManager;
    }

    /**
     * @return PortalManager
     * @throws Exception
     */
    public function getPortalManager(): PortalManager
    {
        if ($this->portalManager === null) {
            throw new Exception('Portal manager not provided');
        }
        return $this->portalManager;
    }

    /**
     * @inheritDoc
     */
    public function getPortalNames(): array
    {
        // We're not going to add any new portal names
        return [];
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function hasConfiguration(string $portalName, ?string $configKey = null): bool
    {
        if($configKey === 'features') {
            return false;
        }

        // get the list of features for the portal
        $features = $this->getPortalManager()->getPortalConfig($portalName, 'features');

        foreach($features as $feature) {
            if($this->getFeatureManager()->hasFeature($feature, $configKey)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getConfiguration(string $portalName, ?string $configKey = null)
    {
        if($configKey === 'features') {
            return [];
        }

        $features = $this->getPortalManager()->getPortalConfig($portalName, 'features');
        $config = new Config([]);

        foreach($features as $feature) {
            $config->merge(new Config($this->getFeatureManager()->getFeature($feature, $configKey)));
        }

        return $config->toArray();
    }
}
