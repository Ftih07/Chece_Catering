<?php

namespace App\Filament\Resources\MenuAddonResource\Pages;

use App\Filament\Resources\MenuAddonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMenuAddon extends EditRecord
{
    protected static string $resource = MenuAddonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
