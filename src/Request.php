<?php

namespace ashish336b\PhpCBF;

class Request
{
   public $params;
   public function getUrl()
   {
      return '/' . trim($_SERVER['REQUEST_URI'], '/');
   }
   public function setparams($params)
   {
      $this->params = (object) $params;
   }
}
