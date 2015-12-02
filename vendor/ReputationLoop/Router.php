<?php
namespace ReputationLoop;

use Slim\Environment;
use Slim\Route;
use Slim\Http\Request;

/**
 * Router class - handles the parsing of string literals into controller
 * class and method callback.
 *
 * @author Akin Williams
 */
class Router
{

    /**
     *
     * @var array
     */
    protected $routes;

    /**
     *
     * @var Request
     */
    protected $request;

    /**
     *
     * @var ambiguous
     */
    protected $errorHandler;

    /**
     * Constructor
     */
    public function __construct()
    {
        $env = Environment::getInstance();
        $this->request = new Request($env);
        $this->routes = array();
    }

    /**
     * Add Routes
     *
     * @param array $routes            
     */
    public function addRoutes($routes)
    {
        foreach ($routes as $route => $path) {
            
            $method = "any";
            
            if (strpos($path, "@") !== false) {
                list ($path, $method) = explode("@", $path);
            }
            
            $func = $this->processCallback($path);
            
            $r = new Route($route, $func);
            $r->setHttpMethods(strtoupper($method));
            
            array_push($this->routes, $r);
        }
    }

    /**
     * Parse string path into controller class/method call.
     *
     * @param string $path            
     */
    protected function processCallback($path)
    {
        $class = $callback = false;
        
        if (strpos($path, ":") !== false) {
            list ($class, $path) = explode(":", $path);
        }
        
        // Set default Controller if missing.
        if (! $class) {
            $class = "Main";
        }
        
        // Set default path if missing.
        if (! $path) {
            $path = "index";
        }
        
        $method = $path;
        
        $callback = function () use($class, $method) {
            $class_name = '\Controller\\' . $class;
            if (class_exists($class_name)) {
                $class = new $class_name();
                
                $args = func_get_args();
                
                return call_user_func_array(array(
                    $class,
                    $method
                ), $args);
            }
        };
        
        return $callback;
    }

    /**
     * Set callback for 404 error page.
     *
     * @param string $path            
     */
    public function set404Handler($path)
    {
        $this->errorHandler = $this->processCallback($path);
    }

    /**
     * Run application.
     * Find route matching requesst uri and execute associated controller callback, or
     * set 404 variable.
     */
    public function run()
    {
        $display404 = true;
        $uri = $this->request->getResourceUri();
        $method = $this->request->getMethod();
        
        foreach ($this->routes as $i => $route) {
            if ($route->matches($uri)) {
                if ($route->supportsHttpMethod($method) || $route->supportsHttpMethod("ANY")) {
                    call_user_func_array($route->getCallable(), array_values($route->getParams()));
                    $display404 = false;
                }
            }
        }
        
        if ($display404) {
            if (is_callable($this->errorHandler)) {
                call_user_func($this->errorHandler);
            } else {
                echo "404 - route not found";
            }
        }
    }
}

?>