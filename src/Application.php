<?php

namespace ashish336b\PhpCBF;

use Exception;
use PDO;
use PDOException;

class Application
{
   public static $router;
   public static $_instance = null;
   public static $path = '';
   public static $config;
   public static $appEvent = ["before" => [], 'after' => []];
   public static $pdo = null;
   /**
    * __construct
    *
    * @return void
    */
   public  function __construct()
   {
      self::$router = new Router();
      self::connectDB();
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

   private static function connectDB()
   {
      try {
         self::$pdo = new PDO("mysql:host=" . Application::$config["host"] . ";dbname=" . Application::$config["dbname"] . ";port=" . Application::$config["dbport"], Application::$config["dbuser"], Application::$config["dbpass"]);
         self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch (PDOException $e) {
         die($e->getMessage());
      }
   }
}
