<?php

namespace ashish336b\PhpCBF;

class Request
{
   public $params;
   public $body;
   public $query;
   public function __construct()
   {
      $this->params = (object)[];
   }
   public function getUrl()
   {
      $url =  '/' . trim($_SERVER['REQUEST_URI'], '/');
      $position = strpos($url, '?');
      if ($position !== false) {
         $url = substr($url, 0, $position);
      }
      return $url;
   }
   public function query($name)
   {
      if (property_exists($this->query, $name)) {
         return $this->query->{$name};
      }
      return null;
   }
   public function body($name)
   {
      if (property_exists($this->body, $name)) {
         return $this->query->{$name};
      }
      return null;
   }
   public function setparams($params)
   {
      if ($params) {
         $this->params = (object) $params;
      } else {
         $this->params = (object)[];
      }
   }
   public function getMethod()
   {
      return $_SERVER['REQUEST_METHOD'];
   }
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
   public function setRequest($params = false)
   {
      $this->setparams($params);
      $this->setBody();
   }
}
