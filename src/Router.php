<?php

namespace ashish336b\PhpCBF;

use Exception;

class Router
{
    private $utility;
    protected $currentPrefix = '';
    protected $currentMiddleware = [];
    protected $routeCollection = [];
    /* for routes pattern another dispatch logic */
    protected $staticPatternCollection = [];
    protected $variablePatternCollection = [];
    protected $closureCollection = [];

    public function __construct()
    {
        $this->utility = new Utility();
    }
    public function group($url, $callback)
    {
        if (!isset($url['middleware'])) {
            $url['middleware'] = [];
        }
        $previousPrefix = $this->currentPrefix;
        $this->currentPrefix = $previousPrefix . $url['prefix'];
        $previousMiddleware = $this->currentMiddleware;
        $this->currentMiddleware = $this->utility->pushArr($previousMiddleware, $url['middleware']);
        $callback();
        $this->currentPrefix = $previousPrefix;
        $this->currentMiddleware = $previousMiddleware;
    }

    public function get($url, $action, $middleware = [])
    {
        $uriPattern = $this->currentPrefix . $url;
        $getAllMiddleWare = $this->utility->pushArr($this->currentMiddleware, $middleware);

        $urlRegex = $this->utility->parseURI($uriPattern);
        $params = $this->utility->getPlaceholderName($uriPattern);
        $this->routeCollection['GET'][] = ['fn' => $action, 'pattern' => $urlRegex, 'params' =>
        $params, 'middleware' => $getAllMiddleWare];
        /* for combined regular expression */
        if ($this->isStaticPattern($uriPattern)) {
            if (isset($this->staticPatternCollection['GET'][$uriPattern])) {
                throw new Exception("Cannot add same routes twice : $uriPattern \n");
            }
            $this->staticPatternCollection['GET'][$uriPattern] = [$action, $middleware];
        } else {
            if (isset($this->variablePatternCollection['GET'])) {
                if (in_array($urlRegex, $this->variablePatternCollection['GET'])) {
                    throw new Exception("Cannot add same routes twice : $uriPattern \n");
                }
            }
            $this->closureCollection['GET'][] = [$action, $params, $middleware];
            $this->variablePatternCollection['GET'][] = $urlRegex;
        }
    }
    private function isStaticPattern($uriPattern)
    {
        str_replace("/", "\/", $uriPattern);
        if (preg_match('/\{[a-zA-Z0-9-?]+\}/', $uriPattern, $matches)) {
            return false;
        }
        return true;
    }
    /* Individual regexes matches approach */
    public function dispatch()
    {
        $uri = '/' . trim($_SERVER['REQUEST_URI'], '/');
        $notFoundCount = 0;
        foreach ($this->routeCollection['GET'] as $item) {
            if (preg_match_all('#^' . $item['pattern'] . '$#', $uri, $matches, PREG_OFFSET_CAPTURE)) {
                $matches = array_slice($matches, 1);
                $params = array_map(function ($match, $index) use ($matches) {
                    return isset($match[0][0]) && $match[0][1] != -1 ? trim($match[0][0], '/') : null;
                }, $matches, array_keys($matches));
                $run = true;
                /* if (isset($item['middleware'])) {
                foreach ($item['middleware'] as $eachMiddleware) {
                $midd = new $eachMiddleware();
                if (!$midd->run()) {
                $run = false;
                break;
                }
                }
                } */
                if ($run) {
                    $urlParams = $this->utility->combineArr($item['params'], $params); // for $request->params->name
                    $item['fn'](...$params);
                } else {
                    echo "canno run";
                }
                break;
            }
            $notFoundCount++;
        }
        if ($notFoundCount === count($this->routeCollection['GET'])) {
            echo "404";
        }
    }
    /* combining all regular expression method */
    public function run()
    {
        $uri = '/' . trim($_SERVER['REQUEST_URI'], '/');
        if (isset($this->staticPatternCollection['GET'][$uri])) {
            $this->staticPatternCollection['GET'][$uri][0]();
            return;
        }
        if (isset($this->variablePatternCollection['GET'])) {
            $regex = implode("|", $this->variablePatternCollection['GET']);
            $regex = "/^" . "(?:" . $regex . ")$/";
            if (preg_match($regex, $uri, $matches)) {
                for ($i = 1; '' === $matches[$i]; ++$i);
                $params = [...array_filter(array_slice($matches, 1))];
                list($fn, $paramsName, $middleware) = $this->closureCollection['GET'][$i - 1];
                $urlParams = $this->utility->combineArr($paramsName, $params); // for $request->params->name
                $fn(...$params);
            } else {
                echo "not Found";
            };
        } else {
            echo "not Found";
        }
    }
}
