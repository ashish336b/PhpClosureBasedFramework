<?php

namespace ashish336b\PhpCBF;

class Router
{
   protected $currentPrefix = '';
   protected $currentMiddleware = [];
   protected $routeCollection = [];
   /* for routes pattern another dispatch logic */
   protected $patternCollection = [];
   protected $closureCollection = [];

   public function group($url, $callback)
   {
      if (!isset($url['middleware'])) {
         $url['middleware'] = [];
      }
      $previousPrefix = $this->currentPrefix;
      $this->currentPrefix = $previousPrefix . $url['prefix'];
      $previousMiddleware = $this->currentMiddleware;
      $this->currentMiddleware = $this->combineArray($previousMiddleware, $url['middleware']);
      $callback();
      $this->currentPrefix = $previousPrefix;
      $this->currentMiddleware = $previousMiddleware;
   }

   private function combineArray($arr1, $arr2)
   {
      array_push($arr1, ...$arr2);
      return $arr1;
   }

   public function get($url, $action, $middleware = [])
   {
      $uriPattern = $this->currentPrefix . $url;
      $getAllMiddleWare = $this->combineArray($this->currentMiddleware, $middleware);

      $urlRegex = $this->parseUrl($uriPattern);
      $params = $this->getParamsName($uriPattern);
      $this->routeCollection['GET'][] = ['fn' => $action, 'pattern' => $urlRegex, 'params' =>
      $params, 'middleware' => $getAllMiddleWare];
      $this->patternCollection['GET'][] = $urlRegex;
      $this->closureCollection['GET'][] = [$action, $params, $middleware];
   }
   private function getParamsName($uriPattern)
   {
      if (preg_match_all('/\{[a-z]+[?]?\}/', $uriPattern, $matches)) {
         $matches[0] = array_map(function ($value) {
            return preg_replace('/[{\?}]/', "", $value);
         }, $matches[0]);
         return $matches[0];
      }
   }
   private function parseUrl($pattern)
   {
      $pattern = str_replace("/", "\/", $pattern);
      // below line check if /{id} is in the string and replace with regex
      $regix = $this->replaceWithRegix('/\/\{[\w]*\}/', "/([\w]*)", $pattern);
      // below line check if /{id?} is in the string and replace with apporiate regex
      $regix = $this->replaceWithRegix("/([\\\]\/\{[a-z]+\?\})/", '(\/[\w]*)?', $regix);
      // below line check if /{id:num} is in the string and replace with apporiate regex
      $regix = $this->replaceWithRegix('/\/\{[\w]*:num\}/', '(\/[\d]*)?', $regix);
      return $regix;
   }
   private function replaceWithRegix($patternToReplace, $replaceWith, $string)
   {
      if (preg_match($patternToReplace, $string, $matches)) {
         return preg_replace($patternToReplace, $replaceWith, $string);
      }
      return $string;
   }
   private function combineArr($keys, $values = [])
   {
      if (!count($values) && !$keys) {
         return false;
      }
      if (count($keys) != count($values)) {
         return false;
      }
      $result = array();

      foreach ($keys as $i => $k) {
         $result[$k][] = $values[$i];
      }

      array_walk($result, function (&$v) {
         $v = (count($v) == 1) ? array_pop($v) : $v;
      });

      return $result;
   }
   /* Individual regexes matches approach */
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
            /* if (isset($item['middleware'])) {
               foreach ($item['middleware'] as $eachMiddleware) {
                  $midd = new $eachMiddleware();
                  if (!$midd->run()) {
                     $run = false;
                     break;
                  }
               }
            } */
            if ($run) {
               $urlParams = $this->combineArr($item['params'], $params); // for $request->params->name
               $item['fn'](...$params);
            } else {
               echo "canno run";
            }
            break;
         }
         $notFoundCount++;
      }
      if ($notFoundCount === count($this->routeCollection['GET'])) {
         echo "404";
      }
   }
   /* combining all regular expression method */
   public function run()
   {
      $uri = '/' . trim($_SERVER['REQUEST_URI'], '/');
      $regex = implode("|", $this->patternCollection['GET']);
      $regex = "/^" . "(?:" . $regex . ")$/";
      if (preg_match($regex, $uri, $matches)) {
         for ($i = 1; '' === $matches[$i]; ++$i);
         $params = [...array_filter(array_slice($matches, 1))];
         list($fn, $paramsName, $middleware) = $this->closureCollection['GET'][$i - 1];
         $urlParams = $this->combineArr($paramsName, $params); // for $request->params->name
         $fn(...$params);
      } else {
         echo "not Found";
      };
   }
   /* best is chunk split algorithm */
}
