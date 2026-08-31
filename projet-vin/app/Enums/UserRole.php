<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Stockkeeper = 'stockkeeper';
    case Seller = 'seller';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrateur',
            self::Stockkeeper => 'Magasinier',
            self::Seller => 'Vendeur',
        };
    }
}
