<?php

namespace App\Enums;

enum ShipmentStatus: int
{
    case Pending = 1;
    case Completed = 2;
    case Failed = 3;
}