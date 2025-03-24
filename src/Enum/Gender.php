<?php

namespace App\Enum;

enum Gender: string
{
    case MALE = '1';
    case FEMALE = '2';
    case OTHERS = '3';

    public static function getRoles(): array
    {
        return array_column(self::cases(), 'value');
    }
}
