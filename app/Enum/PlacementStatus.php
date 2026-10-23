<?php
declare(strict_types = 1);

namespace App\Enum;

enum PlacementStatus : string
{
    case PENDING = "pending";
    case HIRED = "hired";
    case REJECTED = "rejected";
    case OFFER_ACCEPTED = "offer_accepted";
    case OFFER_DECLINED = "offer_declined";
    case INTERNSHIP = "internship";

    public function getLabel() : string
    {
        return match ($this)
        {
            self::PENDING => "Pending",
            self::HIRED => "Hired",
            self::REJECTED => "Rejected",
            self::OFFER_ACCEPTED => "Offer Accepted",
            self::OFFER_DECLINED => "Offer Declined",
            self::INTERNSHIP => "Internship"
        };
    }
}
