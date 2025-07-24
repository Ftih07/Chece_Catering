<?php

namespace App\Filament\Resources\MenuSubcategoryResource\Pages;

use App\Filament\Resources\MenuSubcategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMenuSubcategories extends ListRecords
{
    protected static string $resource = MenuSubcategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
