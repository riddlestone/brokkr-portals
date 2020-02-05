<?php

namespace Riddlestone\Brokkr\Portals;

interface PortalConfigProviderInterface
{
    /**
     * Get the portals this provider has configuration for
     *
     * @return string[]
     */
    public function getPortalNames(): array;

    /**
     * @param string $portalName
     * @param string|null $configKey (All if null)
     * @return bool
     */
    public function hasConfiguration(string $portalName, ?string $configKey = null): bool;

    /**
     * @param string $portalName
     * @param string|null $configKey
     * @return array|string
     */
    public function getConfiguration(string $portalName, ?string $configKey = null);
}
