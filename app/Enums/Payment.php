<?php

namespace App\Enums;

enum Payment: string
{
    case ONLINE = 'ONLINE';
    case ON_SITE = 'ON_SITE';
}
