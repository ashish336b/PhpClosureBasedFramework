<?php

use ashish336b\PhpCBF\Application;

require_once __DIR__ . "/../vendor/autoload.php";
Application::get("/", function () {
   require_once __DIR__ . "/../app/views/welcome.php";
});
Application::run();

   //test urls
   // http://localhost:1212/home/user/index/id/hello/ok/bollo