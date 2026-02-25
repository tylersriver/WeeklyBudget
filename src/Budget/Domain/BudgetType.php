<?php

declare(strict_types=1);

namespace App\Budget\Domain;

enum BudgetType: string
{
    case Weekly  = 'weekly';
    case Monthly = 'monthly';
}
