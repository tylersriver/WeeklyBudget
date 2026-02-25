<?php

declare(strict_types=1);

namespace App\Transaction\Domain;

enum TransactionType: string
{
    case Food      = 'Food';
    case Groceries = 'Groceries';
    case Gas       = 'Gas';
    case Shopping  = 'Shopping';
    case Other     = 'Other';
}
