<?php

namespace ashish336b\PhpCBF;

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
    public function post($url, $action, $middleware)
    {
        $this->mapRoutes('POST', $url, $action, $middleware);
    }
    private function mapRoutes($method, $url, $action, $middleware = [])
    {
        $uriPattern = $this->currentPrefix . $url;
        $getAllMiddleWare = $this->_utility->pushArr($this->currentMiddleware, $middleware);
        $this->_route->addRoutes($method, $uriPattern, $action, $getAllMiddleWare);
    }
    public function dispatch()
    {
        $uri = '/' . trim($_SERVER['REQUEST_URI'], '/');
        $notFoundCount = 0;
        foreach ($this->routeCollection['GET'] as $item) {
            if (preg_match_all('#^' . $item['pattern'] . '$#', $uri, $matches, PREG_OFFSET_CAPTURE)) {
                $matches = array_slice($matches, 1);
                $params = array_map(function ($match, $index) use ($matches) {
                    return isset($match[0][0]) && $match[0][1] != -1 ? trim($match[0][0], '/') : null;
                }, $matches, array_keys($matches));
                $run = true;
                if ($run) {
                    $urlParams = $this->utility->combineArr($item['params'], $params); // for $request->params->name
                    $item['fn'](...$params);
                } else {
                    echo "cannot run";
                }
                break;
            }
            $notFoundCount++;
        }
        if ($notFoundCount === count($this->routeCollection['GET'])) {
            echo "not Found";
        }
    }
    public function run()
    {
        $this->_dispatch->dispatch();
    }
}
