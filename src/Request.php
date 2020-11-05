<?php

namespace ashish336b\PhpCBF;

class Request
{
   public $params;
   public $body;
   public $query;
   public $protocol;
   public $host;
   public $fullURL;
   private $header;
   public function __construct()
   {
      $this->params = (object)[];
      $this->protocol = $this->protocol();
      $this->setRequestedHeaders();
      $this->setHost();
      $this->setBody();
      $this->fullURL = $this->protocol . $this->host . $this->getUrl();
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
    * setHost
    *
    * @return void
    */
   private function setHost()
   {
      $this->host = $this->header('Host');
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
   private function setBody()
   {
      if ($this->getMethod() == 'POST') {
         if (isset($this->setRequestedHeaders()['Content-Type']) && $this->setRequestedHeaders()['Content-Type'] == "application/json") {
            $_POST = file_get_contents('php://input');
            $_POST = json_decode($_POST, TRUE);
            $this->body = $_POST;
         } else {
            foreach ($_POST as $key => $value) {
               $this->body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
         }
      }
      if ($this->getMethod() == 'GET') {
         foreach ($_GET as $key => $value) {
            $this->query[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
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
   /**
    * setRequestedHeaders
    * Description : Set all requested header to header property.
    * @return void
    */
   private function setRequestedHeaders()
   {
      $headers = array();
      foreach ($_SERVER as $key => $value) {
         if (substr($key, 0, 5) <> 'HTTP_') {
            continue;
         }
         $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
         $headers[$header] = $value;
      }
      $this->header =  $headers;
   }

   /**
    * header
    * Description : Return the value of header passed as parameters.
    * @param  mixed $header
    * @return void
    */
   public function header($header)
   {
      if (isset($this->header[$header])) {
         return $this->header[$header];
      }
      return false;
   }
   /**
    * allHeaders
    * Description : Return all requested Header in object
    * @return void
    */
   public function allHeaders()
   {
      if (count($this->header)) {
         return (object)$this->header;
      }
      return false;
   }
}
