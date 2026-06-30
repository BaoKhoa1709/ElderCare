<?php

namespace App\Enums;

enum Role: string
{
    case SEEKER = 'SEEKER';
    case GIVER = 'GIVER';
    case ADMIN = 'ADMIN';
    case USER = 'USER';
}
