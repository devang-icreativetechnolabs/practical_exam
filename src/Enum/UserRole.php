<?php

namespace App\Enum;

enum UserRole: string
{
    case ADMIN = '1';
    case USER = '2';
    case MANAGER = '3';

    public static function getRoles(): array
    {
        return array_column(self::cases(), 'value');
    }
}
