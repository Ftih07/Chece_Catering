<?php

namespace App\Filament\Resources\MenuVariantResource\Pages;

use App\Filament\Resources\MenuVariantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMenuVariant extends EditRecord
{
    protected static string $resource = MenuVariantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
