<?php

namespace ashish336b\PhpCBF\Dispatch;

use ashish336b\PhpCBF\abstraction\IDispatch;
use ashish336b\PhpCBF\Request;
use ashish336b\PhpCBF\Response;
use ashish336b\PhpCBF\Routes\Route;
use ashish336b\PhpCBF\Utility;

class Dispatch implements IDispatch
{
   private $_route;

   public function __construct(Route $route)
   {
      $this->_route = $route;
      $this->_utility = new Utility();
   }
   public function dispatch(Request $request, Response $response)
   {
      $uri = '/' . trim($_SERVER['REQUEST_URI'], '/');
      $method = $_SERVER['REQUEST_METHOD'];
      if (isset($this->_route->staticPatternCollection[$method][$uri])) {
         $this->_route->staticPatternCollection[$method][$uri][0]();
         return;
      }
      if (isset($this->_route->variablePatternCollection[$method])) {
         $regex = implode("|", $this->_route->variablePatternCollection[$method]);
         $regex = "/^" . "(?:" . $regex . ")$/";
         if (preg_match($regex, $uri, $matches)) {
            for ($i = 1; '' === $matches[$i]; ++$i);
            $params = [...array_filter(array_slice($matches, 1))];
            list($fn, $paramsName, $middleware) = $this->_route->closureCollection[$method][$i - 1];
            $urlParams = $this->_utility->combineArr($paramsName, $params);
            $request->setparams($urlParams);
            $fn($request, $response);
         } else {
            echo "not Found";
         };
      } else {
         echo "not Found";
      }
   }
}
