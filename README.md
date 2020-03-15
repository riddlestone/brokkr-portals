# [![Riddlestone](https://avatars0.githubusercontent.com/u/57593244?s=30&v=4)](https://github.com/riddlestone) Brokkr-Portals

A [Laminas](https://github.com/laminas) module to pull configuration together for portals, such as a public portal, an admin portal, etc.

## Adding Configuration

To add information about a portal, add it to a module configuration file under `portals.{portal_name}`:

```php
return [
    'portals' => [
        'main' => [
            'layout' => 'main.layout',
            'css' => [
                __DIR__ . '/../css/styles.css',
            ],
            'js' => [
                __DIR__ . '/../js/scripts.js',
            ],
        ],
    ],
];
```

Portals also supports lazy configuration loading through PortalConfigProviders:

```php
return [
    'portal_manager' => [
        'provider_names' => [
            'My\\Portal\\Config\\Provider',
        ],
        'factories' => [
            'My\\Portal\\Config\\Provider' => 'My\\Portal\\Config\\ProviderFactory',
        ],
    ],
];
```

## Portal Features

Features allow you to group portal configuration together, and assign it to a portal separately. This might be usefuuul
if some modular functionality requires multiple css/js files, but the module doesn't know which portals it will be used
in:

```php
return [
    'portals' => [
        'main' => [
            'features' => [
                'some-functionality',
            ],
        ],
    ],
    'portal_features' => [
        'some-functionality' => [
            'css' => [
                __DIR__ . '/../css/styles.css',
            ],
            'js' => [
                __DIR__ . '/../js/scripts.js',
            ],
        ],
    ],
];
```

Here the module can define the js and css required for portal_features/some-functionality, and the project can declare
that portals/main uses it.

## Getting the Portal Manager
```php
/** @var \Laminas\ServiceManager\ServiceManager $serviceManager */

$portalManager = $serviceManager->get(\Riddlestone\Brokkr\Portals\PortalManager::class);
```

## Getting Configuration

```php
/** @var \Riddlestone\Brokkr\Portals\PortalManager $portalManager */

# get a list of portals
$portals = $portalManager->getPortalNames();

# set the current portal
$portalManager->setCurrentPortalName('main');

# get the current portal name
$portalName = $portalManager->getCurrentPortalName();

# check a portal has config
$hasConfig = $portalManager->hasPortalConfig('main', 'css');

# check the current portal has config
$hasConfig = $portalManager->hasCurrentPortalConfig('css');

# get the config for a portal
$portalConfig = $portalManager->getPortalConfig('main', 'css');

# get the config for the current portal
$portalConfig = $portalManager->getCurrentPortalConfig('css');
```
