# PhpClosureBasedFramework

This is simple php closure based framework for api development

- [Installation](#Installation)

* [Routes](#Routes)

* [Controller](#Controller)
* [Model](#Model)
* [Database](#Database)
* [Middleware](#Middleware)
* [Request](#Request)
* [Response](#Response)
* [views](#views)

# Installation

```composer
composer create-project ashish336b/carpo-php-framework blog
```

#### With this Installation is completed. Run the command to open web server in port 1212.

```bash
php -S localhost:1212 -t public/
```

#### Project Folder Structure.

├───app </br>
│ ├───controller </br>
│ ├───middleware </br>
│ ├───model </br>
│ └───views </br>
├───public </br>
└───vendor </br>

- app/controller contains all your controller class for project
- app/middleware contains all your middleware class fro project.
- app/views contains all views files for project.

#### with this Full installation is completed.

# Routes

You can create routes by calling static class `Application`

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

#### There is also another method you can access from `\ashish\PhpCBF\Application` class

```php
App::on("EVENT_TYPE" , function(){});
```

#### EVENTTYPE can be either _BEFORE_ or _AFTER_

- BEFORE : This event run before all application middleware and routes. Best usecase to set CORS header.
- AFTER : If you need to run a piece of code after running your middleware and routes function use this. For example global response header is set here

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
# Model
This contains data related logic such as retriving data from database and passing to controller.<br/>
you have to create model class inside model folder. This folder can contain sub folder to group models.
```php
<?php

namespace App\model;

use ashish336b\PhpCBF\DB;
use ashish336b\PhpCBF\Model;

class Auth extends Model
{
   protected $table = "user";
   public function fetchUser()
   {
      //directly run query from model
      return $this->query("select * from user")->results();
      // fetch all row of table specified in $table
      return $this->fetch();
      // get columns of table
      return $this->getColumns();
   }
}
```
To access model function from controller.
```php
$user  = $this->model("Auth")->fetchUser();
```
Here `Auth` is the name of file that is inside model folder and `fetchUser()` is method inside Auth file/class.
```php
$this->model("Admin\Auth")->fetchUser();
```
In above case Auth class is inside Admin folder which is inside model folder.
# Database
This framework mainly support mysql database. However you can integrate any database with the help other open source project.
* To fetch all data from table(eg. user)
```php
DB::table("user")->get();
```
* To run any query
```php
$result = DB::raw()->query("select * from user where id =  ?",[1])->result();
```
* count result
```php
$noOfRow = DB::raw()->query("select * from user")->count();
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

# views

- views have three method render() , include() and extend().

* render() is already explainded in Response documentation.
* include() method is called inside view file to include files. Usually done for including header and footer. <br/>
  `views/admin.php`

```php
<body>
<?php $this->include("/partials/header") ?>
<h1>MVC Framework <?php echo $ok ?></h1>
<?php $this->include("/partials/footer") ?>
</body>
```

- Example : include header.php inside views/partials/header.php.
- Extend method is used for extending layouts. This replace {{any_string}} with content that comes after calling extend() method and before end() method in views file
  Example. layout.php

```php
<?php $this->include('partials/header'); ?>
{{any_string}}
<?php $this->include("partials/footer") ?>
```

- Extending above layout.php view file.

```php
<?php $this->extend("any_string", "partials/layout"); ?>
<h1>Body <?php echo $ok ?></h1>
<?php $this->end() ?>
```

- replace `{{any_string}}` with `<h1>Body <?php echo $ok ?></h1>`

# License & copyright

Copyright (c) Ashish Bhandari

Licensed under the MID [License](LICENSE).
