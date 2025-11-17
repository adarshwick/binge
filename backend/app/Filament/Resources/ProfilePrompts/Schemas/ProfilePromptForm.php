<?php

namespace App\Filament\Resources\ProfilePrompts\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class ProfilePromptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('text')->required()->maxLength(255),
                Toggle::make('active')->default(true),
            ]);
    }
}
