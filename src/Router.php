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
    protected $request;
    protected $response;
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->_utility = new Utility();
        $this->_route = new Route();
        $this->_dispatch = new Dispatch($this->_route);
        $this->request = new Request();
        $this->response = new Response();
    }
    /**
     * group
     *
     * @param  mixed $url
     * @param  mixed $callback
     * @return void
     */
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

    /**
     * get
     *
     * @param  mixed $url
     * @param  mixed $action
     * @param  mixed $middleware
     * @return void
     */
    public function get($url, $action, $middleware = [])
    {
        $this->mapRoutes('GET', $url, $action, $middleware);
    }
    /**
     * post
     *
     * @param  mixed $url
     * @param  mixed $action
     * @param  mixed $middleware
     * @return void
     */
    public function post($url, $action, $middleware = [])
    {
        $this->mapRoutes('POST', $url, $action, $middleware);
    }
    /**
     * put
     *
     * @param  mixed $url
     * @param  mixed $action
     * @param  mixed $middleware
     * @return void
     */
    public function put($url, $action, $middleware = [])
    {
        $this->mapRoutes('PUT', $url, $action, $middleware);
    }
    /**
     * delete
     *
     * @param  mixed $url
     * @param  mixed $action
     * @param  mixed $middleware
     * @return void
     */
    public function delete($url, $action, $middleware = [])
    {
        $this->mapRoutes('DELETE', $url, $action, $middleware);
    }
    /**
     * mapRoutes
     *
     * @param  mixed $method
     * @param  mixed $url
     * @param  mixed $action
     * @param  mixed $middleware
     * @return void
     */
    private function mapRoutes($method, $url, $action, $middleware = [])
    {
        $uriPattern = $this->currentPrefix . $url;
        $uriPattern = rtrim($uriPattern, "/"); // convert /user/home/ to /user/home
        if ($uriPattern == "") {
            $uriPattern = "/";
        }
        $getAllMiddleWare = $this->_utility->pushArr($this->currentMiddleware, $middleware);
        $this->_route->addRoutes($method, $uriPattern, $action, $getAllMiddleWare);
    }
    /**
     * run
     *
     * @return void
     */
    public function run()
    {
        $this->_dispatch->dispatch($this->request, $this->response);
    }
}
