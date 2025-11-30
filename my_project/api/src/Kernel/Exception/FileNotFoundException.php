<?php

declare(strict_types=1);

namespace App\Kernel\Exception;

class FileNotFoundException extends FileException
{
    protected string $error = 'File not found';
}
