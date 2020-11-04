<?php

use ashish336b\PhpCBF\Application;
use ashish336b\PhpCBF\Request;
use ashish336b\PhpCBF\Response;

require_once __DIR__ . "/../vendor/autoload.php";
Application::$path = __DIR__ . "/../";
Application::get("/hello/ok/{userid?}", function (Request $request, Response $response) {
   echo $response->toJSON($request);
}, ['Auth']);
Application::get("/user/hello", function (Request $request, Response $response) {
   echo "hello\n";
   echo $response->toJSON($request);
});
Application::get("/user/{ok}/{id?}", "AdminController@index");

Application::post('/', function (Request $request, Response $response) {
   return $response->toJSON($request);
});
Application::group(['prefix' => '/admin', 'middleware' => ['Auth']], function () {
   Application::get("/login", function () {
      echo "login page";
   });
   Application::group(['prefix' => '/setting'], function () {
      Application::get('/{token}/fetch/{id?}', function (Request $request, Response $response) {
         return $response->toJSON($request);
      });
      Application::put('/new/{id}', function (Request $request, Response $response) {
         return $response->toJSON($request);
      });
   });
});
Application::run();

   //test urls
   // http://localhost:1212/home/user/index/id/hello/ok/bollo