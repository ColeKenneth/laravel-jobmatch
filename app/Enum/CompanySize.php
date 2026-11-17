<?php

namespace App\Enum;

enum CompanySize: string
{
    case UP_TO_FIVE_HUNDRED = '1-500';
    case MICRO = '1-10';
    case SMALL = '11-50';
    case UP_TO_FIFTY = '1-50';
    case MEDIUM = '51-200';
    case GROWING = '201-500';
    case LARGE = '501-1000';
    case ENTERPRISE = '1001-5000';
    case MAJOR_ENTERPRISE = '5001-10000';
    case GLOBAL_ENTERPRISE = '10000+';
    case ALL_LARGE = '500+';

    public function getLabel(): string
    {
        return match ($this) {
            self::ALL_LARGE => '500+',
            default => $this->value,
        };
    }

    public function matchingBuckets(): array
    {
        return match ($this) {
            self::UP_TO_FIVE_HUNDRED => ['1-500', '1-10', '11-50', '1-50', '51-200', '201-500'],
            self::UP_TO_FIFTY => ['1-50', '1-10', '11-50'],
            self::ALL_LARGE => ['500+', '501-1000', '1001-5000', '5001-10000', '10000+'],
            default => [$this->value],
        };
    }
}
