<?php

declare(strict_types=1);

namespace App\Enum;

enum BudgetType: string
{
    case Weekly  = 'weekly';
    case Monthly = 'monthly';
}
