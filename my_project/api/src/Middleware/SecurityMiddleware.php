<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Kernel\Component\Request;
use App\Kernel\Component\Response;
use App\Kernel\Contract\MiddlewareInterface;
use App\Service\TokenDecoder;
use Closure;

class SecurityMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->cookies->get('access_token')) {
            return $next($request);
        }

        try {
            $payload = TokenDecoder::decode($request->cookies->get('access_token'));
            $request->request->set('security_user', (int)$payload['user_id'] ?? null);
        } catch (\Throwable $exception) {
            throw new \Exception('Invalid access token');
        }

        return $next($request);
    }
}
