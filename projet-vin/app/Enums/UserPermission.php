<?php

namespace App\Enums;

enum UserPermission: string
{
    case ManageUsers = 'manage users';
    case ManageSettings = 'manage settings';
    case ManageProducts = 'manage products';
    case RecordStockEntries = 'record stock entries';
    case RecordStockExits = 'record stock exits';
    case ManageInventories = 'manage inventories';
    case ViewInventories = 'view inventories';
    case ViewMovements = 'view movements';
}
