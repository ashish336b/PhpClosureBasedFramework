<?php

namespace ashish336b\PhpCBF\abstraction;

interface IRoute
{
   public function addRoutes(
      $method,
      $uriPattern,
      $action,
      $middleware = []
   );
}
