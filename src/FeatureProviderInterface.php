<?php

namespace Riddlestone\Brokkr\Portals;

interface FeatureProviderInterface
{
    /**
     * Whether this class provides portal config for a given feature name, and optionally whether that feature provides
     * a given portal config key
     *
     * @param string $name
     * @param string|null $configKey
     * @return bool
     */
    public function hasFeature(string $name, ?string $configKey = null): bool;

    /**
     * Provides the portal config for a given feature name, optionally filtered to a single portal config key
     *
     * @param string $name
     * @param string|null $configKey
     * @return array
     */
    public function getFeature(string $name, ?string $configKey = null): array;
}
