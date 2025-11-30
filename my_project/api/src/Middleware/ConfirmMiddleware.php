<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Kernel\Component\JsonResponse;
use App\Kernel\Component\Request;
use App\Kernel\Component\Response;
use App\Kernel\Contract\MiddlewareInterface;
use App\Model\User;
use Closure;

class ConfirmMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Response
    {
        $fetchHeader = $request->headers->get('X_REQUESTED_WITH');
        $redirect = $fetchHeader !== 'XMLHttpRequest';

        $securityUser = $request->request->get('security_user');
        if (!$securityUser) {
            return $next($request);
        }

        $user = User::find($securityUser);
        if (!$user) {
            return $next($request);
        }
        if (!$user->isConfirmed()) {
            return $this->onConfirmationFailure($redirect);
        }

        return $next($request);
    }

    private function onConfirmationFailure(bool $redirect): JsonResponse
    {
        $data = ['message' => 'Confirmation failed'];

        if ($redirect) {
            header("Location: /confirm");
            exit;
        }

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
