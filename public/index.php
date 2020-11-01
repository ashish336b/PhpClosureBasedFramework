<?php

use App\Controller\AdminController;
use ashish336b\PhpCBF\Application;
use ashish336b\PhpCBF\Request;
use ashish336b\PhpCBF\Response;

require_once __DIR__ . "/../vendor/autoload.php";
Application::get("/", function () {
   require_once __DIR__ . "/../app/views/welcome.php";
});
// Application::get("/user/{id?}", "AdminController@index");
Application::post('/', function (Request $request, Response $response) {
   return $response->toJSON($_REQUEST);
});
Application::get("/user/{name}/{id?}", "AdminController@index");
Application::group(['prefix' => '/admin'], function () {
   Application::get("/login", function () {
      echo "login page";
   });
   Application::group(['prefix' => '/setting'], function () {
      Application::get('/{token}/fetch/{id?}', function (Request $request, Response $response) {
         $response->toJSON($request);
      });
   });
});
Application::run();

   //test urls
   // http://localhost:1212/home/user/index/id/hello/ok/bollo