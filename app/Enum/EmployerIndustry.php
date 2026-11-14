<?php

namespace App\Enum;

enum EmployerIndustry: string
{
    case GOVERNMENT = 'Government';
    case BPO = 'BPO & Call Center';
    case TECHNOLOGY = 'IT & Software';
    case HEALTHCARE = 'Healthcare';
    case EDUCATION = 'Education';
    case FINANCE = 'Banking & Finance';
    case RETAIL = 'Retail & E-commerce';
    case HOSPITALITY = 'Hospitality & Tourism';
    case MANUFACTURING = 'Manufacturing';
    case ENGINEERING = 'Construction & Engineering';
    case LOGISTICS = 'Logistics & Transportation';
    case TELECOM = 'Telecommunications';
    case REAL_ESTATE = 'Real Estate';
    case PROFESSIONAL = 'Professional Services';
    case NONPROFIT = 'Nonprofit & Community';

    public function aliases(): array
    {
        return match ($this) {
            self::GOVERNMENT => [$this->value, 'Public Sector', 'Government & Public Sector'],
            self::BPO => [$this->value, 'BPO', 'Call Center', 'Call Centre', 'Business Process Outsourcing', 'Customer Service'],
            self::TECHNOLOGY => [$this->value, 'Information Technology', 'Software Development', 'IT', 'Technology'],
            self::HEALTHCARE => [$this->value, 'Medicine', 'Hospital', 'Health Care'],
            self::FINANCE => [$this->value, 'Banking', 'Finance', 'Financial Services'],
            self::LOGISTICS => [$this->value, 'Logistics', 'Transportation'],
            self::HOSPITALITY => [$this->value, 'Hospitality', 'Tourism', 'Hotel'],
            self::ENGINEERING => [$this->value, 'Construction', 'Engineering'],
            self::RETAIL => [$this->value, 'Retail', 'E-commerce'],
            self::NONPROFIT => [$this->value, 'Nonprofit', 'Non-profit', 'NGO'],
            default => [$this->value],
        };
    }
}
