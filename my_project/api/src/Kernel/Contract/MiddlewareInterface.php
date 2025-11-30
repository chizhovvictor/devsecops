<?php

declare(strict_types= 1);

namespace App\Kernel\Contract;

use Closure;
use App\Kernel\Component\Request;
use App\Kernel\Component\Response;

interface MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Response;
}
