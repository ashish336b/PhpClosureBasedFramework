<?php

namespace App\middleware;

use ashish336b\PhpCBF\Request;
use ashish336b\PhpCBF\Response;

class TokenVerify
{
   public function run(Request $request, Response $response)
   {
      return $response->toJSON(["hello" => "token"]);
   }
}
