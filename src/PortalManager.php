<?php


namespace Riddlestone\Brokkr\Portals;


class PortalManager
{
    /**
     * @var array[]
     */
    protected $config = [
        'main' => [],
    ];

    /**
     * @var string
     */
    protected $currentPortalName = 'main';

    /**
     * PortalManager constructor.
     *
     * @param array|null $config
     */
    public function __construct(?array $config = null)
    {
        if ($config !== null) {
            $this->mergeConfig($config);
        }
    }

    /**
     * Merge new configuration into portal configs.
     *
     * The top level of this array should be the portal names, e.g.
     * [
     *     'main' => [...],
     *     'admin' => [...],
     * ]
     *
     * @param array[] $config
     */
    public function mergeConfig(array $config): void
    {
        $this->config = array_merge_recursive($this->config, $config);
    }

    /**
     * Get a list of the configured portals
     *
     * @return array
     */
    public function getPortalNames(): array
    {
        return array_keys($this->config);
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
     * Get the configuration for the named portal
     *
     * @param string $name
     * @return array
     */
    public function getPortalConfig(string $name): array
    {
        return array_key_exists($name, $this->config)
            ? $this->config[$name]
            : [];
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
     * Get the configuration for the currently selected portal
     *
     * @return array
     */
    public function getCurrentPortalConfig(): array
    {
        return $this->getPortalConfig($this->currentPortalName);
    }
}
