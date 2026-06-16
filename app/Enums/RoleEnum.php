<?php

namespace App\Enums;

enum RoleEnum: string
{
    case Admin = 'admin';
    case Manager = 'Manager';
    case Expedidor = 'expedidor';
    case Contabilista = 'contabilista';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::Manager => 'Gerente',
            self::Expedidor => 'Expedidor',
            self::Contabilista => 'Contabilista',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
