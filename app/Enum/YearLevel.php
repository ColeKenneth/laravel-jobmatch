<?php
declare(strict_types = 1);

namespace App\Enum;

enum YearLevel : string
{
    case FOURTH_YEAR = "fourth_year";
    case ALUMNI = "alumni";

    public function getLabel() : string
    {
        return match ($this)
        {
            self::FOURTH_YEAR => "Fourth Year",
            self::ALUMNI => "Alumni",
        };
    }
}
