<?php

namespace ashish336b\PhpCBF;

use ashish336b\PhpCBF\abstraction\IDispatch;

class Dispatch implements IDispatch
{
   private $_route;

   public function __construct(Route $route)
   {
      $this->_route = $route;
      $this->_utility = new Utility();
   }
   public function dispatch()
   {
      $uri = '/' . trim($_SERVER['REQUEST_URI'], '/');
      if (isset($this->_route->staticPatternCollection['GET'][$uri])) {
         $this->_route->staticPatternCollection['GET'][$uri][0]();
         return;
      }
      if (isset($this->_route->variablePatternCollection['GET'])) {
         $regex = implode("|", $this->_route->variablePatternCollection['GET']);
         $regex = "/^" . "(?:" . $regex . ")$/";
         if (preg_match($regex, $uri, $matches)) {
            for ($i = 1; '' === $matches[$i]; ++$i);
            $params = [...array_filter(array_slice($matches, 1))];
            list($fn, $paramsName, $middleware) = $this->_route->closureCollection['GET'][$i - 1];
            $urlParams = $this->_utility->combineArr($paramsName, $params); // for $request->params->name
            $fn(...$params);
         } else {
            echo "not Found";
         };
      } else {
         echo "not Found";
      }
   }
}
