<?php

declare(strict_types=1);

namespace App\Estimator\Domain;

enum ExpenseCategory: string
{
    case Bills    = 'Bills';
    case Savings  = 'Savings';
    case Spending = 'Spending';
}
