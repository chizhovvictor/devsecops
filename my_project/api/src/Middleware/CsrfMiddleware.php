<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Kernel\Component\JsonResponse;
use App\Kernel\Component\Request;
use App\Kernel\Component\Response;
use App\Kernel\Contract\MiddlewareInterface;
use Closure;

class CsrfMiddleware implements MiddlewareInterface
{

    public function handle(Request $request, Closure $next): Response
    {
        $fetchHeader = $request->headers->get('X_REQUESTED_WITH');
        $brake = $fetchHeader !== 'XMLHttpRequest';
        $csrf = $request->cookies->get('csrf');

        if (!$csrf || $csrf !== md5('csrf')) {
            return $this->onCsrfFailure($brake);
        }

        return $next($request);
    }

    private function onCsrfFailure(bool $brake = false): JsonResponse
    {
        if ($brake) {
            exit;
        }

        $data = ['message' => 'CSRF failed'];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }
}