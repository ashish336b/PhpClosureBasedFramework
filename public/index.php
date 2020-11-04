<?php

use ashish336b\PhpCBF\Application as App;

require_once __DIR__ . "/../vendor/autoload.php";
App::$path = __DIR__ . "/../";


/* Test Case 1 */
App::group(["prefix" => '/admin'], function () {
   App::get("/", 'AdminController@index');
   App::get("/profile/{id}/hello/{userid}/{one}/{two}/{three}/{ok?}/{hello?}/{next?}", 'AdminController@index');
   App::get("/profile/{id}/hello", 'AdminController@index');
});
App::get("/user", 'AdminController@index');
App::run();

   //test urls
   // http://localhost:1212/admin/profile/pid/hello/userid/one/two/three/ok/hello/next