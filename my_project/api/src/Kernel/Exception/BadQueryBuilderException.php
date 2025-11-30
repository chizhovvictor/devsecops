<?php

declare(strict_types=1);

namespace App\Kernel\Exception;

class BadQueryBuilderException extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 500);
    }
}
