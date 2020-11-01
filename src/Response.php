<?php

namespace ashish336b\PhpCBF;

class Response
{
   public $views;
   public function __construct()
   {
      $this->views = new Views();
   }
   public function toJSON($obj)
   {
      echo json_encode($obj);
   }
   public function render($view, $params = [])
   {
      return $this->views->render($view, $params);
   }
}
