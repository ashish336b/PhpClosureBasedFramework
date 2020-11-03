<?php

namespace App\middleware;

use ashish336b\PhpCBF\Request;
use ashish336b\PhpCBF\Response;

class CheckName
{
   public function run(Request $request, Response  $response)
   {
      if ($request->query->name == 'ashish') {
         return true;
      }
      return $response->toJSON(['message' => 'Ashish must be authenticated']);
   }
}
