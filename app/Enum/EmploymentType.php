<?php
declare(strict_types = 1);

namespace App\Enum;

enum EmploymentType : string
{
    case FULL_TIME = "full_time";
    case PART_TIME = "part_time";
    case INTERNSHIP = "internship";
    case CONTRACT = "contract";
    case FREELANCE = "freelance";

    public function getLabel() : string
    {
        return match ($this)
        {
            self::FULL_TIME => "Full Time",
            self::PART_TIME => "Part Time",
            self::INTERNSHIP => "Internship",
            self::CONTRACT => "Contract",
            self::FREELANCE => "Freelance"
        };
    }
}
