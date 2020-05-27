<?php 

namespace Config;

use Root\Routes;
use Config\Response;

class Router
{
    public static function run()
    {
        $routes = self::retrieveRoutes($_SERVER);
        if ($routes) {
            self::retrieveMethods($routes);
        } else {
            Response::failure('Route not found !', 404);
        }
    }

    private static function retrieveMethods($request)
    {
        $controller = explode('@', $request['value'][0]);

        $file = './app/' . $controller[0] . '.php';

        $method = $controller[1];

        if (file_exists($file)) {
            $className =  'App\\' . $controller[0];

            $class = new $className();

            if (method_exists($class, $method)) {
                $class->$method($request['query']);
            } else {
                Response::failure('Method ' . $method . ' doesnt exist in ' . $file . ' !', 404);
            }

        } else {
            Response::failure('File ' . $file . ' not found !', 404);
        }
    }
    
    private static function retrieveRoutes($request)
    {
        $urlPath = parse_url($request['REQUEST_URI']);

        $queryParams = [];

        if (! empty($urlPath['query'])) {
            parse_str($urlPath['query'], $queryParams);
        }
        
        $uriCount = substr_count(rtrim($request['REQUEST_URI'], '/'), '/');

        $routes = [];

        foreach(Routes::ROUTES as $key => $value) {

            $keyCount = substr_count($key, '/');

            if ($uriCount === $keyCount && $value[1] === $request['REQUEST_METHOD']) {

                similar_text($key, $urlPath['path'], $percent);

                if ($percent == 100) {
                    $routes[$percent] = [
                        'path' => $urlPath['path'],
                        'query' => $queryParams,
                        'value' => $value
                    ];
                    break;

                } elseif ($percent >= 75) {
                    $routes[$percent] = [
                        'path' => $urlPath['path'],
                        'query' => $queryParams,
                        'value' => $value
                    ];
                }
            }
        }

        krsort($routes);

        return array_values($routes)[0];
    }
}