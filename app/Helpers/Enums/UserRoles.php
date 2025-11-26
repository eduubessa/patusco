<?php

namespace App\Helpers\Enums;

enum UserRoles: string
{
    //
    case Admin = 'admin';
    case Customer = 'customer';
    case Receptionist = 'receptionist';
    case Doctor = 'doctor';
}
