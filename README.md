# PhpClosureBasedFramework

This is simple php closure based framework for api development

- [Installation](#Installation)

* [Routes](#Routes)

* [Controller](#Controller)
* [Middleware](#Middleware)
* [Request](#Request)
* [Response](#Response)

# Installation

```composer
composer require ashish336b/carpo-php
```

#### After installation go to composer.json and paste following to main object.

```json
 "autoload": {
        "psr-4": {
            "App\\": "app/"
        }
    }
```

After this Make Public folder and paste following code.

```php
<?php

use ashish336b\PhpCBF\Application as App;
use ashish336b\PhpCBF\Request;
use ashish336b\PhpCBF\Response;

require_once __DIR__ . "/../vendor/autoload.php";
App::$path = __DIR__ . "/../";

App::on("BEFORE", function () {
   /* For CORS HEADER */
});
App::on("AFTER", function () {
   /* For Code to run after response if needed */
});
/* Test Case 1 */
App::get("/", function (Request $request, Response $response) {
   echo "welcome to simple closure based framework";
});
App::run();
```

#### With this Installation is completed. Run the command to open web server in port 1212.

```bash
php -S localhost:1212 -t public/
```

#### If you want the functionality of controller , middleware and views go ahead and further configure as :

_Note:_ This is completely optional if you don't want controller and middleware.

#### Then in root directory of project make folder name as shown below.

├───app </br>
│ ├───controller </br>
│ ├───middleware </br>
│ └───views </br>
├───public _Already in project folder_</br>
└───vendor _Already in project folder_</br>

#### Then Make controller class and middleware class.

`AdminController.php`

```php
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
   public function user(Request $request, Response $response)
   {
      echo $response->render("/admin", ['ok' => $request->fullURL]);
   }
}
```

_note:_ you can make class name of any name and same goes with middleware class.

#### Inside Middleware folder create Auth.php and paste the following code.

`Auth.php`

```php
<?php

namespace App\middleware;

use ashish336b\PhpCBF\Request;
use ashish336b\PhpCBF\Response;

class Auth
{
   public function run(Request $request, Response $response)
   {
      return $response->toJSON(["hello" => "ok"]);
      // return true;
   }
}
```

#### Then run below command in terminal to load new class.

```composer
composer dump-autoload
```

#### with this Full installation is completed.

# Routes

You can create routes by calling static Class Application

```php
use ashish336b\PhpCBF\Application as App;
App::get("/", function (Request $request, Response $response) {
   echo "My First Route.";
});
```

Methods

```php
App::get("/urlPattern" , function(){});
App::post("/urlPattern" , function(){});
App::put("/urlPattern" , function(){});
App::delete("/urlPattern",function(){});
```

#### There is another method except get,post,put,delete you can access from `\ashish\PhpCBF\Application` class

```php
App::on("EVENT_TYPE" , function(){});
```

#### EVENTTYPE can be either _BEFORE_ or _AFTER_

- BEFORE : This event run before all application middleware and routes. Best usecase to set CORS header.
- AFTER : If you need to run a piece of code after running your middleware and routes function use this.

### URL Pattern

- Url pattern is used to define routes. Both static pattern and dynamic pattern can be defined mostly same as laravel.

- #### static Routes: `App::get("/users", Closure);`
  `baseurl/users` url is dispatched with this pattern.

* #### Variable Routes: `APP::get("/home/{id}", closure) `baseurl/home/1`or`baseurl/home/2` ... is dispatch.

* You cannot register two same pattern.

#### Optional Pattern.

- `App::get("/user/{id?}" , closure);` </br>
  both `baseurl/user/1` and `baseurl/user` are dispatched here id params is optional.

* You should not define any required placeholder/params after optional placeholder/params.</br>
  `/user/{id?}/{userId}` This is completely wrong. </br>
  `/user/{userId}/{id?}` This is correct way.
* It is good practice to have only one optional params in pattern and it must be last placeholder.
* However two or more optional params are supported if only one required params appears before any optional params. Using more than one optional params may lead to confusion and hard to debug so is not recommended</br>
* You cannot register static Routes after any variable routes that matches static route pattern. Example: </br>
  suppose this variable pattern is defined at first`user/{id}` and you define another pattern `user/home` which matches``user/{id}` It gives you error.
* However you can define `user/home` at first and then `user/{id}`. This is completely fine.

#### Routes Group

- Your can define routes group just like in other famous framework like laravel, slim. etc.

* First Params is array which can have two keys params and middleware.
* Params : For defining common url pattern that appears in every routes group.
* Middleware : name of middleware class that is inside `/App/middleware/` namespace.

```php
App::group(["prefix"=>"/admin"],function(){
   // every GET POST PUT DELETE methods routes can be define.
   App::get("/login",Closure);
   //dispatch /admin/login
   App::get("/login/{id}", Closure);
   //dispatch /admin/login/1 , /admin/login/anything etc.
});
```

- `get`, `post` , `put` and `delete` method have two parameters. url pattern and closure respectively.
- closure can have two paramas $request and $response of type
  `Request` and `Response` respectively

```php
App::get("/user/{id}/{anotherParams}", function (Request $request, Response $response) {
   // Access params value with $request->params.
   $request->params->id;
   $request->params->anotherParams;
});
```

# Controller

You can create controller class inside app/controller with namespace `namespace App\controller;`

```php
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
  public function user(Request $request, Response $response)
  {
     echo $response->render("/admin", ['fullUrl' => $request->fullURL]);
  }
}
```

you can define class and method to execute in route with classname@methodname.

```php
App::get("/admin/user", "AdminController@user");
```

- In routes "AdminController@user" means AdminController is directly inside controller folder which have user method that is to be executed for response.

- If your controller is inside /app/controller/user/UserController.php and method is index that is to be executed then </br>

```php
App::get("/user", "user\UserController@index")
```

# Middleware

Middleware provide a convenient mechanism for filtering HTTP requests entering your application. For Example if you want to check if user is authenticated or not you can check it in middleware. If user is not authenticated you can throw response of 403 which does not allow routes closure to execute.

Your can define Middleware in routes groups as well as individual routes.

```php
App::group(["prefix"=>"/admin","middleware"=>["Auth"]],function(){
   App::get("/",function(){
      echo "/admin";
   })
});
```

Auth class should be inside app/middleware/Auth.php with namespace `namespace app\\middleware\\`;

- Middleware should always return true if none of the condition meets.

```php
<?php

namespace App\middleware;

use ashish336b\PhpCBF\Request;
use ashish336b\PhpCBF\Response;

class Auth
{
   public function run(Request $request, Response $response)
   {
      $auth = false;
      if(!$auth){
         return $response->toJSON(["message" => "not authenticated"]);
      }
      return true;
   }
}
```

- You can define middleware in individual middleware in get,post,put and delete method.

```php
App::get("/user","UserController@index",["Guest"]);
```

# Request

Request and Response can be accessed in both controller method and closure from parameter.

-return url without get params. eg. /admin/index

```php
$request->getUrl();
```

- Return all body from post request as object.

```php
$request->body
```

- Access Body with name. If BODY_NAME is in object of `$request->body` then it return value else return `null`.

```php
$request->body("BODY_NAME");
```

- Get all get params from url as object.

```php
$request->query
```

- Return get query params value. If `QUERY_NAME` is not set it return `null`

```php
$request->query("QUERY_NAME");
```

- return all request headers as object.

```php
$request->allHeaders();
```

- Return header value. If not set return false

```php

$request->header("Header_NAME");
```

# Response

Response class have two method as of now.

- Return object to json.

```php
$response->toJSON($obj);
```

- render method return view containing html files. Accept two params first is path of folder from view folder and another is associative array.

```php
$response->render("/admin",array);
```

- `/admin` is file from `/app/view/admin.php`
- `/admin/login` is file from `/app/view/admin/login.php`

# License & copyright

Copyright (c) Ashish Bhandari

Licensed under the MID [License](LICENSE).
