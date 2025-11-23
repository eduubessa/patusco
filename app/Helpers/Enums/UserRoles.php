<?php

namespace App\Helpers\Enums;

enum UserRoles: string
{
    //
    case SuperAdmin = 'admin';
    case Customer = 'customer';
    case Receptionist = 'receptionist';
    case Veterinary = 'veterinary';
}
