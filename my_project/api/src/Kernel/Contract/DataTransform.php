<?php

declare(strict_types=1);

namespace App\Kernel\Contract;

use App\Kernel\Component\Request;

interface DataTransform
{
    public function transform($object, Request $request, array $context = []): object;
}
