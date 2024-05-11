<?php

namespace App\Domain\Enum;

enum TransactionType: string
{
    case TRANSFER = "TRANSFER";
    case PURCHASE = "PURCHASE";
    case PAYMENT = "PAYMENT";
}
