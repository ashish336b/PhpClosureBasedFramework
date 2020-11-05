<?php

namespace App\middleware;

use ashish336b\PhpCBF\Request;
use ashish336b\PhpCBF\Response;

class Auth
{
   public function run(Request $request, Response $response)
   {
      return $response->toJSON(["hello" => "ok"]);
      // return true;
   }
}
