<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Kernel\Component\Request;
use App\Kernel\Component\Response;
use App\Kernel\Contract\MiddlewareInterface;
use App\Model\User;
use Closure;
use App\Service\TokenDecoder;

class RedirectToMain implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->cookies->get('access_token')) {
            return $next($request);
        }

        try {
            $payload = TokenDecoder::decode($request->cookies->get('access_token'));
            $securityUserId = $payload['user_id'] ?? null;

            if (!$securityUserId || !$this->isTokenValid($payload)) {
                return $next($request);
            }
            if (!User::find($securityUserId)) {
                return $next($request);
            }

            $this->onRedirectToMain();
        } catch (\Throwable $exception) {
            throw new \Exception('Invalid access token');
        }

        return $next($request);
    }

    private function isTokenValid(array $payload): bool
    {
        $expireAt = $payload['expire_at']['date'] ?? null;
        if (!$expireAt) {
            return false;
        }

        return new \DateTime() < new \DateTime($expireAt);
    }

    private function onRedirectToMain()
    {
        header("Location: /");
        exit;
    }
}
