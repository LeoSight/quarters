<?php

namespace App\Enum;

enum NotificationTypes: int
{
    case INFO = 1;
    case WARNING = 2;
    case ALERT = 3;
}