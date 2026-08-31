<?php

namespace App\Enums;

enum StockMovementReason: string
{
    case Purchase = 'purchase';
    case Sale = 'sale';
    case Breakage = 'breakage';
    case Loss = 'loss';
    case InternalConsumption = 'internal_consumption';
    case Gift = 'gift';
    case Inventory = 'inventory';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Purchase => 'Approvisionnement',
            self::Sale => 'Vente',
            self::Breakage => 'Casse',
            self::Loss => 'Perte',
            self::InternalConsumption => 'Consommation interne',
            self::Gift => 'Cadeau',
            self::Inventory => 'Inventaire',
            self::Other => 'Autre',
        };
    }
}
