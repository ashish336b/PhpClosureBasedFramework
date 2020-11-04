<?php

namespace ashish336b\PhpCBF;

class Views
{
   private $_content = "";
   private $_str;
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
   public function extend($str, $filePath)
   {
      $this->_str = $str;
      $filePath = trim($filePath, "/");
      ob_start();
      include_once Application::$path . "app/views/$filePath.php";
      $this->_content = ob_get_clean();
      ob_start();
   }
   public function include($filePath)
   {
      $filePath = trim($filePath, '/');
      ob_start();
      include_once Application::$path . "app/views/$filePath.php";
      echo ob_get_clean();
   }
   private function getcontent($str)
   {
      $str = "{{" . $str . "}}";
      return str_replace($str, ob_get_clean(), $this->_content);
   }
   public function end()
   {
      echo $this->getcontent($this->_str);
      $this->_content = '';
   }
}
