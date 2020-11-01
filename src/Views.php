<?php

namespace ashish336b\PhpCBF;

class Views
{
   public function render($view, array $params = [])
   {
      $view = trim($view, "/");
      foreach ($params as $key => $value) {
         $$key = $value;
      }

      ob_start();
      include_once Application::$path . "app/views/$view.php";
      return ob_get_clean();
   }
}
