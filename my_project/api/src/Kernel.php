<?php

declare(strict_types=1);

namespace App;

use App\Kernel\Pipeline;
use App\Kernel\AbstractKernel;
use App\Kernel\Component\Request;
use App\Kernel\Component\Response;
use App\Kernel\Exception\BadResponseException;

class Kernel extends AbstractKernel
{
    public function handle(Request $request): Response
    {
        $this->requestStack->push($request);
        try {
            return $this->handleRequest($request);
        } catch (\Throwable $e) {
            return $this->handleThrowable($e, $request);
        } finally {
            $this->requestStack->pop();
        }
    }

    private function handleRequest(Request $request): Response
    {
        [$class, $method] = $this->getController($request);
        $instance = $this->newInstance($class);
        $controller = $this->checkController($instance, $method);

        $pipes = array_merge(
            $this->getMiddlewares($controller, $method),
            [$method ? [$controller, $method] : $controller],
        );

        $response = Pipeline::send($request)
            ->through($pipes)
            ->thenReturn()
        ;

        if (!$response instanceof Response) {
            $name = get_class($controller);
            $type = gettype($response);

            throw new BadResponseException(
                "The {$name} must return a App\Kernel\Component\Response object but it returned {$type}."
            );
        }

        return $response;
    }

    private function handleThrowable(\Throwable $exception, Request $request): Response
    {
        $message = $exception->getMessage().'</br>';
        $message .= $exception->getFile().':'.$exception->getLine().'</br>';
        $message .= $exception->getTraceAsString();

        return new Response(
            $message, 
            $exception->getCode()
        );
    }
}
