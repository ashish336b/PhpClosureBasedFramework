<?php

namespace ashish336b\PhpCBF;

class Application
{
   public static $router;
   public static $_instance = null;
   public static $path = '';
   public  function __construct()
   {
      self::$router = new Router();
   }

   public static function __callStatic($name, $arguments)
   {
      self::getInstance();
      self::$router->$name(...$arguments);
   }
   public static function getInstance()
   {
      if (!isset(self::$_instance)) {
         self::$_instance = new self();
      }
      return self::$_instance;
   }
}
