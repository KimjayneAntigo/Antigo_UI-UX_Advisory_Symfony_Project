<?php

namespace App\Enum;

enum PaymentMethod: string
{
    case GCASH = 'GCash';
    case BANK_TRANSFER = 'Bank Transfer';
    case CASH = 'Cash';
    case CARD = 'Card';
}
