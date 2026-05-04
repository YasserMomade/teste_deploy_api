<?php

namespace App\Enums;

enum RoleEnum: string
{
    case Admin = 'admin';
    case Manager = 'Manager';
    


    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::Manager => 'Gerente',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}