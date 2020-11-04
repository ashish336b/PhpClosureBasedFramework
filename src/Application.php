<?php

namespace ashish336b\PhpCBF;

class Application
{
   public static $router;
   public static $_instance = null;
   public static $path = '';
   /**
    * __construct
    *
    * @return void
    */
   public  function __construct()
   {
      self::$router = new Router();
   }

   /**
    * __callStatic
    *
    * @param  mixed $name
    * @param  mixed $arguments
    * @return void
    */
   public static function __callStatic($name, $arguments)
   {
      self::getInstance();
      self::$router->$name(...$arguments);
   }
   /**
    * getInstance
    *
    * @return void
    */
   public static function getInstance()
   {
      if (!isset(self::$_instance)) {
         self::$_instance = new self();
      }
      return self::$_instance;
   }
}
