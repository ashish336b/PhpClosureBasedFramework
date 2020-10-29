<?php
$rustart = getrusage();

use ashish336b\PhpCBF\Application;
use ashish336b\PhpCBF\Router;

require_once __DIR__ . "/../vendor/autoload.php";
$env = 0;
if ($env) {
   Application::group(['prefix' => '/hi'], function () {
      Application::get("/bye/{id}", function ($id) {
         echo "bye $id";
      });
   });
   Application::group(['prefix' => '/home'], function () {
      Application::group(['prefix' => '/user'], function () {
         Application::get('/index/{id}/{hello?}/ok/{bollo}', function ($id, $hello, $bollo) {
            echo "$id\n $hello \n$bollo";
         });
      });
   });
   Application::dispatch();
} else {
   $app = new Router();
   $app->group(['prefix' => "/home", 'middleware' => ["SimpleMiddleware"]], function () use ($app) {
      $app->group(['prefix' => '/user', 'middleware' => ['authMiddleware']], function () use ($app) {
         $app->get("/index/{id}/{hello?}/ok/{bollo}", function ($id, $hello = null, $bollo) {
            echo "$id\n $hello \n$bollo";
         }, ['MelloMiddleware']);
         $app->get("/index1/{userId?}", function () {
         });
      });
      $app->get("/bhalu", function () {
         echo "ok";
      });
   });

   $app->group(['prefix' => '/hello'], function () use ($app) {
      $app->get("/index/{id}", function () {
         echo "hello/index/{id}";
      });
   });


   $app->run();
}

/* function rutime($ru, $rus, $index)
{
   return ($ru["ru_$index.tv_sec"] * 1000 + intval($ru["ru_$index.tv_usec"] / 1000))
      -  ($rus["ru_$index.tv_sec"] * 1000 + intval($rus["ru_$index.tv_usec"] / 1000));
}

echo "</br>";
$ru = getrusage();
echo "This process used " . rutime($ru, $rustart, "utime") .
   " ms for its computations\n";
echo "It spent " . rutime($ru, $rustart, "stime") .
   " ms in system calls\n"; */

   //test urls
   // http://localhost:1212/home/user/index/id/hello/ok/bollo