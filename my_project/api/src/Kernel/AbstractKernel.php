<?php

declare(strict_types=1);

namespace App\Kernel;

use App\Kernel\Attribute\Middleware;
use App\Kernel\Component\Request;
use App\Kernel\Component\Response;
use App\Kernel\Component\RequestStack;
use App\Kernel\Exception\NotFoundHttpException;
use App\Kernel\Exception\CallableControllerException;

abstract class AbstractKernel
{
    private array $routes = [];

    public function __construct(
        protected RequestStack $requestStack,
        protected Container $container,
    ) {
    }

    abstract public function handle(Request $request): Response;

    public function autowiring(array $config = []): void
    {
        foreach ($config as $name => $class) {
            if (is_int($name)) {
                $this->container->set($class);
            } else {
                $this->container->set($name, $class);
            }
        }
    }
    
    public function loadRoutes(array $config = []): void
    {
        foreach ($config as $route) {
            $this->addRoute(
                $route['method'], 
                $route['path'], 
                $route['controller']
            );
        }
    }

    protected function addRoute(string $method, string $url, $target): void
    {
        $this->routes[$method][$url] = $target;
    }

    protected function getController(Request $request): array
    {
        $method = $request->method;
        $basePath = $request->basePath;
        $class = null;

        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $routeUrl => $target) {
                $pattern = preg_replace('/\{([a-zA-Z0-9_]+)}/', '([a-zA-Z0-9_\-\.=]+)', $routeUrl);
                $pattern = str_replace('/', '\/', $pattern);
                $pattern = '/^' . $pattern . '$/';

                if (preg_match($pattern, $basePath, $matches)) {
                    array_shift($matches);
                    $class = $target;
                    $request->params = $matches;
                    break;
                }
            }
        }

        if (!isset($class)) {
            throw new NotFoundHttpException(
                "Unable to find the controller for path {$request->basePath}.",
            );
        }

        if (is_string($class)) {
            $class = (array) $class;
            $class[1] = null;
        }

        return $class;
    }

    protected function newInstance(string $class): object
    {
        return $this->container->get($class, [], false);
    }

    protected function checkController(object $controller, ?string $method = null): object
    {
        $x = get_class($controller);

        if (!(is_object($controller) && $x !== 'stdClass')) {
            throw new CallableControllerException(
                "The controller ".get_class($controller)." is not callable."
            );
        }

        if (!$method && !is_callable($controller)) {
            throw new CallableControllerException(
                "The controller ".get_class($controller)." is not callable."
            );
        }

        if ($method && !method_exists($controller, $method)) {
            throw new CallableControllerException(
                "The controller ".get_class($controller)." does not have a method {$method}."
            );
        }

        return $controller;
    }

    protected function getMiddlewares(object $controller, ?string $method = null): array
    {
        $reflectionClass = new \ReflectionClass($controller::class);
        $method = $reflectionClass->getMethod($method ?: '__invoke');
        $middlewares = array_map(
            static function (\ReflectionAttribute $attribute) {
                if ($attribute->getName() !== Middleware::class) {
                    return;
                }

                if (
                    isset($attribute->getArguments()[0])
                    && count($attribute->getArguments()) === 1
                ) {
                    return $attribute->getArguments()[0];
                }

                return $attribute->getArguments()['class'] ?? null;
            },
            $method->getAttributes() 
        );
        
        return array_filter($middlewares);
    }
}
