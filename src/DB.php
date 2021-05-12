<?php

namespace ashish336b\PhpCBF;

use PDO;
use PDOException;

class DB
{
   private static $_table, $_pdo, $_instance;
   private $_query, $_count, $_lastInsertID;

   public static function table($table)
   {
      self::$_table = $table;
      self::$_pdo = Application::$pdo;
      return self::getInstance();
   }

   public static function raw()
   {
      self::$_pdo = Application::$pdo;
      return self::getInstance();
   }

   public function get()
   {
      $sql = "SELECT * FROM " . self::$_table;
      $this->_query = self::$_pdo->prepare($sql);
      try {
         $this->_query->execute();
         $result = $this->_query->fetchAll(PDO::FETCH_OBJ);
         return $result;
      } catch (PDOException $exception) {
         throw new PDOException($exception);
      }
   }

   public function query($sql, $params = [], $class = false)
   {
      if ($this->_query = self::$_pdo->prepare($sql)) {
         $x = 1;
         if (count($params)) {
            foreach ($params as $param) {
               $this->_query->bindValue($x, $param);
               $x++;
            }
         }
      }
      try {
         $this->_query->execute();
         if ($class) {
            $this->_result = $this->_query->fetchAll(PDO::FETCH_CLASS, $class);
         } else {
            $this->_result = $this->_query->fetchALL(PDO::FETCH_OBJ);
         }
         $this->_count = $this->_query->rowCount();
         $this->_lastInsertID = self::$_pdo->lastInsertID();
      } catch (PDOException $e) {
         die($e->getMessage());
      }
      return $this;
   }
   public function count()
   {
      return $this->_count;
   }

   public function lastID()
   {
      return $this->_lastInsertId;
   }

   public function results()
   {
      return $this->_result;
   }

   public function getColumns()
   {
      return $this->query("SHOW COLUMNS FROM " . self::$_table)->results();
   }

   public static function getInstance()
   {
      // Create it if it doesn't exist.
      if (!self::$_instance) {
         self::$_instance = new DB();
      }
      return self::$_instance;
   }
}
