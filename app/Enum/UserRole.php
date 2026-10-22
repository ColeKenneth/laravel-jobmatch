<?php
declare(strict_types = 1);

namespace App\Enum;

enum UserRole : string
{
    case STUDENT = "student";
    case ALUMNI = "alumni";
    case EMPLOYER = "employer";
    case ADMIN = "admin";
    case PLACEMENT_OFFICER = "placement_officer";

    public function getLabel() : string
    {
        return match ($this)
        {
            self::STUDENT => "Student",
            self::ALUMNI => "Alumni",
            self::EMPLOYER => "Employer",
            self::ADMIN => "Admin",
            self::PLACEMENT_OFFICER => "Placement Officer",
        };
    }
}
