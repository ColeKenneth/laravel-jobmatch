<?php
declare(strict_types = 1);

namespace App\Enum;

enum Category : string
{
    case TECHNICAL = "technical";
    case SOFT = "soft";
    case LANGUAGE = "language";
    case TOOL = "tool";

    public function getLabel() : string
    {
        return match ($this)
        {
            self::TECHNICAL => "Technical",
            self::SOFT => "Soft",
            self::LANGUAGE => "Language",
            self::TOOL => "Tool"
        };
    }
}