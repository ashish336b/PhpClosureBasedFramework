<?php

namespace ashish336b\PhpCBF;

use ashish336b\PhpCBF\abstraction\IRoute;
use Exception;

class Route implements IRoute
{
    private $_utility;
    public $routeCollection = [];
    /* for routes pattern another dispatch logic */
    public $staticPatternCollection = [];
    public $variablePatternCollection = ['GET' => []];
    public $closureCollection = [];
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
        /* for combined regular expression */
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
}
