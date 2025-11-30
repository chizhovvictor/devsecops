<?php

declare(strict_types=1);

namespace App\Enum\Logger;

enum Level: string
{
    case INFO = 'info';
    case ERROR = 'error';
}