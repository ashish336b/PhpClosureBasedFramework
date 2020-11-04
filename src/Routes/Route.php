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
    /**
     * addRoutes
     *
     * @param  mixed $method
     * @param  mixed $uriPattern
     * @param  mixed $action
     * @param  mixed $middleware
     * @return void
     */
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
            $this->staticPatternCollection[$method][$excep] = [$action, $this->_utility->combineArr($params, []), $middleware];
        }
        if ($this->isStaticPattern($uriPattern)) {
            if (isset($this->staticPatternCollection[$method][$uriPattern])) {
                throw new Exception("Cannot add same routes twice : $uriPattern \n");
            }
            $this->validateStaticPattern($method, $uriPattern);
            $this->staticPatternCollection[$method][$uriPattern] = [$action, [], $middleware];
        } else {
            if (isset($this->variablePatternCollection[$method])) {
                if (in_array($urlRegex, $this->variablePatternCollection[$method])) {
                    throw new Exception("Cannot add same routes twice : $uriPattern \n");
                }
            }
            if (!sizeof($this->closureCollection[$method])) {
                $this->closureCollection[$method][1] = [$action, $params, $middleware];
            } else {
                $getMaxIndex = max(array_keys($this->closureCollection[$method]));
                $index = $getMaxIndex + count($this->closureCollection[$method][$getMaxIndex][1]);
                $this->closureCollection[$method][$index] = [$action, $params, $middleware];
            }
            $this->variablePatternCollection[$method][] = $urlRegex;
        }
    }
    /**
     * isStaticPattern
     *
     * @param  mixed $uriPattern
     * @return void
     */
    private function isStaticPattern($uriPattern)
    {
        str_replace("/", "\/", $uriPattern);
        if (preg_match('/\{[a-zA-Z0-9-?]+\}/', $uriPattern, $matches)) {
            return false;
        }
        return true;
    }
    /**
     * checkFirstOptPattern
     * Description : check if optional params appears first /user/{id?} without required params     *               appearing in first 
     *               for /user/{id?} => return /user
     *               for /user/{id}/{pid?} => return false
     * @param  mixed $uriPattern
     * @return void
     */
    private function checkFirstOptPattern($uriPattern)
    {
        if (!$this->isStaticPattern($uriPattern)) {
            $pattern = preg_replace('/\/\{[a-z]+\?\}/', "", $uriPattern);
            return $pattern ? $pattern : "/";
        }
        return false;
    }
    /**
     * validateStaticPattern
     * Description : If static pattern is added it's check if already added variable pattern matches *               with currently added static pattern if it matches then its throw errors else it *               return true
     *               Example : you should not add /user/home after adding routes /user/{id} since
     *               /user/{id} matches /user/home that helps to remove conflict 
     *               you can add /user/{id} after /user/home.
     * @param  mixed $method
     * @param  mixed $pattern
     * @return void
     */
    public function validateStaticPattern($method, $pattern)
    {
        $regex = implode("|", $this->variablePatternCollection[$method]);
        $regex = "/^" . "(?:" . $regex . ")$/";
        if (preg_match($regex, $pattern, $matches)) {
            throw new Exception("$pattern that matches variable pattern is already registered");
        }
        return true;
    }
}
