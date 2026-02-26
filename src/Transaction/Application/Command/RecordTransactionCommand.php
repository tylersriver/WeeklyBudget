<?php

declare(strict_types=1);

namespace App\Transaction\Application\Command;

readonly class RecordTransactionCommand
{
    public function __construct(
        public string $type,
        public string $description,
        public string $amount,
        public string $date,
        public int $userId,
    ) {
    }
}
