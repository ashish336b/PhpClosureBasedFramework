<?php

namespace ashish336b\PhpCBF;

use Exception;

class Application
{
   public static $router;
   public static $_instance = null;
   public static $path = '';
   public static $appEvent = ["before" => [], 'after' => []];
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
   private static function getInstance()
   {
      if (!isset(self::$_instance)) {
         self::$_instance = new self();
      }
      return self::$_instance;
   }

   public static function on($params, $closure)
   {
      // if (\strtolower($params) !== 'before' && strtolower($params) !== 'after') {
      //    throw new Exception("First Parameters should be either BEFORE or AFTER");
      // }
      if (strtolower($params) == 'before') {
         self::$appEvent['before'][] = $closure;
      }
      if (strtolower($params) == 'after') {
         self::$appEvent['after'][] = $closure;
      }
   }
}
