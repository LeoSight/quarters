<?php

namespace App\Enum;

enum FieldTypes: int
{
    case EMPTY = 0;
    case WATER = 1;
    case FOREST = 2;
    case MOUNTAIN = 3;
}