<?php

declare(strict_types=1);

namespace App\Kernel\Exception;

class BadRequestHttpException extends HttpException
{
    public function __construct(
        string $message = "",
        \Throwable $previous = null,
    ) {
        parent::__construct($message, 400, $previous);
    }
}
