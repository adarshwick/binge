<?php

namespace App\Filament\Resources\ProfilePrompts\Pages;

use App\Filament\Resources\ProfilePrompts\ProfilePromptResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProfilePrompts extends ListRecords
{
    protected static string $resource = ProfilePromptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
