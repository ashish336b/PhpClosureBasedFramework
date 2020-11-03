<?php

namespace App\middleware;

use ashish336b\PhpCBF\Request;
use ashish336b\PhpCBF\Response;

class Auth
{
   public function run(Request $request, Response $response)
   {
      if ($request->query("ok") == 'ashish') {
         return $response->toJSON(['error' => true, 'message' => "Not Authenticated"]);
      }
      return true;
   }
}
