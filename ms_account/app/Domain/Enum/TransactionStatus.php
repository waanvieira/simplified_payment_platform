<?php

namespace App\Domain\Enum;

enum TransactionStatus: string
{
    case ERROR = 'ERROR';
    case APROVED = 'APROVED';
    case PROCESSING = 'PROCESSING';
}
