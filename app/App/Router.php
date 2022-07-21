<?php 

namespace Andromeda\Belajar\PHP\MVC\App;

class Router {

    private static array $routes = [];

    public static function add( string $method,
                                string $path,
                                string $controller, 
                                string $function,
                                array $middleware = []): void    
    {
        self::$routes[] = [
            "method" => $method,
            "path" => $path,
            "controller" => $controller,
            "function" => $function,
            "middleware" => $middleware    
        ];
    }

    public static function run(): void
    {
        $path = "/";
        if (isset($_SERVER["PATH_INFO"])) {
            $path = $_SERVER["PATH_INFO"];
        }        

        $method = $_SERVER["REQUEST_METHOD"];

        foreach (self::$routes as $route) {
            $pattern = '#^' . $route['path'] . '$#';
            if (preg_match($pattern, $path, $variable) && $method == $route['method']) {

                // call middleware
                foreach($route['middleware'] as $middleware) {
                    $instace = new $middleware;
                    $instace->before();
                }

                $controller = new $route['controller'];
                $function = $route['function'];
                //$controller->$function();

                array_shift($variable);
                call_user_func_array([$controller, $function], $variable);

                return;     // baris code di bawah tidak akan di eksekusi
            }
        }

        http_response_code(404);
        echo "CONTROLLER NOT FOUND";
        // echo $_SERVER['PATH_INFO'];
    }
} 
