<?php

namespace App\controller;

use ashish336b\PhpCBF\Request;
use ashish336b\PhpCBF\Response;

class AdminController
{
   public function index(Request $request, Response $response)
   {
      $request->url = $request->getUrl();
      return $response->toJSON($request);
   }
}
