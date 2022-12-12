<?php

namespace App\Enum;

enum SoldierRoles: int
{
    case CITIZEN = 0;
    case RIFLEMAN = 1;
    case MACHINE_GUNNER = 2;
    case MEDIC = 3;
    case SCOUT = 4;
}