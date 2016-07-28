# Zend Session middleware for Zend Expressive

Provides [Zend Session](https://github.com/zendframework/zend-session) integration for
[Expressive](https://github.com/zendframework/zend-expressive).

Install this library using composer:

```bash
$ composer require xorock/expressive-zend-session
```

## Usage
I suggest installing [Expressive Configuration Manager](https://github.com/mtymek/expressive-config-manager)

Zend Session has built-in `ConfigProvider` class, responsible for automatic registration of the components.
With Expressive Configuration Manager You can register all factories with just single line:

```php
$configManager = new ConfigManager([
    \Zend\Session\ConfigProvider::class,
    new PhpFileProvider('config/autoload/{{,*.}global,{,*.}local}.php'),
], 'data/config-cache.php');
```

Now, Zend Session will search Your merged config file for predefined keys. Please refer to Zend Session documentation. 

As an example, We can create following file:

**session.global.php**
```php
use Zend\Session\Storage\SessionArrayStorage;

return [
    'session_config' => [
        'name' => 'SID',
        'cookie_httponly' => true,
        'cookie_path' => '/',
        'cookie_secure' => false,
        'use_cookies' => true,
        'cookie_lifetime' => 3600,
        'save_path' => '/temp/data/session',
    ],
    'session_storage' => [
        'type' => SessionArrayStorage::class
    ],
];
```

**Note:** There is a [bug](https://github.com/zendframework/zend-session/issues/10) in SessionManager which can lead
to fatal error when using SessionStorage class.

Now You can register middleware:

**middleware-pipeline.global.php**
```php
use Mylab\Session\ZendSessionMiddleware;
use Mylab\Session\ZendSessionMiddlewareFactory;

// ...
'dependencies' => [
    'factories' => [
        Helper\ServerUrlMiddleware::class => Helper\ServerUrlMiddlewareFactory::class,
        Helper\UrlHelperMiddleware::class => Helper\UrlHelperMiddlewareFactory::class,
        ZendSessionMiddleware::class => ZendSessionMiddlewareFactory::class,
    ],
],
'middleware_pipeline' => [
    'always' => [
        'middleware' => [
            Helper\ServerUrlMiddleware::class,
            ZendSessionMiddleware::class,
        ],
        'priority' => 10000,
    ]
]
```

Middleware injects `SessionManager` to `Container` so You can get it with:

```php
use Zend\Session\Container;

Container::getDefaultManager();
```

**How can I use Session Save Handler?**

When created, `SessionManagerFactory` searches `Container` for addition keys. One of them is
```
$saveHandler = $container->get(SaveHandlerInterface::class);
```

Simply, if you wish to attach a save handler to the manager, you will need to write Your own factory, and assign it to the service name
"Zend\Session\SaveHandler\SaveHandlerInterface", (or alias that name to your own service).