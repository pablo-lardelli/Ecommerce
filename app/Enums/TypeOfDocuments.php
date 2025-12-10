<?php

namespace App\Enums;

enum TypeOfDocuments:int
{
    case DNI = 1;
    case CE = 2;
    case RUC = 3;
    case PP = 4;
    case LE = 5;
    case ID = 6;
}