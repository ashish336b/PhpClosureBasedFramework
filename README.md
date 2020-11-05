# PhpClosureBasedFramework

This is simple php closure based framework for api development

### Installation

```composer
composer require ashish336b/carpo-php
```

#### After installing go to composer.json and paste following to main object.

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

#### Then in root directory of project make folder name as shown below.

├───app </br>
│ ├───controller </br>
│ ├───middleware </br>
│ └───views </br>
├───public _Already in project folder_</br>
└───vendor _Already in project folder_</br>

### Then Make controller class and middleware class.

`AdminController`

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

### with this installation of project is completed.
