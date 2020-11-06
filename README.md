# PhpClosureBasedFramework

This is simple php closure based framework for api development

### Installation

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

### With this Installation is completed. Run the command to open web server in port 1212.

```bash
php -S localhost:1212 -t public/
```

### If you want the functionality of controller , middleware and views go ahead and further configure as :

_Note:_ This is completely optional if you don't want controller and middleware.

#### Then in root directory of project make folder name as shown below.

├───app </br>
│ ├───controller </br>
│ ├───middleware </br>
│ └───views </br>
├───public _Already in project folder_</br>
└───vendor _Already in project folder_</br>

### Then Make controller class and middleware class.

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

### Inside Middleware folder create Auth.php and paste the following code.

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

### Then run below command in terminal to load new class.

```composer
composer dump-autoload
```

### with this Full installation is completed.

# Routes

You can create routes by calling static Class Application

```php
use ashish336b\PhpCBF\Application as App;
App::get("/", function (Request $request, Response $response) {
   echo "My First Route.";
});
```

Method

```php
App::get("/urlPattern" , function(){});
App::post("/urlPattern" , function(){});
App::put("/urlPattern" , function(){});
App::delete("/urlPattern",function(){});
```

### There is another method except get,post,put,delete you can access from `\ashish\PhpCBF\Application` class

```php
App::on("EVENT_TYPE" , function(){});
```

#### EVENT*TYPE can be either \_BEFORE* or _AFTER_

- BEFORE : This event run before all application middleware and routes. Best usecase to set CORS header.
- AFTER : If you need to run a piece of code after running your middleware and routes function use this.

## URL Pattern

- Url pattern is used to define routes. Both static pattern and dynamic pattern can be defined mostly same as laravel.

- ### static Routes: `App::get("/users", Closure);`
  `baseurl/users` url is dispatched with this pattern.

* ### Variable Routes: `APP::get("/home/{id}", closure) `baseurl/home/1`or`baseurl/home/2` ... is dispatch.

* You cannot register two same pattern.

### Optional Pattern.

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
