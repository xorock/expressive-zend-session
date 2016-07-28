<?php

namespace Mylab\Session;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Session\SessionManager;
use Zend\Session\Container;

final class ZendSessionMiddleware
{    
    /**
     * @var SessionManager
     */
    protected $sessionManager;
    
    /**
     * Constructor
     * 
     * @param SessionManager $sessionManager
     */
    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $next
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $this->sessionManager->start();
        Container::setDefaultManager($this->sessionManager);
        
        $container = new Container('initialized');
        
        if (isset($container->init)) {
            return $next($request, $response);
        }
        
        $this->sessionManager->regenerateId(true);
        $container->init = true;
        
        if ($next) {
            return $next($request, $response);
        }
        return $response;
    }
}
