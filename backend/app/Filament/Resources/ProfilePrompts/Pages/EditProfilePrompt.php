<?php

namespace App\Filament\Resources\ProfilePrompts\Pages;

use App\Filament\Resources\ProfilePrompts\ProfilePromptResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProfilePrompt extends EditRecord
{
    protected static string $resource = ProfilePromptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
