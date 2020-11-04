<?php

use App\Controller\AdminController;
use ashish336b\PhpCBF\Application;
use ashish336b\PhpCBF\Request;
use ashish336b\PhpCBF\Response;

require_once __DIR__ . "/../vendor/autoload.php";
Application::$path = __DIR__ . "/../";
Application::get("/{id?}", function (Request $request, Response $response) {
   echo $response->render("/welcome", ["ok" => 1]);
}, ['Auth']);
Application::get("/user/{id?}", "AdminController@index");
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
      Application::post('/new/{id}', function (Request $request, Response $response) {
         return $response->toJSON($request);
      });
   });
});
Application::run();

   //test urls
   // http://localhost:1212/home/user/index/id/hello/ok/bollo