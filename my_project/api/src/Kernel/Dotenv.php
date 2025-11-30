<?php

declare(strict_types=1);

namespace App\Kernel;

class Dotenv
{
    public function load(string $path): void
    {
        if (file_exists($path . '/.env')) {
            $lines = file($path . '/.env');
            if ($lines === false) {
                throw new \RuntimeException('Env file not found.');
            }

            foreach ($lines as $line) {
                if (!trim($line) || str_starts_with(trim($line), '#')) {
                    continue;
                }

                [$key, $value] = array_map('trim', explode('=', $line, 2));

                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }

        foreach ($_SERVER as $key => $value) {
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $value;
            }
        }
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        $value = getenv($key);
        return $value !== false ? $value : $default;
    }
}
