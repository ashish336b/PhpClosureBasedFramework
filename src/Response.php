<?php

namespace ashish336b\PhpCBF;

class Response
{
   public $views;
   public function __construct()
   {
      $this->views = new Views();
   }
   /**
    * toJSON
    *
    * @param  mixed $obj
    * @return void
    */
   public function toJSON($obj)
   {
      header('Content-Type: application/json');
      echo json_encode($obj);
   }
   /**
    * render
    *
    * @param  mixed $view
    * @param  mixed $params
    * @return void
    */
   public function render($view, $params = [])
   {
      return $this->views->render($view, $params);
   }
}
