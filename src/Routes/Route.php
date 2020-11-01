<?php

namespace ashish336b\PhpCBF\Routes;

use ashish336b\PhpCBF\abstraction\IRoute;
use ashish336b\PhpCBF\Utility;
use Exception;

class Route implements IRoute
{
    private $_utility;
    public $routeCollection = [];
    public $staticPatternCollection = ['GET' => [], 'POST' => [], 'PUT' => [], 'DELETE' => []];
    public $variablePatternCollection = ['GET' => [], 'POST' => [], 'PUT' => [], 'DELETE' => []];
    public $closureCollection = ['GET' => [], 'POST' => [], 'PUT' => [], 'DELETE' => []];
    public function __construct()
    {
        $this->_utility = new Utility();
    }
    public function addRoutes($method, $uriPattern, $action, $middleware = [])
    {
        $urlRegex = $this->_utility->parseURI($uriPattern);
        $params = $this->_utility->getPlaceholderName($uriPattern);
        $this->routeCollection[$method][] = ['fn' => $action, 'pattern' => $urlRegex, 'params' =>
        $params, 'middleware' => $middleware];
        $excep = $this->checkFirstOptPattern($uriPattern);
        if ($excep && $this->isStaticPattern($excep)) {
            if (isset($this->staticPatternCollection[$method][$uriPattern])) {
                throw new Exception("Cannot add same routes twice : $uriPattern \n");
            }
            $this->staticPatternCollection[$method][$excep] = [$action, $middleware];
        }
        if ($this->isStaticPattern($uriPattern)) {
            if (isset($this->staticPatternCollection[$method][$uriPattern])) {
                throw new Exception("Cannot add same routes twice : $uriPattern \n");
            }
            $this->staticPatternCollection[$method][$uriPattern] = [$action, $middleware];
        } else {
            if (isset($this->variablePatternCollection[$method])) {
                if (in_array($urlRegex, $this->variablePatternCollection[$method])) {
                    throw new Exception("Cannot add same routes twice : $uriPattern \n");
                }
            }
            $this->closureCollection[$method][] = [$action, $params, $middleware];
            $this->variablePatternCollection[$method][] = $urlRegex;
        }
    }
    private function isStaticPattern($uriPattern)
    {
        str_replace("/", "\/", $uriPattern);
        if (preg_match('/\{[a-zA-Z0-9-?]+\}/', $uriPattern, $matches)) {
            return false;
        }
        return true;
    }
    private function checkFirstOptPattern($uriPattern)
    {
        if (!$this->isStaticPattern($uriPattern)) {
            return preg_replace('/\/\{[a-z]+\?\}/', "", $uriPattern);
        }
        return false;
    }
}
