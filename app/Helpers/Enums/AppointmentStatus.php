<?php

namespace App\Helpers\Enums;

enum AppointmentStatus: string
{
    case Scheduled = 'scheduled';
    case Pending = 'pending';
    case Approved = 'approved';
    case Cancelled = 'cancelled';
    case NoShow = 'no-show';

    public function color(): string
    {
        return match ($this) {
            self::Scheduled => 'blue',
            self::Pending => 'yellow',
            self::Cancelled => 'red',
            self::NoShow => 'green',
        };
    }
}
