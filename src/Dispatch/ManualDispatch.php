<?php
/* this class should me maintained for now dispatch logic is implemented by Dispatch Class */

namespace ashish336b\PhpCBF\Dispatch;

use ashish336b\PhpCBF\abstraction\IDispatch;
use ashish336b\PhpCBF\Request;
use ashish336b\PhpCBF\Response;
use ashish336b\PhpCBF\Routes\Route;
use ashish336b\PhpCBF\Utility;

class ManualDispatch implements IDispatch
{
   private $_route;

   public function __construct(Route $route)
   {
      $this->_route = $route;
      $this->_utility = new Utility();
   }
   public function dispatch(Request $request, Response $response)
   {
      $uri = $request->getUrl();
      $method = $request->getMethod();
      $notFoundCount = 0;
      foreach ($this->_route->routeCollection[$method] as $item) {
         if (preg_match_all('#^' . $item['pattern'] . '$#', $uri, $matches, PREG_OFFSET_CAPTURE)) {
            $matches = array_slice($matches, 1);
            $params = array_map(function ($match, $index) use ($matches) {
               return isset($match[0][0]) && $match[0][1] != -1 ? trim($match[0][0], '/') : null;
            }, $matches, array_keys($matches));
            $run = true;
            if ($run) {
               $urlParams = $this->_utility->combineArr($item['params'], $params);
               $request->setRequest($urlParams);
               $item['fn']($request, $response);
            } else {
               echo "cannot run";
            }
            break;
         }
         $notFoundCount++;
      }
      if ($notFoundCount === count($this->_route->routeCollection['GET'])) {
         echo "not Found";
      }
   }
}
