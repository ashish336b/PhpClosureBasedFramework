<?php

namespace ashish336b\PhpCBF;

class Response
{
   public function toJSON($obj)
   {
      echo json_encode($obj);
   }
}
