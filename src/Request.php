<?php

namespace ashish336b\PhpCBF;

class Request
{
   public $params;
   public $body;
   public $query;
   public $protocol;
   public function __construct()
   {
      $this->params = (object)[];
      $this->protocol = $this->protocol();
   }
   /**
    * getUrl
    *
    * @return void
    */
   public function getUrl()
   {
      $url =  '/' . trim($_SERVER['REQUEST_URI'], '/');
      $position = strpos($url, '?');
      if ($position !== false) {
         $url = substr($url, 0, $position);
      }
      return $url;
   }
   /**
    * query
    *
    * @param  mixed $name
    * @return void
    */
   public function query($name)
   {
      if (property_exists($this->query, $name)) {
         return $this->query->{$name};
      }
      return null;
   }
   /**
    * body
    *
    * @param  mixed $name
    * @return void
    */
   public function body($name)
   {
      if (property_exists($this->body, $name)) {
         return $this->query->{$name};
      }
      return null;
   }
   /**
    * setparams
    *
    * @param  mixed $params
    * @return void
    */
   public function setparams($params)
   {
      if ($params) {
         $this->params = (object) $params;
      } else {
         $this->params = (object)[];
      }
   }
   /**
    * getMethod
    *
    * @return void
    */
   public function getMethod()
   {
      return $_SERVER['REQUEST_METHOD'];
   }
   /**
    * setBody
    *
    * @return void
    */
   public function setBody()
   {
      if ($this->getMethod() == 'GET') {
         foreach ($_GET as $key => $value) {
            $this->query[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
         }
      }
      if ($this->getMethod() == 'POST') {
         foreach ($_POST as $key => $value) {
            $this->body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
         }
      }
      $this->body = (object) $this->body;
      $this->query = (object) $this->query;
   }
   private function protocol()
   {
      $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, strpos($_SERVER["SERVER_PROTOCOL"], '/'))) . '://';
      return $protocol;
   }
}
