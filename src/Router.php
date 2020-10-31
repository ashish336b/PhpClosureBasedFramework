<?php

namespace ashish336b\PhpCBF;

use ashish336b\PhpCBF\Dispatch\Dispatch;
use ashish336b\PhpCBF\Routes\Route;

class Router
{
    private $_utility;
    private $_route;
    private $_dispatch;
    protected $currentPrefix = '';
    protected $currentMiddleware = [];

    public function __construct()
    {
        $this->_utility = new Utility();
        $this->_route = new Route();
        $this->_dispatch = new Dispatch($this->_route);
    }
    public function group($url, $callback)
    {
        if (!isset($url['middleware'])) {
            $url['middleware'] = [];
        }
        $previousPrefix = $this->currentPrefix;
        $this->currentPrefix = $previousPrefix . $url['prefix'];
        $previousMiddleware = $this->currentMiddleware;
        $this->currentMiddleware = $this->_utility->pushArr($previousMiddleware, $url['middleware']);
        $callback();
        $this->currentPrefix = $previousPrefix;
        $this->currentMiddleware = $previousMiddleware;
    }

    public function get($url, $action, $middleware = [])
    {
        $this->mapRoutes('GET', $url, $action, $middleware);
    }
    public function post($url, $action, $middleware = [])
    {
        $this->mapRoutes('POST', $url, $action, $middleware);
    }
    private function mapRoutes($method, $url, $action, $middleware = [])
    {
        $uriPattern = $this->currentPrefix . $url;
        $getAllMiddleWare = $this->_utility->pushArr($this->currentMiddleware, $middleware);
        $this->_route->addRoutes($method, $uriPattern, $action, $getAllMiddleWare);
    }
    public function run()
    {
        $this->_dispatch->dispatch();
    }
}
