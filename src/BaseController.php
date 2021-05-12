<?php

namespace ashish336b\PhpCBF;

use Exception;

class BaseController
{
   public function model($name)
   {
      if (class_exists("App\\model\\$name")) {
         $className = "App\\model\\$name";
         $instance = new $className();
         return $instance;
      } else {
         throw new Exception("cannot find class $name");
      }
   }
}
