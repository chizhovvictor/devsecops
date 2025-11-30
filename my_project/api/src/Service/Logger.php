<?php

declare(strict_types=1);

namespace App\Service;

use App\Enum\Logger\Level;
use App\Model\Log;

class Logger
{
    public static function error(string $message, array $context = []): Log
    {
        $log = new Log();
        $log->setLevel(Level::ERROR);
        $log->setMessage($message);
        $log->setContext($context);
        $log->save();

        return $log;
    }
}