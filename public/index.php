<?php

use ashish336b\PhpCBF\Application;
use ashish336b\PhpCBF\Request;
use ashish336b\PhpCBF\Response;

require_once __DIR__ . "/../vendor/autoload.php";
Application::get("/", function () {
   require_once __DIR__ . "/../app/views/welcome.php";
});
Application::post('/', function () {
   echo "post";
});
Application::group(['prefix' => '/admin'], function () {
   Application::get("/login", function () {
      echo "login page";
   });
   Application::group(['prefix' => '/setting'], function () {
      Application::get('/{token}/fetch/{id?}', function (Request $request, Response $response) {

         return $response->toJson($request->query);
         return $response->toJson($request->params);
         require_once __DIR__ . "/../app/views/admin.php";
      });
   });
});
Application::run();

   //test urls
   // http://localhost:1212/home/user/index/id/hello/ok/bollo