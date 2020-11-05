<?php

namespace ashish336b\PhpCBF\Dispatch;

use ashish336b\PhpCBF\abstraction\IDispatch;
use ashish336b\PhpCBF\Application;
use ashish336b\PhpCBF\Request;
use ashish336b\PhpCBF\Response;
use ashish336b\PhpCBF\Routes\Route;
use ashish336b\PhpCBF\Utility;
use Closure;

class Dispatch implements IDispatch
{
    private $_route;
    public function __construct(Route $route)
    {
        $this->_route = $route;
        $this->_utility = new Utility();
    }
    /**
     * getStaticData
     * Description : If request URL matches static pattern it return all data associated with routes
     *               like params , middleware, closure, body , query
     * @param  mixed $method
     * @param  mixed $uri
     * @return void
     */
    private function getStaticData($method, $uri)
    {
        if (isset($this->_route->staticPatternCollection[$method][$uri])) {
            list($fn, $paramsName, $middleware) = $this->_route->staticPatternCollection[$method][$uri];
            return ["static" => true, 'fn' => $fn, 'middleware' => $middleware, 'urlParams' => $paramsName];
        }
        return false;
    }
    /**
     * getVariableData
     * Description : matches all variable pattern with requested URL. It it matches return all
     *               data associated with the routes like params , query , body, middleware, closure
     * @param  mixed $method
     * @param  mixed $uri
     * @return void
     */
    private function getVariableData($method, $uri)
    {
        $regex = implode("|", $this->_route->variablePatternCollection[$method]);
        $regex = "~^" . "(?:" . $regex . ")$~x";
        if (preg_match($regex, $uri, $matches)) {
            for ($i = 1; '' === $matches[$i]; ++$i);
            list($fn, $paramsName, $middleware) = $this->_route->closureCollection[$method][$i];
            $urlParams = $this->_utility->combineArr($paramsName, [...array_filter(array_slice($matches, 1))]);
            return ['static' => false, 'fn' => $fn, 'middleware' => $middleware, 'urlParams' => $urlParams];
        }
        return false;
    }
    /**
     * dataToDispatch
     *
     * @param  mixed $request
     * @param  mixed $response
     * @return void
     */
    private function dataToDispatch(Request $request, Response $response)
    {
        $uri = $request->getUrl();
        $method = $request->getMethod();
        $staticData = $this->getStaticData($method, $uri);
        if ($staticData) {
            return $staticData;
        }
        $variableData = $this->getVariableData($method, $uri);
        if ($variableData) {
            return $variableData;
        }
        return false;
    }

    /**
     * dispatch
     *
     * @param  mixed $request
     * @param  mixed $response
     * @return void
     */
    public function dispatch(Request $request, Response $response)
    {
        $dataToDispatch = $this->dataToDispatch($request, $response);
        if (!$dataToDispatch) {
            echo "not Found";
            return;
        }
        $this->initRequest($request, $dataToDispatch['urlParams']);
        $this->dispatchBeforeEvent();
        if ($dataToDispatch['fn'] instanceof Closure) {
            if ($this->dispatchMiddleware($dataToDispatch['middleware'], $request, $response)) {
                $dataToDispatch['fn']($request, $response);
                $this->dispatchAfterEvent();
                return;
            }
            return;
        }
        $arr = explode('@', $dataToDispatch['fn']);
        $className = '\App\controller\\' . $arr[0];
        $controllerObj = new $className();
        if ($this->dispatchMiddleware($dataToDispatch['middleware'], $request, $response)) {
            $controllerObj->{$arr[1]}($request, $response);
            $this->dispatchAfterEvent();
            return;
        }
        return;
    }
    /**
     * dispatchMiddleware
     *
     * @param  mixed $middlewares
     * @param  mixed $request
     * @param  mixed $response
     * @return void
     */
    public function dispatchMiddleware($middlewares, $request, $response)
    {
        foreach ($middlewares as $item) {
            $className = '\App\middleware\\' . $item;
            $obj = new $className();
            $isAllowed = $obj->run($request, $response);
            if ($isAllowed !== true) {
                echo $isAllowed;
                return false;
            }
        }
        return true;
    }

    /**
     * initRequest
     *
     * @param  mixed $request
     * @param  mixed $params
     * @return void
     */
    private function initRequest($request, $params)
    {

        $request->setparams($params);
    }
    private function dispatchBeforeEvent()
    {
        $closureArr = Application::$appEvent;
        foreach ($closureArr['before'] as $item) {
            $item();
        }
    }
    private function dispatchAfterEvent()
    {
        foreach (Application::$appEvent['after'] as $item) {
            $item();
        }
    }
}
