<?php

namespace ashish336b\PhpCBF;

class Controller
{
   public  $middleware = [];
   public function setMiddleware($middleware)
   {
      array_push($middleware, ...$middleware);
   }
}
