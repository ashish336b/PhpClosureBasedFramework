<?php

namespace App\middleware;

use ashish336b\PhpCBF\Request;
use ashish336b\PhpCBF\Response;

class Auth
{
   public function run(Request $request, Response $response)
   {
      if (!(bool)(array)$request->query) {
         return $response->toJSON(['error' => true, 'message' => "Not Authenticated"]);
      }
      return true;
   }
}
