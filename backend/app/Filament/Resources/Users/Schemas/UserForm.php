<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),
                TextInput::make('email')->required()->email(),
                TextInput::make('phone'),
                Toggle::make('banned')->label('Banned'),
                DatePicker::make('dob'),
                TextInput::make('gender'),
                Textarea::make('bio'),
            ]);
    }
}
