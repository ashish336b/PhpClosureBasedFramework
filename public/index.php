<?php

use ashish336b\PhpCBF\Application as App;
use ashish336b\PhpCBF\Request;
use ashish336b\PhpCBF\Response;

require_once __DIR__ . "/../vendor/autoload.php";
App::$path = __DIR__ . "/../";

App::on("BEFORE", function () {
   /* For CORS HEADER */
});
/* Test Case 1 */
App::group(["prefix" => '/admin'], function () {
   App::get("/", 'AdminController@index');
   App::get("/profile/{id}/hello/{userid}/{one}/{two}/{three}/{ok?}/{hello?}/{next?}", 'AdminController@index');
   App::get("/profile/{id}/hello", 'AdminController@index');
});
App::get("/user", 'AdminController@user');
App::get("/user/{id}/{hello?}", 'AdminController@index');
App::get("/bhola/{id}", 'AdminController@index');
App::get("/", function (Request $request, Response $response) {
   echo "welcome to simple closure based framework";
});
App::post("/", function (Request $request, Response $response) {
   $response->toJSON($request->body);
});
App::run();

   //test urls
   // http://localhost:1212/admin/profile/pid/hello/userid/one/two/three/ok/hello/next