<?php

namespace App\Domain\Enum;

enum TransactionStatus: string
{
    case ERROR = 'REPROVED';
    case APROVED = 'APROVED';
    case PROCESSING = 'PROCESSING';
}
