<?php

namespace ashish336b\PhpCBF;

class Request
{
   public $params;
   public $body;
   public $query;
   public function getUrl()
   {
      $url =  '/' . trim($_SERVER['REQUEST_URI'], '/');
      $position = strpos($url, '?');
      if ($position !== false) {
         $url = substr($url, 0, $position);
      }
      return $url;
   }
   public function setparams($params)
   {
      $this->params = (object) $params;
   }
   public function getMethod()
   {
      return $_SERVER['REQUEST_METHOD'];
   }
   public function setBody()
   {
      $data = [];
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
      return $data;
   }
   public function setRequest($params)
   {
      $this->setparams($params);
      $this->setBody();
   }
}
