<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending     = 'pending';
    case Confirmed   = 'confirmed';
    case Preparing   = 'preparing';
    case Ready       = 'ready';
    case Delivering  = 'delivering';
    case Completed   = 'completed';
    case Cancelled   = 'cancelled';
}
