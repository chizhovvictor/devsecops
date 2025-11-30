<?php

declare(strict_types=1);

namespace App\Kernel\Exception;

class FileExtensionNotCorrectException extends FileException
{
    protected string $error = 'File extension not correct';
}
