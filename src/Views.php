<?php

namespace ashish336b\PhpCBF;

class Views
{
   public function render($view, array $params = null)
   {
      $view = trim($view, "/");
      if ($params) {
         foreach ($params as $key => $value) {
            $$key = $value;
         }
      }
      ob_start();
      include_once Application::$path . "app/views/$view.php";
      return ob_get_clean();
   }
}
