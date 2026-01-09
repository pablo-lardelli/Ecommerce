<?php

namespace App\Enums;

enum OrderStatus: int
{
    //creo un case con indice 0 para ordenes creadas que no se pagan?o que po defecto sean 5
    case Pending = 1;
    case Processing = 2;
    case Shipped = 3;
    case Completed = 4;
    case Failed = 5;
    case Refunded = 6;
    case Cancelled = 7;
}