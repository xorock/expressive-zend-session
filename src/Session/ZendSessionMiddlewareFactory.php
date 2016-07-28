<?php

namespace Mylab\Session;

use Interop\Container\ContainerInterface;
use Zend\Session\SessionManager;

class ZendSessionMiddlewareFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $sessionManager = $container->get(SessionManager::class);
        
        return new ZendSessionMiddleware($sessionManager);
    }
}
