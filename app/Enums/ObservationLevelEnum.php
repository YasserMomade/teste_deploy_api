<?php

namespace App\Enums;


enum ObservationLevelEnum: string
{
    case GOOD = 'good';
    case MEDIUM = 'medium';
    case BAD = 'bad';
    case CRITICAL = 'critical';


    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::GOOD => 'bom',
            self::MEDIUM => 'medio',
            self::BAD => 'mau',
            self::CRITICAL => 'critico'
        };
    }

}