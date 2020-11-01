<?php

namespace ashish336b\PhpCBF\Dispatch;

use App\controller\AdminController;
use ashish336b\PhpCBF\abstraction\IDispatch;
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
    private function getStaticData($method, $uri)
    {
        if (isset($this->_route->staticPatternCollection[$method][$uri])) {
            return ["static" => true, 'fn' => $this->_route->staticPatternCollection[$method][$uri][0], 'middleware' => $this->_route->staticPatternCollection[$method][$uri][1], 'urlParams' => false];
        }
        return false;
    }
    private function getVariableData($method, $uri)
    {
        $regex = implode("|", $this->_route->variablePatternCollection[$method]);
        $regex = "/^" . "(?:" . $regex . ")$/";
        if (preg_match($regex, $uri, $matches)) {
            for ($i = 1; '' === $matches[$i]; ++$i);
            list($fn, $paramsName, $middleware) = $this->_route->closureCollection[$method][$i - 1];
            $urlParams = $this->_utility->combineArr($paramsName, [...array_filter(array_slice($matches, 1))]);
            return ['static' => false, 'fn' => $fn, 'middleware' => $middleware, 'urlParams' => $urlParams];
        }
        return false;
    }
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

    public function dispatch(Request $request, Response $response)
    {
        $dataToDispatch = $this->dataToDispatch($request, $response);
        if (!$dataToDispatch) {
            echo "not Found";
            return;
        }
        $request->setRequest($dataToDispatch['urlParams']);
        if ($dataToDispatch['fn'] instanceof Closure) {
            echo $dataToDispatch['fn']($request, $response);
            return;
        }
        $arr = explode('@', $dataToDispatch['fn']);
        $className = '\App\controller\\' . $arr[0];
        $controllerObj = new $className();
        echo $controllerObj->{$arr[1]}($request, $response);
    }
}