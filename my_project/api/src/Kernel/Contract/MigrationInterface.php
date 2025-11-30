<?php

declare(strict_types=1);

namespace App\Kernel\Contract;

interface MigrationInterface
{
    public function up(): string;

    public function down(): string;
}